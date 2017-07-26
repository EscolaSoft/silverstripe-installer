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
This task moves mysite/_config.php settings to _ss_environment.php
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

# Switching to exisitng project

1. Add vhost like in installation guide (points 3 and 4)
2. Clone the repository from `git.qunabu.com`, change directory to `PROJECT_NAME` and checkout the `develop` branch 

```bash
git clone git@git.qunabu.com:qunabuinteractive/PROJECT_NAME.git
cd PROJECT_NAME
git checkout develop 
```

3. Open `http://PROJECT_NAME.something` in the browser and follow default installation steps.
4. Once installed run task `dev/tasks/SetEnvironmentTask` by calling `http://PROJECT_NAME.something/dev/tasks/SetEnvironmentTask`
This task moves mysite/_config.php settings to _ss_enviroment.php
 
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

```html
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

* preloader is defined and shown as inline css inside `<meta>`. It covers the whole websitecd  which is unstyled since css are not loaded yet. 
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

### .htaccess files to set caching flags
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

`gulp` has two tasks

1. `gulp compress-images` that compress all images inside `themes/PROJECT_NAME/images`
2. `gulp compress-assets` that compress assets. *Beware* this tasks that hughe amount of resources but is essential to be lanuched once for a while. 

## JavaScript helpers

### SilverStripe variables

Namespace SilverStripe contaians following settings 

* `window.SilverStripe.settings.baseUrl` full base URL with protocol, port etc eg. 'http://sunsol2017.qunabu.com/';
* `window.SilverStripe.settings.baseRelUrl` relative base URL, eg  '/';
* `window.SilverStripe.settings.pageUrl` current page URI, eg. '/about-us/';
* `window.SilverStripe.settings.themeDir` current theme main path, eg 'themes/sunsol2017';
* `window.SilverStripe.isLive` Boolean value is site is in live mode;
* `window.SilverStripe.isDev=true` Boolean value is site is in live mode;

### Grid and grid helpers 

All visual elements must fit into grid as they were designed. By default boilerplate is importing [boostrap 4 grid](https://v4-alpha.getbootstrap.com/layout/grid), which is fine with most of the cases.  

In ES6 examples above is `Grid` helper which shows a grid so one can check if all elements fit, pressing `g` on keyboard toggles grid in backgroud, to provide a comfortable way of checking fitting of elements. 

## SCSS and CSS

Task from gulp use Node.js bindings to libsass. There are thee major entry points 

* `sass/layout.scss` which is a main file for all styles on the site.
* `sass/typography` which is a main style for typography, which is 
> * all definition of typography (fonts, colors, sizes, line-height, etc) should be defined in this file 
> * style that is used in [WYSYWIG editor](https://docs.silverstripe.org/en/3.0/reference/typography/)
> * style that use `typography' general css class in the template 

Example 
```css
/* typography.css */
.typography h1, .typography h2 {
  color: #F77;
}
```

```html
<!-- Page.ss -->
<div class="typography">
$Content
</div>
```

