#!/bin/bash

# Script to create a zip file of the WP_CrowdFundTime plugin
# This script should be run from the plugin's root directory

# Set variables
PLUGIN_NAME="wp-crowdfundtime"
VERSION=$(grep "Version:" wp-crowdfundtime.php | awk -F': ' '{print $2}' | tr -d '\r')
ZIP_NAME="${PLUGIN_NAME}-${VERSION}.zip"

# Create a temporary directory
TMP_DIR=$(mktemp -d)
echo "Creating temporary directory: $TMP_DIR"

# Copy all files to the temporary directory
echo "Copying plugin files..."
cp -R ./* "$TMP_DIR"

# Remove any development or unnecessary files
echo "Removing unnecessary files..."
rm -rf "$TMP_DIR/create-zip.sh"
rm -rf "$TMP_DIR/.git"
rm -rf "$TMP_DIR/.gitignore"
rm -rf "$TMP_DIR/node_modules"
rm -rf "$TMP_DIR/package-lock.json"
rm -rf "$TMP_DIR/.DS_Store"
find "$TMP_DIR" -name ".DS_Store" -delete

# Create the zip file
echo "Creating zip file: $ZIP_NAME"
cd "$TMP_DIR"
zip -r "$ZIP_NAME" ./*
cd - > /dev/null

# Move the zip file to the current directory
echo "Moving zip file to current directory..."
mv "$TMP_DIR/$ZIP_NAME" .

# Clean up
echo "Cleaning up..."
rm -rf "$TMP_DIR"

echo "Done! Created $ZIP_NAME"
