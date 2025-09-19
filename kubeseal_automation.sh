#!/bin/bash

# This bash script automates the process of sealing Kubernetes secrets using kubeseal.

set -x

if [ -z "$1" ]; then
    ENV_FILE_CONTENT_BASE64=""
else
    ENV_FILE_CONTENT_BASE64="$1"
fi

echo "OSTYPE variable: $OSTYPE"

# Method 3: Check for specific OS
if [[ "$OSTYPE" == "darwin"* ]]; then
    echo "✓ Running on macOS"
    OS_TYPE="macos"
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    echo "✓ Running on Linux"
    OS_TYPE="linux"

    # Check if it's Ubuntu specifically
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        echo "Distribution: $NAME"
        if [[ "$NAME" == *"Ubuntu"* ]]; then
            echo "✓ Detected Ubuntu Linux"
            OS_TYPE="ubuntu"
        fi
    fi
else
    echo "❌ Unsupported or unknown operating system: $OSTYPE"
    exit 1
fi
echo "Final OS detection: $OS_TYPE"

# Check if kubeseal binary is installed
if [[ "$OS_TYPE" == "macos" ]]; then

    if ! command -v kubeseal &> /dev/null; then
        echo "Error: kubeseal binary is not installed!"
        echo "Please install kubeseal from: https://github.com/bitnami-labs/sealed-secrets"
        echo "Or run: brew install kubeseal"
        exit 1
    fi

elif [[ "$OS_TYPE" == "ubuntu" ]]; then
    echo "Running on Ubuntu Linux"
    if ! command -v kubeseal &> /dev/null; then
        echo "Error: kubeseal binary is not installed!"
        echo "Please install kubeseal from: https://github.com/bitnami-labs/sealed-secrets"
        echo "Or run: sudo apt-get install kubeseal"
        exit 1
    fi
fi
# Add more OS checks if needed

echo "kubeseal binary found: $(which kubeseal)"
echo "kubeseal version: $(kubeseal --version)"

# Install yq if not present
if ! command -v yq &> /dev/null; then
    echo "yq not found, installing..."
    if [[ "$OS_TYPE" == "macos" ]]; then
        brew install yq
    elif [[ "$OS_TYPE" == "ubuntu" ]]; then
        sudo apt-get install -y yq
    fi
fi

if ! command -v yq &> /dev/null; then
    echo "Error: yq is not installed!"
    exit 1
fi

echo $ENV_FILE_CONTENT_BASE64 | base64 -d > temp.txt
ENV_FILE_CONTENT_BASE64_DECODED_FILE="temp.txt"
#"/Users/balinderwalia/Documents/Work/aws_keys/.env_wsl_prod"

SEALED_SECRET_INPUT_PATH="secret_wsl_prod_template.yaml"
SEALED_SECRET_OUTPUT_PATH="secret_wsl_prod.yaml"

if [ ! -f "$ENV_FILE_CONTENT_BASE64_DECODED_FILE" ]; then
    echo "Error: Environment file '$ENV_FILE_CONTENT_BASE64_DECODED_FILE' not found!"
    exit 1
fi

if [ ! -f "$SEALED_SECRET_INPUT_PATH" ]; then
    echo "Error: Sealed secret template file '$SEALED_SECRET_INPUT_PATH' not found!"
    exit 1
fi

if base64 --help 2>&1 | grep -q -- '--wrap'; then
    # GNU base64 (Linux)
    echo "Using GNU base64 (Linux)"
    BASE64_WRAP_OPTION="--wrap=0"
else
    # BSD base64 (macOS)
    echo "Using BSD base64 (macOS)"
    BASE64_WRAP_OPTION="-b 0"
fi

#   cat temp.txt 

# rm temp.txt

rm -Rf $SEALED_SECRET_OUTPUT_PATH
cp $SEALED_SECRET_INPUT_PATH $SEALED_SECRET_OUTPUT_PATH

sed -i '' "s/WSL_ENV_FILE_PLACEHOLDER_BASE64_PROD/$ENV_FILE_CONTENT_BASE64/g" $SEALED_SECRET_OUTPUT_PATH

if [ ! -f "$SEALED_SECRET_OUTPUT_PATH" ]; then
    echo "Error: Sealed secret output file '$SEALED_SECRET_OUTPUT_PATH' not found!"
    exit 1
fi

# cat $SEALED_SECRET_OUTPUT_PATH

echo "Sealing the secret using kubeseal..."
kubeseal --format yaml < $SEALED_SECRET_OUTPUT_PATH > sealed_secret_wsl_prod.yaml

# rm -Rf $SEALED_SECRET_OUTPUT_PATH
# cat sealed_secret_wsl_prod.yaml
echo "Sealed secret created at 'sealed_secret_wsl_prod.yaml'"

# extract the sealed secret env_file
echo "Extracting sealed secret env_file encrypted value..."

if ! command -v yq &> /dev/null; then
    echo "Error: yq is not installed!"
    exit 1
fi

yq .spec.encryptedData.env_file sealed_secret_wsl_prod.yaml > sealed_env_file_base64_wsl_prod.txt
echo "Sealed env_file base64 content saved to 'sealed_env_file_base64_wsl_prod.txt'"
# cat sealed_env_file_base64_wsl_prod.txt

HELM_VALUES_INPUT_PATH=devops/webimpetus-chart/values-prod-k3s1_template.yaml
HELM_VALUES_OUTPUT_PATH=devops/webimpetus-chart/values-prod-k3s1.yaml

if [ ! -f "$HELM_VALUES_INPUT_PATH" ]; then
    echo "Error: Helm values template file '$HELM_VALUES_INPUT_PATH' not found!"
    exit 1
fi

cp $HELM_VALUES_INPUT_PATH $HELM_VALUES_OUTPUT_PATH

SAFE_SEALEDSECRET_ENCRYPTED=$(<sealed_env_file_base64_wsl_prod.txt)

# echo $SAFE_SEALEDSECRET_ENCRYPTED

if python3 --version &> /dev/null; then
    echo "Python3 is installed"
else
    echo "Error: Python3 is not installed!"
    exit 1
fi

# Use Python for reliable string replacement
python3 << EOF
import sys

# Read the file
with open('$HELM_VALUES_OUTPUT_PATH', 'r') as f:
    content = f.read()

# Replace the placeholder with the encrypted secret
content = content.replace('helmfilesecretsplaceholder', '$SAFE_SEALEDSECRET_ENCRYPTED')

# Write back to file
with open('$HELM_VALUES_OUTPUT_PATH', 'w') as f:
    f.write(content)

print("Successfully replaced placeholder with encrypted secret")
EOF

cat $HELM_VALUES_OUTPUT_PATH

echo "Helm values file created at '$HELM_VALUES_OUTPUT_PATH'"
# Clean up temporary files
rm -Rf $SEALED_SECRET_OUTPUT_PATH
rm -Rf sealed_secret_wsl_prod.yaml
rm -Rf temp.txt
rm -Rf sealed_env_file_base64_wsl_prod.txt


