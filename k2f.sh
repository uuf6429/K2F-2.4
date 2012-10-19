ARGC="$#"
RETVAL=0
GITURL="https://github.com/uuf6429/K2F-2.4.git"
BRANCH="master"
SET PROGNM="$0"
SET COMMND="$1"
SET PHP="php"

_prompt(){
	read -p "K2F>" EXEC
	$EXEC
	echo ""
	_prompt
}

if [ "$ARGC" = "0" ]; then _prompt

case $COMMND in
	build)
		$PHP tools/build.php
		;;
	test)
		$PHP tools/test.php
		ECHO.
		;;
	init)
		git init
		git remote add origin $GITURL
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
		RETVAL=1
esac

exit $RETVAL