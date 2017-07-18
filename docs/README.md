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
2. Create an empty folder `PROJECT_NAME` where project is stored 
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

```bash
127.0.0.1 PROJECT_NAME.SOMETHING
```

# Installation
1. Open terminal and enter project folder 

```bash
cd PROJECT_NAME
```

2. Run composer command 

```bash
composer create-project -s dev qunabu/silverstripe-installer PROJECT_NAME
```

3. Open `http://PROJECT_NAME.something` in the browser and follow default installation steps.
4. Once installed run task `dev/tasks/SetEnvironmentTask` by calling `http://PROJECT_NAME.something/dev/tasks/SetEnvironmentTask`
This task moves mysite/_config.php settings to _ss_enviroment.php
5. Create or copy project URL from qunabu gitlab git.qunabu.com. Likely url to you repository would be `git@git.qunabu.com:qunabuinteractive/PROJECT_NAME.git`
6. Run following commands in main folder of your projects

```bash
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

```bash
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
There is an global namespace `Silverstripe` which all our behaviours script should be placed next to global settings for whole project. 
### Silverstripe.behaviors
All scripts that manipulates our page will be called from `Silverstripe.behaviors.attachAll` method which is called on `body` `DOMContentLoaded` event once page is loaded. To add your module just add an object that have `attach` method to `Silverstripe.behaviors` global object. 
 
 Example
 
 ```javascript
 Silverstripe.behaviors.helloWorld = {
     attach: function (context, settings) {
        console.log('hello world');
       // Code to be run on page load, 
     }
   };
 ```
 Names of module should refer to it functionality. 
### 3rd party libraries and `lib` folder 
 All files placed in `javascript/lib` folder will be imported to the page in alphabetical order. To change order of loading files just rename them. 
### es6
 [ES6/ES2015](http://es6-features.org/) is supported and should be placed only in `javascript/es6/entry.js` file which is an entry file for webpack. That file is translated by `webpack` and `babel` into `javascript/lib/Z_bundle.js` (prefix *Z_* means that file should be loaded as last in queue). To work with external files use [es6 import](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import). 
 
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
 class Grid {
  constructor() {
    this.cols=12;
    this.colClass="col-xs-1 col-sm-1 col-md-1";
    this.colStyle = [
      'background:rgba(255,0,0,0.1); height:100%;',
      'background:rgba(0,0,255,0.1); height:100%;',
      '1px solid rgba(0,0,0,0.2)'
    ];
    this.onStage=false;
  }
  attach() {
    let self = this;
    window.addEventListener('keydown', function(e) {
      if (e.key === 'g') {
        self.toggleGrid()
      }
    })
  }
  toggleGrid() {
    if (this.onStage) {
      document.querySelector('.grid-helper').parentNode.removeChild(document.querySelector('.grid-helper'));
    } else {
      let html = "<div class='grid-helper' style='z-index: 999; width:100%; height: 100%; position:fixed; left:0; top:0;'>";
      html += "<div class='container' style='height:100%;'>";
      html += "<div class='row' style='height:100%;'>";
      for (let i = 1; i <= this.cols; i++) {
        let border_style = i == 1 ?  `border-right: ${this.colStyle[2]} ;border-left: ${this.colStyle[2]}` : `border-right: ${this.colStyle[2]}`;
        html += `<div class=' ${this.colClass} ' style=' ${border_style} '><div class='column' style=' ${this.colStyle[i % 2]} '></div></div>`;
      }
      html += "</div></div></div>";
      document.body.insertAdjacentHTML('beforeend', html);
    }
    this.onStage = !this.onStage;
  }
}

export default Grid;

 ```
 
## Dev / Live versions 

Dev is default mode when developing website. Once everything is ready to publish website should be switched to "live", which is crutial part of making website work fast. 

Switching flag to live version in file `_ss_environment.php` makes website "live"

```php
<?php
/* What kind of environment is this: development, test, or live (ie, production)? */
define('SS_ENVIRONMENT_TYPE', 'live');
```

you should call `gulp deploy-live` which calls other `gulp` tasks

* `es6` calls `webpack` to create `Z_bundle.js`
* `live-scripts` merge all files from `javascript/lib` into `javascript/live/scripts.js` and them minify it with `uglify` to `javascript/live/scripts.min.js`
* `sass` that compiles `sass/*.scss` into `css/*.css` files 
* `minify-css` that minifies `css/layout.css` to `css/layout.min.css`

Once site is in `LIVE` mode css and js files are loaded asynchroniusly. Look at the following code 

```
<script>
  window.SilverStripe.loadScript(["{$ThemeDir}/javascript/live/scripts.min.js", "{$ThemeDir}/css/layout.min.css"], function() {
    //console.log('all loaded');
    SilverStripe.behaviors.init(); // Calls all behaviors attach method
  } )
</script>
```

which loades all essential JavaScript and StyleSheets. Before that event preloader should be visible and once `layout.min.css` is loaded preloader should fade out. All of codes above are part of boilerplate (in `{$ThemeDir}/templates/Includes/JavaScript`).

## Async img loading (lazyload + svg placeholder)

### Responsive Images `srcset`, lazy loading and `lazysizes`

[lazysizes](https://github.com/aFarkas/lazysizes) is chosen library for asynchronius image loading with support of `src-set` retina ready images. Most of images should be served in that way. This libarary is part of boilerplace and it is included bu default. 
 
Example in template `.ss` file 

```html
<!-- responsive example with automatic sizes calculation: -->
<%-- $Image is Image Object from the Controller
<img
    data-sizes="auto"
    data-src="{$Image.URL}"
    data-srcset="{$Image.setWidth(300).URL} 300w,
    {$Image.setWidth(600).URL} 600w,
    {$Image.setWidth(900).URL}g 900w" class="lazyload" />
```

### SVG placeholder technique 

The following technique shows `svg` rectangle in the same ratio as an image before it is loaded 

```html
<%-- $Image is Image Object from the Controller
<div class="img">
  <img data-src="{$Image.URL}" class="lazyload" />
  <svg>
  	<rect width="{$Image.Width}" height="{$Image.Height}"/>
  </svg>
</div>
<style>
div.img {
  position:relative;
}
div.img img {
  position:absolute:
  z-index:10;
  width:100%;
  height:auto;
  left:0;
  top:0;
}
div.img svg {
  position:realtive;
  z-index:9;
  width:100%;
  height:auto;
}
</style>
```

*Note* that there is a bug with `SVG` `height:auto;` in Safari. 

### Dominant Color (Qunabu Helpers)

Qunabu Helpers introduce Dominant Color Image extenstion which returns main color of image. Given exaple above, placeholder technique with this extenstion would change one line of `SVG` definition to 

```html
  <rect width="{$Image.Width}" height="{$Image.Height}" style="fill:{$Image.DominantColor}/>
```

## Preloader (hidden in dev) 

Straight forward technique of showing preloader 

* preloader is defined and shown as inline css inside `<meta>` 
* preloader is hidden in `layout.css` one it is loaded before end of '</body>'

This technique is built-in boilerplate. 

Example 

```html
<!-- Page.ss -->
<html>
<meta>
<style type="text/css">
  #main-loader {
    position: fixed;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    z-index: 9998;
    background: rgba(255, 255, 255, 0.9);
    -webkit-transition: opacity 0.35s ease-out, visibility 0.35s linear;
    transition: opacity 0.35s ease-out, visibility 0.35s linear;
    visibility: visible;
    will-change: opacity; }
  #main-loader:after {
    content: 'loading'; }
</style>
</meta>
<body>
<div id="main-loader"></div>
<script>
  window.SilverStripe.loadScript(["{$ThemeDir}/javascript/live/scripts.min.js", "{$ThemeDir}/css/layout.min.css"], function() {
    //console.log('all loaded');
    SilverStripe.behaviors.init(); // Calls all behaviors attach method
  } )
</script>
</body>
</html>
```

```css
/* layout.css */
#main-loader {
  visibility: hidden;
  opacity: 0; }
