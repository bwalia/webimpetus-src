#!/bin/bash

# Script to test SSL setup for dev000.workstation.co.uk
# Run with: ./test-ssl-setup.sh

set -e

echo "=========================================="
echo "  SSL Setup Testing Script"
echo "  Domain: dev000.workstation.co.uk:8443"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
PASSED=0
FAILED=0

# Function to test endpoint
test_endpoint() {
    local name="$1"
    local url="$2"
    local expected_code="$3"

    echo -n "Testing $name... "

    # Make request and get status code
    status_code=$(curl -k -s -o /dev/null -w "%{http_code}" "$url" 2>&1)

    if [ "$status_code" == "$expected_code" ]; then
        echo -e "${GREEN}✓ PASS${NC} (HTTP $status_code)"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Expected $expected_code, got $status_code)"
        ((FAILED++))
        return 1
    fi
}

# Function to test redirect
test_redirect() {
    local name="$1"
    local url="$2"

    echo -n "Testing $name... "

    # Get redirect location
    location=$(curl -s -I "$url" 2>&1 | grep -i "location:" | cut -d' ' -f2 | tr -d '\r')

    if [[ "$location" == https://* ]]; then
        echo -e "${GREEN}✓ PASS${NC} (Redirects to HTTPS)"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (No HTTPS redirect: $location)"
        ((FAILED++))
        return 1
    fi
}

# Function to test security header
test_security_header() {
    local header="$1"
    local url="https://dev000.workstation.co.uk:8443/"

    echo -n "Testing security header: $header... "

    # Check if header exists
    if curl -k -s -I "$url" 2>&1 | grep -qi "$header:"; then
        echo -e "${GREEN}✓ PASS${NC}"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}✗ FAIL${NC} (Header not found)"
        ((FAILED++))
        return 1
    fi
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 1. Testing DNS Resolution"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check hosts file
if grep -q "dev000.workstation.co.uk" /etc/hosts; then
    echo -e "${GREEN}✓ PASS${NC} - dev000.workstation.co.uk found in /etc/hosts"
    ((PASSED++))
else
    echo -e "${RED}✗ FAIL${NC} - dev000.workstation.co.uk NOT in /etc/hosts"
    echo "  Run: sudo ./setup-hosts.sh"
    ((FAILED++))
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 2. Testing Nginx Container"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check if nginx container is running
if docker ps | grep -q "workerra-ci-nginx"; then
    echo -e "${GREEN}✓ PASS${NC} - Nginx container is running"
    ((PASSED++))
else
    echo -e "${RED}✗ FAIL${NC} - Nginx container is NOT running"
    echo "  Run: docker-compose up -d nginx"
    ((FAILED++))
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 3. Testing SSL Certificate"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check if certificate files exist
if [ -f "nginx/ssl/dev000.workstation.co.uk.crt" ] && [ -f "nginx/ssl/dev000.workstation.co.uk.key" ]; then
    echo -e "${GREEN}✓ PASS${NC} - SSL certificate files exist"
    ((PASSED++))

    # Check certificate expiry
    expiry=$(openssl x509 -in nginx/ssl/dev000.workstation.co.uk.crt -noout -enddate | cut -d= -f2)
    echo "  Certificate expires: $expiry"
else
    echo -e "${RED}✗ FAIL${NC} - SSL certificate files missing"
    ((FAILED++))
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 4. Testing HTTPS Endpoints"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

test_endpoint "Health check" "https://dev000.workstation.co.uk:8443/health" "200"
test_endpoint "Main application" "https://dev000.workstation.co.uk:8443/" "302"
test_endpoint "Adminer" "https://dev000.workstation.co.uk:8443/adminer/" "200"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 5. Testing HTTP to HTTPS Redirect"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

test_redirect "HTTP redirect" "http://dev000.workstation.co.uk:8888/"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 6. Testing Security Headers"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

test_security_header "Strict-Transport-Security"
test_security_header "X-Frame-Options"
test_security_header "X-Content-Type-Options"
test_security_header "X-XSS-Protection"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " 7. Testing HTTP/2 Support"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo -n "Testing HTTP/2... "
if curl -k -s -I https://dev000.workstation.co.uk:8443/ 2>&1 | grep -q "HTTP/2"; then
    echo -e "${GREEN}✓ PASS${NC}"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠ WARN${NC} (HTTP/1.1 used)"
fi

echo ""
echo "=========================================="
echo " Test Results Summary"
echo "=========================================="
echo ""
echo -e "  ${GREEN}Passed:${NC} $PASSED"
echo -e "  ${RED}Failed:${NC} $FAILED"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All tests passed!${NC}"
    echo ""
    echo "Your SSL setup is working correctly."
    echo ""
    echo "Access your application at:"
    echo "  🔒 https://dev000.workstation.co.uk:8443/"
    echo ""
    echo "Note: You'll see a security warning in the browser"
    echo "because the certificate is self-signed. Click 'Advanced'"
    echo "and 'Proceed anyway' to access the application."
    exit 0
else
    echo -e "${RED}✗ Some tests failed!${NC}"
    echo ""
    echo "Please review the failures above and:"
    echo "  1. Check docker logs: docker logs workerra-ci-nginx"
    echo "  2. Verify services are running: docker-compose ps"
    echo "  3. Review configuration files in nginx/"
    exit 1
fi
