@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION

SET ARGC=0
FOR %%x IN (%*) DO SET /A ARGC+=1
IF "%ARGC%" EQU "0" GOTO PROMPT

SET RETVAL=0
SET GITURL="https://github.com/uuf6429/K2F-2.4.git"
SET BRANCH="master"
SET PROGNM=%~nx0
SET COMMND=%1%
CALL :UCase COMMND COMMND
SET PHP="php"
IF DEFINED ProgramFiles(x86) (SET BITNESS=64) else (SET BITNESS=32)
SET ANSICON="tests\libs\ansicon\ansicon%BITNESS%.exe"

REM "CALL git" is used because of: http://stackoverflow.com/q/5401229/314056

FOR %%L IN (BUILD TEST INIT PULL PUSH) DO IF "%COMMND%" EQU "%%L" GOTO :CASE_%COMMND%
GOTO CASE_DEFAULT
	:CASE_BUILD
		%ANSICON% %PHP% tools/build.php
		ECHO.
		GOTO END_SWITCH
	:CASE_TEST
		%ANSICON% %PHP% tools/test.php
		ECHO.
		GOTO END_SWITCH
	:CASE_INIT
		CALL git init
		CALL git remote add origin %GITURL%
		GOTO CASE_PULL
	:CASE_PULL
		CALL git pull -u origin %BRANCH%
		GOTO END_SWITCH
	:CASE_PUSH
		CALL git add -A
		SET /P MSG=Commit Message: 
		CALL git commit -m "%MSG%"
		CALL git push -u origin %BRANCH%
		GOTO END_SWITCH
	:CASE_DEFAULT
		ECHO Usage: %PROGNM% COMMAND [options]
		ECHO.
		ECHO List of Commands:
		ECHO.
		ECHO build  Generates a new build from source
		ECHO test   Runs the tests and shows results
		ECHO init   Install any dependencies and set up Git
		ECHO pull   Get any updates from Git VCS system
		ECHO push   Send any changes to Git VCS system
		ECHO.
		ECHO List of Options:
		ECHO /b     Change Git branch (defaults to "master")
		ECHO.
		SET RETVAL=1
		GOTO END_SWITCH

:LCase
:UCase
	SET _UCase=A B C D E F G H I J K L M N O P Q R S T U V W X Y Z
	SET _LCase=a b c d e f g h i j k l m n o p q r s t u v w x y z
	SET _Lib_UCase_Tmp=!%1!
	IF /I "%0"==":UCase" SET _Abet=%_UCase%
	IF /I "%0"==":LCase" SET _Abet=%_LCase%
	FOR %%Z IN (%_Abet%) DO SET _Lib_UCase_Tmp=!_Lib_UCase_Tmp:%%Z=%%Z!
	SET %2=%_Lib_UCase_Tmp%
	GOTO :EOF

:PROMPT
	SET /P EXEC=K2F^>
	CALL %EXEC%
	ECHO.
	GOTO :PROMPT

:END_SWITCH
EXIT %RETVAL%