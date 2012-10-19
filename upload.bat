@ECHO OFF

ECHO Work in progress.
PAUSE

git add -A
git commit -m %MSG%
git remote add origin https://github.com/uuf6429/K2F-2.4.git
git remote add origin gitolite@demo.keen.com.mt:testproj.git
git push -u origin master

PAUSE