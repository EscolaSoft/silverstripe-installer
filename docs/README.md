# Must read, before continue 
* https://docs.silverstripe.org/en/3/ 
* https://www.silverstripe.org/learn/lessons/
# Dependecies
Before installing first Qunabu SilverStripe Theme boilerplate please make sure all dependecies listed belowe are installed on your machine. 
* Node.js, npm 
* gulp cli 
* composer 
* git client and git flow installed

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
Qunabu has introduced several new techniques that allow you far greater flexibility and control in the scripts you can have on your SilverStripe site's pages.
There is an global namespace `Silverstripe` which all our behaviours script shoule be places next to global settings for whole project. 
### Silverstripe.behaviors
All scripts that manipulates our page will be called from `Silverstripe.behaviors.attachAll` method which is called on `body` `DOMContentLoaded` event once page is loaded. To add your module just add an object that have `attach` method to `Silverstripe.behaviors` global object. 
 Example
 ```
 Silverstripe.behaviors.helloWorld = {
     attach: function (context, settings) {
        console.log('hello world);
       // Code to be run on page load, 
     }
   };
 ```
 Names of module should refer to it functionality. 
### 3rd party libraries and `lib` folder 
 All files placed in `javascript/lib` folder will be imported to the page in alphabetical order. To change order of loading files just rename them. 
### es6
 es6 is supported and should be placed only in `javascript/es6/entry.js` file which is an entry file for webpack. That file is translated by `webpack` and `babel` into `javascript/lib/Z_bundle.js` (prefix *Z_* means that file should be loaded as last in queue). To work with external files use [es6 import](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import). 
 
 #### Example 
 
 File `javascript/es6/entry.js`
 
 ```javascript
 /** import sections */
 import Grid from './dev/grid';
 
 /** attaching behaviors to global object */
 window.SilverStripe.behaviors.Grid = new Grid();
 ```
 
 File `javascript/es6/dev/grid.js`
 
 ```javascript
 export default class Grid {
   constructor() {
     this.cols=12;
     this. colClass="col-xs-1 col-sm-1 col-md-1";
     this.colStyle = [
       'background:rgba(255,0,0,0.1); height:100%;',
       'background:rgba(0,0,255,0.1); height:100%;',
       '1px solid rgba(0,0,0,0.2)'
     ];
     this.onStage=false;
   }
   attach() {
     var self = this;
     window.addEventListener('keydown', function(e) {
       if (e.key=='g') {
         self.toggleGrid()
       }
     })
   }
   toggleGrid() {
     if (this.onStage) {
       document.querySelector('.grid-helper').parentNode.removeChild(document.querySelector('.grid-helper'));
     } else {
       var html = "<div class='grid-helper' style='z-index: 999; width:100%; height: 100%; position:fixed; left:0; top:0;'>";
       html += "<div class='container' style='height:100%;'>";
       html += "<div class='row' style='height:100%;'>"
       for (var i = 1; i <= this.cols; i++) {
         var border_style = i == 1 ?  'border-right:' + this.colStyle[2] + ';border-left:' + this.colStyle[2] : 'border-right:' + this.colStyle[2];
         html += "<div class='" + this.colClass + "' style=' " + border_style + " '><div class='column' style='" + ( this.colStyle[i % 2]) + "'></div></div>";
       }
       html += "</div></div></div>";
       document.body.insertAdjacentHTML('beforeend', html);
     }
     this.onStage = !this.onStage;
   }
 }

 ```
 
## Dev / Live versions 
When switching flag to live version on `_ss_environment.php` to `LIVE`

```php
<?php
/* What kind of environment is this: development, test, or live (ie, production)? */
define('SS_ENVIRONMENT_TYPE', 'live');
```

you should call `gulp deploy-live` which call other `gulp` tasks

* `es6` calls `webpack` to create `Z_bundle.js`
* 'live-scripts' merge all files from `javascript/lib` into `javascript/live/scripts.js` and them minify it with `uglify` to `javascript/live/scripts.min.js`
* 'sass' that compiles `sass/*.scss` into `css/*.css` files 
* 'minify-css' that minifies `css/layout.css` to `css/layout.min.css`


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
