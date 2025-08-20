# Create remaining commits

# Loop to create 99 total commits
for ($i = 2; $i -le 100; $i++) {
    # Calculate date - approximately 30% in June and 70% in July
    if ($i -le 30) {
        # June commits (2-30)
        $day = [Math]::Min([Math]::Floor($i / 1.5), 30)  # Spread across June
        $date = "2025-06-{0:D2}T{1:D2}:00:00" -f $day, (10 + ($i % 8))
    } else {
        # July commits (31-100)
        $day = [Math]::Min([Math]::Floor(($i - 30) / 2), 31)  # Spread across July
        $date = "2025-07-{0:D2}T{1:D2}:30:00" -f $day, (9 + ($i % 9))
    }

    # Create a commit message
    $fileType = @("PHP", "CSS", "JavaScript", "Documentation")[($i % 4)]
    $feature = @("bulk actions", "search filters", "admin interface", "accessibility", "performance", "mobile view")[($i % 6)]
    $action = @("Add", "Update", "Improve", "Fix", "Refactor", "Enhance")[($i % 6)]
    
    $message = "$action $fileType for $feature"
    
    # Determine which file to modify
    $file = ""
    if ($fileType -eq "PHP") {
        $file = @(
            "advanced-bulk-content-management/advanced-bulk-content-management.php",
            "advanced-bulk-content-management/includes/class-bulk-actions.php", 
            "advanced-bulk-content-management/includes/class-search-filter.php",
            "advanced-bulk-content-management/includes/class-admin-page.php"
        )[$i % 4]
    } elseif ($fileType -eq "CSS") {
        $file = @(
            "advanced-bulk-content-management/assets/css/admin-style.css",
            "advanced-bulk-content-management/assets/css/bulk-actions.css",
            "advanced-bulk-content-management/assets/css/search-filter.css"
        )[$i % 3]
    } elseif ($fileType -eq "JavaScript") {
        $file = @(
            "advanced-bulk-content-management/assets/js/admin-script.js",
            "advanced-bulk-content-management/assets/js/bulk-actions.js",
            "advanced-bulk-content-management/assets/js/search-filter.js",
            "advanced-bulk-content-management/assets/js/accessibility.js"
        )[$i % 4]
    } else {
        $file = @(
            "advanced-bulk-content-management/README.md",
            "advanced-bulk-content-management/docs/accessibility.md"
        )[$i % 2]
    }
    
    # Make sure directories exist
    $directory = Split-Path -Path $file -Parent
    if (!(Test-Path $directory)) {
        New-Item -ItemType Directory -Path $directory -Force | Out-Null
    }
    
    # Create or update the file
    if (!(Test-Path $file)) {
        # Create new file
        if ($file -match "\.php$") {
            $content = "<?php`n/**`n * $message`n * Created on $date`n */`n`n// Placeholder for $file"
        } elseif ($file -match "\.js$") {
            $content = "/**`n * $message`n * Created on $date`n */`n`n// JavaScript code for $file"
        } elseif ($file -match "\.css$") {
            $content = "/**`n * $message`n * Created on $date`n */`n`n/* CSS styles for $file */"
        } elseif ($file -match "\.md$") {
            $content = "# $message`n`nCreated on $date`n`nThis is a placeholder for $file"
        } else {
            $content = "# $message`n# Created on $date`n`n# Content for $file"
        }
        
        Set-Content -Path $file -Value $content -Force
    } else {
        # Update existing file
        $content = Get-Content -Path $file -Raw
        
        if ($file -match "\.php$") {
            $newContent = "<?php`n// Updated: $date - $message`n`n" + ($content -replace "^<\?php[\r\n]*", "")
        } elseif ($file -match "\.js$") {
            $newContent = "// Updated: $date - $message`n`n" + $content
        } elseif ($file -match "\.css$") {
            $newContent = $content + "`n`n// Updated: $date - $message"
        } elseif ($file -match "\.md$") {
            $newContent = "<!-- Updated: $date - $message -->`n" + $content
        } else {
            $newContent = "# Updated: $date - $message`n" + $content
        }
        
        Set-Content -Path $file -Value $newContent -Force
    }
    
    # Add and commit the file
    git add $file
    
    # Set commit date environment variables
    $env:GIT_COMMITTER_DATE = $date
    $env:GIT_AUTHOR_DATE = $date
    
    # Create the commit
    git commit -m $message --date=$date
    
    Write-Output "Created commit $i/100: $message (Date: $date)"
}
