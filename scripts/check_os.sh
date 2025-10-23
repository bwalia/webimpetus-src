#!/bin/bash

# Script to detect operating system type (macOS or Ubuntu/Linux)

echo "Detecting operating system..."

# Method 1: Using uname
OS=$(uname -s)
echo "uname -s output: $OS"

# Method 2: Using OSTYPE environment variable
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

# Alternative method using uname only
echo ""
echo "Alternative detection using uname:"
case "$(uname -s)" in
    Darwin)
        echo "✓ macOS detected"
        ;;
    Linux)
        echo "✓ Linux detected"
        # Check for Ubuntu
        if command -v lsb_release &> /dev/null; then
            DISTRO=$(lsb_release -si)
            echo "Distribution: $DISTRO"
        elif [ -f /etc/os-release ]; then
            DISTRO=$(grep "^ID=" /etc/os-release | cut -d'=' -f2 | tr -d '"')
            echo "Distribution: $DISTRO"
        fi
        ;;
    *)
        echo "❌ Unknown OS"
        ;;
esac