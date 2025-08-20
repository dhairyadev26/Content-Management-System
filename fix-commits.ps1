# This script rewrites the Git history to use the correct author name
# Save the old and new email values
$OLD_EMAIL="Your Name <your.email@example.com>"
$CORRECT_NAME="dhairyadev26"
$CORRECT_EMAIL="your.email@example.com"

# Set up the filter branch environment variable to make the command more efficient
$env:FILTER_BRANCH_SQUELCH_WARNING = 1

# Run the git filter-branch command
git filter-branch --env-filter "
    if [ `"`$GIT_COMMITTER_NAME`" = 'Your Name' ]
    then
        export GIT_COMMITTER_NAME=`"$CORRECT_NAME`"
        export GIT_COMMITTER_EMAIL=`"$CORRECT_EMAIL`"
    fi
    if [ `"`$GIT_AUTHOR_NAME`" = 'Your Name' ]
    then
        export GIT_AUTHOR_NAME=`"$CORRECT_NAME`"
        export GIT_AUTHOR_EMAIL=`"$CORRECT_EMAIL`"
    fi
" --tag-name-filter cat -- --branches --tags

# Add a message to indicate completion
Write-Output "Git history has been rewritten with the correct author information."
