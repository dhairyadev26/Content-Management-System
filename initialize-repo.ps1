# Initialize Git Repository and Create Initial Commit
# This script sets up the Git repository and makes the first commit

# Configuration
$repoPath = "c:\demoapp\SmartphoneDataset-Anomaly-Detection"
$pluginPath = "$repoPath\advanced-bulk-content-management"
$initialDate = "2025-06-01T08:00:00"

# Make sure we're in the right directory
Set-Location $repoPath

# Check if git repository already exists
if (Test-Path "$repoPath\.git") {
    Write-Host "WARNING: Git repository already exists. This script is intended for initial setup only."
    $continue = Read-Host "Do you want to continue anyway? This will reset your repository. (y/n)"
    if ($continue -ne "y") {
        exit
    }
    
    # Remove existing git repository
    Remove-Item -Path "$repoPath\.git" -Recurse -Force
    Write-Host "Removed existing Git repository."
}

# Initialize new Git repository
Write-Host "Initializing Git repository..."
git init
git config user.name "Your Name"
git config user.email "your.email@example.com"

# Add all existing files for the initial commit
git add .

# Create the initial commit with backdated timestamp
$commitCommand = "git commit -m `"Initial commit of Advanced Bulk Content Management plugin`" --date=`"$initialDate`""
Write-Host "Executing: $commitCommand"
Invoke-Expression $commitCommand

Write-Host "`nInitial commit created successfully with date: $initialDate"
Write-Host "Now you can run create-commits.ps1 to generate the remaining commits."