```

In this technique preloader is only visible in `live` mode, in `dev` mode all scripts and styles are loaded at once.   

## Live version (How to 100/100 Page Speed) 
To get sitespeed at hightest level except of techniques above there are some additional ones below. 
## .htaccess files to set caching flags
Put this file as `.htaccess` into `assets` and `themes/PROJECT_NAME` to set caching flags
```
# Set up caching on media files for 1 week
<IfModule mod_headers.c>
<FilesMatch "\.(gif|jpg|jpeg|png|swf)$">
    ExpiresDefault A604800
    Header append Cache-Control "public"
</FilesMatch>
</IfModule>
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 seconds"
  ExpiresByType text/html "access plus 1 seconds"
  ExpiresByType image/x-icon "access plus 2592000 seconds"
  ExpiresByType image/gif "access plus 2592000 seconds"
  ExpiresByType image/jpeg "access plus 2592000 seconds"
  ExpiresByType image/png "access plus 2592000 seconds"
  ExpiresByType text/css "access plus 604800 seconds"
  ExpiresByType text/javascript "access plus 86400 seconds"
  ExpiresByType application/x-javascript "access plus 86400 seconds"
</IfModule>
```

## Async loading of main js file 

Except of `<script>` flags `defer` and `async` our namespace `window.SilverStripe` introduces simple `window.SilverStripe.loadScript` method of asynchronious loading scripts and styles, which was mentioned before. 

Example

```javascript
  window.SilverStripe.loadScript(["{$ThemeDir}/javascript/component.js", "{$ThemeDir}/css/component.css"], function() {
    console.log('all loaded now');
  })
```

### Preloader -  Css async loading 

This technique is described above. 

### Minifiing images

`gulp` task TODO

## JavaScript helpers (only in dev)
todo
## Grid and grid helpers 
todo
## SCSS
libsass 
### structure
todo
### responsive vars and mixins
todo 
## Fluid vars (https://www.smashingmagazine.com/2016/05/fluid-typography/) 
todo
## Styling login form 
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
## UserForms 
### UDF email for user 
todo
### UDF custom fields validation 
todo https://jqueryvalidation.org/
## Simple REST API for AJAX calls
todo

# Road map
* Docker 
