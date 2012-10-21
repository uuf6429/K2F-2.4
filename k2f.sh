
# Set up some variables
ARGC="$#"
GITURL="https://github.com/uuf6429/K2F-2.4.git"
BRANCH="master"
PROGNM="$0"
COMMND="$1"
PHP="php"

# Parse command arguments
PARSE_NEXT_ARG(){
        if [ "$1" != "" ]; then
                if [ "$1" = "-b" ]; then
                        BRANCH=$2
                        shift
                fi
                shift
                eval "PARSE_NEXT_ARG $@"
        fi
}
eval "PARSE_NEXT_ARG $@"

# Dynamic prompt for when script is run directly
_prompt(){
	read -p "K2F>" EXEC
	$PROGNM $EXEC
	echo ""
	_prompt
}

# Main decision engine
_decide(){
	case $COMMND in
		build)
			$PHP "tools/build.php"
			echo ""
			;;
		test)
			$PHP "tools/test.php"
			echo ""
			;;
		init)
			git init
			git remote add origin $GITURL
			git pull -u origin $BRANCH
			;;
		pull)
			git pull -u origin $BRANCH
			;;
		push)
			git add -A
			read -p "Commit Message: " MSG
			git commit -m "$MSG"
			git push -u origin $BRANCH
			;;
		*)
			echo "Usage: $PROGNM COMMAND [options]"
			echo ""
			echo "List of Commands:"
			echo ""
			echo "build  Generates a new build from source"
			echo "test   Runs the tests and shows results"
			echo "init   Install any dependencies and set up Git"
			echo "pull   Get any updates from Git VCS system"
			echo "push   Send any changes to Git VCS system"
			echo ""
			echo "List of Options:"
			echo "-b     Change Git branch (defaults to \"master\")"
			echo ""
	esac
}

# Check for dynamic (prompt) mode
if [ "$ARGC" = "0" ]; then
	_prompt
else
	_decide
fi