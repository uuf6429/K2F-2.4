@ECHO OFF
If Defined ProgramFiles(x86) (SET BITNESS=64) else (SET BITNESS=32)
SET ANSICON="tests\libs\ansicon\ansicon%BITNESS%.exe"
%ANSICON% php tests/index.php
ECHO.
PAUSE