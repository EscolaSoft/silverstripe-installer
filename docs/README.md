# Must read, before continue 
* https://docs.silverstripe.org/en/3/ 
* https://www.silverstripe.org/learn/lessons/
# Dependecies
Before installing first Qunabu SilverStripe Theme boilerplate please make sure all dependecies listed belowe are installed on your machine. 
* Node, npm 
* Grunt and Gulp cli 
* Composer 
* Git client and git flow installed

# Before installation 
1. Create an empty mysql database
2. Create an empty folder PROJECT_NAME where project is stored 
3. Setup a vhost that points to that folder. Configuration below is just fine
```
<VirtualHost *:80>
    DocumentRoot "/project-path/project-directory"
    ServerName PROJECT_NAME.SOMETHING
    <Directory "/project-path/project-directory">
        AllowOverride All
        Order Allow,Deny
        Allow from all
        Require all granted
    </Directory>
</VirtualHost>
```
4. Add vhost to `/etc/hosts/`
```
127.0.0.1 PROJECT_NAME.SOMETHING
```

# Installation
1. Open terminal and enter project folder 
```
cd PROJECT_NAME
```
2. Run composer command 
```
composer create-project -s dev qunabu/silverstripe-installer PROJECT_NAME
```
3. Open `http://PROJECT_NAME.something` in the browser and follow default installation steps.
4. Once installed run task `dev/tasks/SetEnvironmentTask` by calling `http://PROJECT_NAME.something/dev/tasks/SetEnvironmentTask`
This task moves mysite/_config.php settings to _ss_enviroment.php
5. Create or copy project URL from qunabu gitlab git.qunabu.com. Likely url to you repository would be `git@git.qunabu.com:qunabuinteractive/PROJECT_NAME.git`
6. Run following commands in main folder of your projects
```
git init
git flow init 
git add . 
git commit -m "initial commit" 
git remote add origin PROJECT_GIT_URL
git push --all
```
7. Now we're ready to start developinig our theme
 
# Qunabu boilerplate theme 
1. In terminal go into theme dir
```
cd PROJECT_NAME
cd themes/qunabu
npm install
```
2. Run gulp by calling command  `gulp`  which starts `gulp watch` that do following 
> * every change from `*.php, *.ss, *.js` will call livereload and refresh a browser
> * every change from `*.sass` file will call `libsass` and compile `*.sass` to `*.css` and reload styles in the browser without refreshing. 
3. Workflow is standard SilverStripe. There are some special features we try to put into every projects. 
## Javascript implementation. Silverstripe global object and behaviours 
todo
## Dev / Live versions 
todo
## Async img loading (lazyload + svg placeholder)
todo
### Dominant image (Qunabu Helpers)
todo
## Preloader (hidden in dev) 
todo
## Live version (How to 100/100 Page Speed) 
todo
## Async loading of main js file 
todo
## Preloader -  Css async loading 
todo
## JavaScript helpers (only in dev)
todo
## Grid and grid helpers 
todo
## SCSS structure
todo
## Fluid vars (https://www.smashingmagazine.com/2016/05/fluid-typography/) 
todo

# Additional modules 
## Backuper
todo
## HTMLBlocks
todo
## Sortlable filelds
todo
## Gridfield. Add inline
todo
## Gridfield. Edit inline 
todo
## Email helpers
todo
## Has one field
todo
## User forms 
todo
## Google Maps fields
todo
## Blocks 
todo
## Qunabu Helpers - 
### Page extenstion (isDev)
todo
### Image extenstion (Dominant color)
todo

# Gitlab pipeline 
## Pipeline
todo
## Deploy
todo

# Crazy issues 
## Polish sorting 
todo
## UDF email for user 
todo
## Simple REST API for AJAX calls
todo

# Road map
* Docker 
