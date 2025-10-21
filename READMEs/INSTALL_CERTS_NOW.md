# Install Production Certificates - Quick Guide

## 📋 Certificate Files

Your production certificate files:

```
Certificate:  /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.crt
Private Key:  /Users/balinderwalia/Documents/Work/aws_keys/workstation-ca.key
Chain (opt): /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert-chain.crt
```

## 🚀 Quick Installation (3 Steps)

### Step 1: Transfer from Mac to Linux

**On your Mac terminal**, run these exact commands:

```bash
# Transfer certificate
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.crt bwalia@172.20.0.1:~/.certs/

# Transfer private key
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-ca.key bwalia@172.20.0.1:~/.certs/

# Transfer chain (optional - if file exists)
scp /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert-chain.crt bwalia@172.20.0.1:~/.certs/ 2>/dev/null || echo "No chain file (OK)"
```

**Expected Output:**
```
workstation-cert.crt                          100% 1823   123KB/s   00:00
workstation-ca.key                            100% 1704   156KB/s   00:00
```

### Step 2: Verify Transfer

**On this Linux system** (where you are now), run:

```bash
ls -lh ~/.certs/
```

**Expected Output:**
```
-rw-r--r-- 1 bwalia bwalia 1.8K Oct 10 15:00 workstation-cert.crt
-rw------- 1 bwalia bwalia 1.7K Oct 10 15:00 workstation-ca.key
```

### Step 3: Install Certificates

**Still on Linux**, run:

```bash
cd /home/bwalia/workerra-ci
./install-production-certs.sh ~/.certs
```

**What This Does:**
- ✓ Verifies certificate and key match
- ✓ Displays certificate information
- ✓ Backs up temporary certificates
- ✓ Installs production certificates as:
  - nginx/ssl/dev000.workstation.co.uk.crt
  - nginx/ssl/dev000.workstation.co.uk.key
- ✓ Restarts nginx
- ✓ Tests HTTPS connection
- ✅ **Removes browser security warnings!**

## ✅ After Installation

Access your application at:
```
🔒 https://dev000.workstation.co.uk:8443/
```

**No more security warnings!** The certificate will be valid.

---

## 🔄 Alternative Method (If SCP Doesn't Work)

### Manual Copy-Paste

#### 1. Display Certificate on Mac

```bash
cat /Users/balinderwalia/Documents/Work/aws_keys/workstation-cert.crt
```

Copy the entire output (including `-----BEGIN CERTIFICATE-----` and `-----END CERTIFICATE-----`)

#### 2. Create Certificate File on Linux

```bash
cat > ~/.certs/workstation-cert.crt << 'EOF'
[Paste certificate content here]
EOF
```

#### 3. Display Private Key on Mac

```bash
cat /Users/balinderwalia/Documents/Work/aws_keys/workstation-ca.key
```

Copy the entire output (including `-----BEGIN PRIVATE KEY-----` and `-----END PRIVATE KEY-----`)

#### 4. Create Key File on Linux

```bash
cat > ~/.certs/workstation-ca.key << 'EOF'
[Paste key content here]
EOF

chmod 600 ~/.certs/workstation-ca.key
```

#### 5. Install

```bash
cd /home/bwalia/workerra-ci
./install-production-certs.sh ~/.certs
```

---

## 🧪 Verification After Install

```bash
# 1. Check certificate is installed
ls -lh /home/bwalia/workerra-ci/nginx/ssl/dev000.workstation.co.uk.*

# 2. Test HTTPS
curl -k https://dev000.workstation.co.uk:8443/health
# Should return: OK

# 3. Check certificate subject
echo | openssl s_client -connect dev000.workstation.co.uk:8443 -servername dev000.workstation.co.uk 2>/dev/null | openssl x509 -noout -subject
# Should show proper subject (not self-signed)

# 4. Open in browser
# https://dev000.workstation.co.uk:8443/
# Should load without security warning
```

---

## 📊 Quick Status Check

**Current Status:**
```
Domain:      dev000.workstation.co.uk
Port:        8443 (HTTPS)
Certificate: Temporary (self-signed)
Status:      ⏳ Waiting for production certificates
```

**After Installation:**
```
Domain:      dev000.workstation.co.uk
Port:        8443 (HTTPS)
Certificate: Production (valid)
Status:      ✅ Fully operational, no warnings
```

---

## 💡 Tips

- **Password Protected?** If your key file asks for a password during installation, you'll need to provide it
- **Wrong Format?** The script accepts .crt, .pem, or .cert formats
- **Chain File Missing?** That's OK - it's optional
- **Need Help?** Check [PRODUCTION_CERT_SETUP.md](PRODUCTION_CERT_SETUP.md) for detailed troubleshooting

---

## 🎯 What You'll Achieve

After installing production certificates:

1. ✅ Valid SSL certificate (no browser warnings)
2. ✅ Proper certificate subject matching your domain
3. ✅ Production-grade security
4. ✅ Professional appearance (green lock in browser)
5. ✅ Works on all browsers without warnings

---

## 📞 Need Help?

If SCP asks for a password and you don't know it, you may need to:
1. Set up SSH keys between Mac and Linux
2. Use the manual copy-paste method instead
3. Transfer files via USB drive

Run `./copy-certs-from-mac.sh` for more transfer options.

---

**Ready?** Run the commands above to install your production certificates!
