# Script to commit files one by one for the initial setup
# This script commits each file individually rather than all at once

# Configuration
$repoPath = "c:\demoapp\SmartphoneDataset-Anomaly-Detection"
$pluginPath = "$repoPath\advanced-bulk-content-management"
$initialDate = "2025-06-01T08:00:00"

# Make sure we're in the right directory
Set-Location $repoPath

# Function to commit a single file
function Commit-SingleFile {
    param (
        [string]$filePath,
        [string]$commitMessage,
        [string]$date
    )
    
    # Check if file exists
    if (-not (Test-Path $filePath)) {
        Write-Host "Warning: File $filePath does not exist. Skipping."
        return
    }
    
    # Add the file
    git add $filePath
    
    # Commit with backdated timestamp
    $commitCommand = "git commit -m `"$commitMessage`" --date=`"$date`""
    Write-Host "Executing: $commitCommand"
    Invoke-Expression $commitCommand
    
    # Add a small delay to ensure commits have different timestamps
    Start-Sleep -Seconds 1
}

# List of files to commit in order
$filesToCommit = @(
    # Main plugin file first
    @{
        path = "$pluginPath\advanced-bulk-content-management.php"
        message = "Add main plugin file with header"
    },
    
    # Core plugin structure
    @{
        path = "$pluginPath\README.md"
        message = "Add README file with plugin documentation"
    },
    @{
        path = "$pluginPath\uninstall.php"
        message = "Add uninstall script for clean removal"
    },
    
    # Admin page classes
    @{
        path = "$pluginPath\includes\class-admin-page.php"
        message = "Create admin page class for backend UI"
    },
    
    # Functionality classes
    @{
        path = "$pluginPath\includes\class-search-filter.php"
        message = "Add search filter functionality"
    },
    @{
        path = "$pluginPath\includes\class-bulk-actions.php"
        message = "Implement bulk action handlers"
    },
    @{
        path = "$pluginPath\includes\class-logger.php"
        message = "Create logging system for activity tracking"
    },
    @{
        path = "$pluginPath\includes\class-async-jobs.php"
        message = "Add asynchronous job processing"
    },
    @{
        path = "$pluginPath\includes\class-hooks.php"
        message = "Implement extensibility hooks system"
    },
    
    # JavaScript files
    @{
        path = "$pluginPath\assets\js\admin-script.js"
        message = "Add main admin interface JavaScript"
    },
    @{
        path = "$pluginPath\assets\js\search-filter.js"
        message = "Create search and filter JavaScript functionality"
    },
    @{
        path = "$pluginPath\assets\js\bulk-actions.js"
        message = "Implement bulk actions JavaScript"
    },
    @{
        path = "$pluginPath\assets\js\accessibility.js"
        message = "Add accessibility improvements for screen readers"
    },
    
    # CSS files
    @{
        path = "$pluginPath\assets\css\admin-style.css"
        message = "Add admin interface styling"
    },
    @{
        path = "$pluginPath\assets\css\search-filter.css"
        message = "Create search filter UI styles"
    },
    @{
        path = "$pluginPath\assets\css\bulk-actions.css"
        message = "Implement bulk actions UI styles"
    },
    
    # Languages
    @{
        path = "$pluginPath\languages\advanced-bulk-content-management.pot"
        message = "Add language template for translations"
    },
    
    # Scripts (commit these last)
    @{
        path = "$repoPath\initialize-repo.ps1"
        message = "Add repository initialization script"
    },
    @{
        path = "$repoPath\create-commits.ps1"
        message = "Add script for generating development history"
    }
)

# Commit each file one by one
$dateObj = Get-Date $initialDate
foreach ($file in $filesToCommit) {
    $currentDate = $dateObj.ToString("yyyy-MM-ddTHH:mm:ss")
    Commit-SingleFile -filePath $file.path -commitMessage $file.message -date $currentDate
    
    # Increment date by a small amount for each commit
    $dateObj = $dateObj.AddMinutes(30)
}

Write-Host "Completed committing files one by one with backdated timestamps."
Write-Host "Now you can run create-commits.ps1 to generate the remaining commits."