* `sass/editor.scss` [WYSIWYG Styles](https://docs.silverstripe.org/en/3/developer_guides/customising_the_admin_interface/typography/) lets you customise the style of content in the CMS.
 
### Structure

Sass structure comes as follows, all new file should have underscore prefix, eg `_footer.scss`;

```
sass/layout.scss // main, entry file 
sass/editor.scss // style for WYSIWYG editor
sass/typography.scss // typography declaration used by WYSIWYG editor and components that display content  
sass/components // folder where components should be kept, eg. _header.scss, _footer.scss, _navigation.scss
sass/base // folder where all variables mixins and all abstract reusabe elements, eg. _mixins.scss, _vars.scss
sass/pages // folder wheren specific page styles are declared, eg. _homePage.scss, _contactPage.scss
```

### Styleguide

Styleguide is essential and first source of truth when developing theme from design provided by designer. 

* All possible font variation should be declared in `typography.scss` You can use only fonts from mixins provided. 
example 
```sass
@mixin default-txt() {
  font-family: 'Titillium Web', sans-serif;
  font-weight: 300;
  line-height: 1.86;
  letter-spacing: 0.4px;
  color: $color_txt_default;
  @include fluid-type(font-size, 14px, 17px);
}
```
* All possible colors must be declated in  `base/_vars.scss` file 
Example 
```sass
$color_white:#fff;
$color_bg_lightgray:#f8f8f8;
$color_txt_default:#9f9f9f;
$color_yellow:#fccc07;
$color_orange:#f9ab10;
```
* All vertical heights must be declated in  `base/_vars.scss` file. When definig vertical space between elements, it should be one from the list 
Example 
```sass
$vertical_h1: 15px;
$vertical_h2: 30px;
$vertical_h3: 40px;
$vertical_h4: 55px;
$vertical_h5: 70px;
$vertical_h6: 90px;
$vertical_h7: 110px;
```

### Bootstrap Grid

#### Customising the grid 

Bootrsap Grid give a straight forward way fo [customising grid by overwritting variables](https://v4-alpha.getbootstrap.com/layout/grid/#variables). Our grid is declared in  `sass/base/_bootstrap-config.scss` 

Example config 
```sass
//FILE sass/base/_bootstrap-config.scss
$grid-columns:      12;
$grid-gutter-width-base: 30px;

$grid-gutter-widths: (
  xs: $grid-gutter-width-base, // 30px
  sm: $grid-gutter-width-base, // 30px
  md: $grid-gutter-width-base, // 30px
  lg: $grid-gutter-width-base, // 30px
  xl: $grid-gutter-width-base  // 30px
)

$grid-breakpoints: (
  // Extra small screen / phone
  xs: 0,
  // Small screen / phone
  sm: 576px,
  // Medium screen / tablet
  md: 768px,
  // Large screen / desktop
  lg: 992px,
  // Extra large screen / wide desktop
  xl: 1200px
);

$container-max-widths: (
  sm: 540px,
  md: 720px,
  lg: 960px,
  xl: 1140px
);
```

#### Mixins 

There are some [mixins](https://v4-alpha.getbootstrap.com/layout/grid/#mixins) provided by bootstrap that allows making custom css classes to work with grid. 

Example (from boostrap docs) [see it in action rendered](https://jsbin.com/ruxona/edit?html,output).
```sass
.container {
  max-width: 60em;
  @include make-container();
}
.row {
  @include make-row();
}
.content-main {
  @include make-col-ready();

  @media (max-width: 32em) {
    @include make-col(6);
  }
  @media (min-width: 32.1em) {
    @include make-col(8);
  }
}
.content-secondary {
  @include make-col-ready();

  @media (max-width: 32em) {
    @include make-col(6);
  }
  @media (min-width: 32.1em) {
    @include make-col(4);
  }
}
```

### Responsive 

By default our boilerplace responsive mixins for bootstrap [which are documented here](https://v4-alpha.getbootstrap.com/layout/overview/#responsive-breakpoints). Breakpoints are declatred in `sass/base/_bootstrap-config.scss`

* `media-breakpoint-down(breakpoint)`
* `media-breakpoint-up(breakpoint)`
* `media-breakpoint-between(breakpoint-down, breakpoint-up)`
 
## Fluid vars  

Unlike responsive typography, which changes only at set breakpoints, [fluid typography](https://www.smashingmagazine.com/2016/05/fluid-typography/) resizes smoothly to match any device width. And this does not refers only to typography. Fluid vars are achived by sass mixins. 
 
 * `@mixin fluid-type-min-max($properties, $min-vw, $max-vw, $min-value, $max-value)`
 * `@mixin fluid-type($properties, $min-value, $max-value)`
 
Examples

```sass
html {
  @include fluid-type(font-size, 320px, 1366px, 14px, 18px);
}
```

which sets two breakspoints for vieport width, max 1366px and min 320px. Results is 
* when viewport width is 320px or smaller font-size is 14px;
* when viewport width is 1366px or wider font-size is 18px;
* when viewport width is between 1366px and 320px font-size is value between 18px and 14px;


```sass
// Multiple properties with same values
h1 {
  @include fluid-type(padding-bottom padding-top, 20em, 70em, 2em, 4em);
}
```

`fluid-type` is shorthand that takes minimum and maximum viewport width from `sass/base/_bootstrap-config.scss`
 
```sass
@mixin fluid-type($properties, $min-value, $max-value) {
  @include fluid-type-min-max($properties, map-get($grid-breakpoints, sm), map-get($grid-breakpoints, xxl), $min-value, $max-value)
}
```

```sass
body {
  @include fluid-type(font-size, 14px, 17px);
}
```
 
## Styling login form 

Tomek todo

# Additional modules 
## Backuper

Experimental module that is resposible for two tasks
1. Create and backup of database and assets folder 
2. Ability to import exported database. 

Does work only in `dev` mode

## HTMLBlocks

Allows to inject HTML Blocks into .SS templates and HTMLContent as shortcode.
Simple idea of managing reusable html parts in different sections of layout. 
It gives panel admins to change reusable parts. 

Example 

1. In the panel create new HTML Block with id FooterContent 

2. In footer put this code
 

```html
<%-- Footer.ss --%>
{$HTMLBlock('footer')}
```

3. Now you're able to manage content of that section from the panel 


## Sortlable fields

Simplest sorting can be achieved with [GridFieldOrderableRows](https://github.com/symbiote/silverstripe-gridfieldextensions/blob/master/docs/en/index.md#orderable-rows) in the following example

Lets assume we have a Block with Logos we want to arrange. The following example allows to drag&drop elements in the gridfield.

```php 
class LogosBlock extends DataObject {
        
    private static $has_many = array("Logos" => "Logo");
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $gridfield = $fields->dataFieldByName("Logos");
        $conf = $gridfield->getConfig();
        $conf->addComponent(new GridFieldOrderableRows('Order'));
        return $fields;
    }
}

class Logo extends DataObject {
    
    private static $db = array(
        "Order" => "Int"
    );
    
    private static $default_sort = "Order";
    
    private static $has_one = array(
      "LogosBlock" => "LogosBlock", 
      "Image" => "Image"
    );
}

```

## GridField components [ss-gridfield-utils](https://github.com/milkyway-multimedia/ss-gridfield-utils)
### Gridfield. Add inline
This components make GridField more user friendly, so you can add many DataObject on one admin page without useless clicks.

Example allows adding many regions at once 

```php
class Region extends DataObject {
    //...    
    private static $summary_fields = array (
        'Photo' => '',
        'Title' => 'Title of region',
        'Description' => 'Short description'
    );
    //...
}

class RegionsPage extends Page {

    private static $has_many = array (
        'Regions' => 'Region'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();
		
		$gridfield = GridField::create(
            'Regions',
            'Regions on this page',
            $this->Regions(),
            GridFieldConfig_RecordEditor::create());
			
		$config = $gridfield->getConfig();
		$component = new Milkyway\SS\GridFieldUtils\AddNewInlineExtended($fragment = 'buttons-before-left', $title = 'Add Regions (inline)');
       	$config->addComponent($component);
		
        $fields->addFieldToTab('Root.Regions', $gridfield);

        return $fields;
    }
}
```

### Gridfield. Edit inline 

The `Milkyway\SS\GridFieldUtils\EditableRow` component adds an expandable form to each row in the GridField, allowing you to edit records directly from the GridField. This makes the GridField act like a Tree, with nested GridFields working as expected.

To add this component use the following snippets like in example above 
```php
/// ...
    $gridfield->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\EditableRow($fields = null));
/// ...	
```

### Problem with HTMLText and TinyMCE 

Datatype HTMLText by default launch TinyMCE which won't work out of the box if there are more then one editor on one page. Solution is to add random ID to the form 

Example 

```php
class NewsletterNews extends DataObject {
  static $db = array(
    'Text'=>'HTMLText'
  );
  static $has_one = array(
    'Newsletter'=>'Newsletter'
  );

  public function getCMSFields() {
    $fields = parent::getCMSFields();
    $fields->removeByName('NewsletterID');
    /** @var HtmlEditorField $tinymce */
    $tinymce = $fields->dataFieldByName('Text');
    $tinymce->setAttribute('id', 'HiImRandomID'.rand(0,1000));
    return $fields;
  }
}
```

## [GridFieldBulkEditingTools](https://github.com/colymba/GridFieldBulkEditingTools). Bulk upload

When uploading many images at once [BulkUpload](https://github.com/colymba/GridFieldBulkEditingTools/blob/master/bulkUpload/BULK_UPLOAD.md) is required. Other bulk tasks are available as well.

Example 

```php 
class LogosBlock extends DataObject {
        
    private static $has_many = array("Logos" => "Logo");
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $gridfield = $fields->dataFieldByName("Logos");
        $config = $gridfield->getConfig();
        $gfbu = new GridFieldBulkUpload();
        $gfbu->setUfSetup('setFolderName', 'logos'); // name of the folder where images should be uploaded. 
        $config->addComponent($gfbu);
        return $fields;
    }
}

class Logo extends DataObject {
    
    private static $db = array(
        "Order" => "Int"
    );
    
    private static $default_sort = "Order";
    
    private static $has_one = array(
      "LogosBlock" => "LogosBlock", 
      "Image" => "Image"
    );
}

```

## Email helpers
Allows to sent email with SMTP. Requires setup in  `mysite/_config/config.yml` eg

```yml
SmtpMailer:
  host: mailing.gmail.com
  user: XXX
  password: XXX
  encryption: tls
  charset: UTF-8
  #SMTPDedug: 4 //allows to debug connection 
```  
  
## Has one field

It behaves like gridfield but for `has_one` relation. 

Example

```php

class Address extends DataObject {
//any delcaration here
}

class AddressesPage extends Page {

  $has_one = array(
    'MainAddress'=>'Address'
  );

  public function getCMSFields() {
    $fields = parent::getCMSFields();
    if($this->MainAddress()->exists()){
      $fields->addFieldsToTab("Root.MainAddress", array(
        ReadonlyField::create("add", "MainAddress", $this->MainAddress()->toString())
      ));
    }
    $fields->removeByName("MainAddressID");
    $fields->addFieldToTab("Root.MainAddress",
      HasOneButtonField::create("MainAddress", "MainAddress", $this) //here!
    );

    return $fields;
  }
}
```

## [UserForms](https://github.com/silverstripe/silverstripe-userforms) 

UserForms module provides a visual form builder for the SilverStripe CMS. No coding required to build forms such as contact pages.

## Google Maps field

In `mysite/_config/config.yml` setup Google Maps API Key 

```yml
GoogleMapField:
  default_options:
    api_key: XXX
```

Example 

```php
class MapPoint extends DataObject {
  static $db = array(
    'Title'=>'Varchar',
    'Latitude'=>'Decimal(9,6)',
    'Longitude'=>'Decimal(9,6)',
  );
  public function getCMSFields() {
    $fields = parent::getCMSFields();
    $root->removeByName('Latitude');
    $root->removeByName('Longitude');
    $fields->addFieldToTab('Root.MapCenter', new GoogleMapField($this, 'Center of Map', array('api_key'=>'AIzaSyANH1lvL_a1y0-LhmTHug8w7WNDCtG-ScY')));
    return $fields;
  }
}
```


## [Silverstripe-Content-Blocks](https://github.com/NobrainerWeb/Silverstripe-Content-Blocks) 

This module gives you the option to create your content, in little blocks, instead of just one big content area.

*This module is just an idea how some types of website should be built, Qunabu has own Blocks module that will be described soon.*

## Qunabu Helpers - 

Qunabu helpers provide 

* DateExtension Date Extension to resolve UTF8 issues with `FormatI18N` in non-english languages. `UTF8FormatI18N` as solution 
* Greyscaled Image Extension 
* HTMLTextExtension HTMLText Extension provide easy way to protect emails from bots with `ProtectEmails` method 
* ImageHelperExtension Image Extension provide `DominantColor` method
* PageHelperExtension provides
> * isDev
> * isLive
> * getJavaScriptLibFiles
* SetEnvironmentTask

# Gitlab pipeline 

Gitlab piple provides an easy way to 
* run tests on git branches
* deploy stage, test and live 

## Pipeline example

```yml
#.gitlab-ci.yml in main folder
stages:
  - test
  - build
  - deploy

test:
  stage: test
  script: echo "Running tests"

build:
  stage: build
  script: echo "Building the app"

deploy_stage:
  stage: deploy
  environment:
    name: stage
    url: http://PROJECT_NAME.qunabu.com
  before_script:
  # install ssh-agent
  - 'which ssh-agent || ( apt-get update -y && apt-get install openssh-client -y )'
  # run ssh-agent
  - eval $(ssh-agent -s)
  # add ssh key stored in SSH_PRIVATE_KEY variable to the agent store
  # - $SSH_PRIVATE_KEY
  - ssh-add <(echo "$SSH_PRIVATE_KEY")
  # disable host key checking (NOTE: makes you susceptible to man-in-the-middle attacks)
  # WARNING: use only in docker container, if you use it with shell you will overwrite your user's ssh config
  - mkdir -p ~/.ssh
  - echo -e "Host *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config
  script:
  - echo "Deploy to staging server"
  - ssh XXX@XXX.com 'exec /home/qunabu/webapps/PROJECT_NAME/build.sh'
  only:
  - develop
```

then you need to create a deploy script called `build.sh` in main folder and add it to git. 
 
Example 

```bash
#!/usr/bin/env bash
cd /home/qunabu/webapps/PROJECT_NAME
git pull
php56 /home/qunabu/bin/composer.phar update
php56 /home/qunabu/webapps/PROJECT_NAME/framework/cli-script.php dev/build "?flush=1"
```

to make `cli-script.php dev/build "?flush=1"` from cli you need to set one manifest file in `_ss_environment.php`. 
This file should be ignored by git so that amend should be done manually on stage server. 

```php
define('MANIFEST_FILE', TEMP_FOLDER . "/manifest-main");
```

In your gitlab project you must provide an [SSH_PRIVATE_KEY](https://docs.gitlab.com/ee/ci/ssh_keys/README.html) which you can log into server. 

[SSH login without password](http://www.linuxproblem.org/art_9.html) 

1. ssh keys we're going to use should not have password
2. if you don't have a ssh key [generate one](https://help.github.com/articles/connecting-to-github-with-ssh/) 
3. copy your public key id_rsa.pub to clipboard `pbcopy < ~/.ssh/id_rsa.pub`
4. log into the stage server and add your key to `.ssh/authorized_keys`
5. copy your private key id_rsa to clipboard `pbcopy < ~/.ssh/id_rsa`
6. in gitlab in project settings under CI/CD Pipelines create new variable `SSH_PRIVATE_KEY` and paste your code there

Git pull on server, install everything set up `_ss_environment.php` and next git push should be build automatically. 

# Crazy issues 
## Polish sorting 

Sorting in SilverStripe is by default made within sql database, to achive non-english sorting add this code to `mysite/_config/config.yml`

```yml
MySQLDatabase:
  # You are advised to backup your tables if changing settings on an existing database
  # `connection_charset` and `charset` should be equal, similarly so should `connection_collation` and `collation`
  connection_charset: utf8
  connection_collation: utf8_polish_ci
  charset: utf8
  collation: utf8_polish_ci
```

## UserForms 

### UDF email for user 
todo Tomek ( w którym pl yml co trzeba było dodać) 

### UDF custom fields validation 
todo Tomek - możesz to opisać https://jqueryvalidation.org/

## Simple REST API for AJAX calls
todo

# Road map
* Docker 
