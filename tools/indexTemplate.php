<?php
// Created: 2025/03/14 08:17:56
// Last Modified: 2025/03/27 07:52:03
// There is a VS Code extension called 'Auto Time Stamp' that will automatically add the created and last modified comments for you. If you don't want this in the file you can remove it from /tools/indexTemplate.php   
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| These files were created when the project was initiated. They need to included
|--------------------------------------------------------------------------
|
*/
include '../config.php'; // adjust this path as needed
include_once APP_ROOT . '/classes/Layout.php';
include_once APP_ROOT . '/classes/Page.php';
include_once APP_ROOT . '/classes/AccessControl.php';

$loggedIn = Layout::confirmLoggedIn();
if (!$loggedIn) {
    header("Location: /auth/index.php");
}

// this value will be created in the make method... I will work on placing it dynamically after creation
$pageId = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX';
$accessRequired = Page::getAccessRequired($pageId);
AccessControl::enforce($accessRequired);

/*
|--------------------------------------------------------------------------
| Initialize page assets
|--------------------------------------------------------------------------
| These are the default js and css file generated with this page. Ideally
| there is no need to change these settings, but feel free to do so to work
| with your coding styles. Around line 61 there is a method to add other js
| files. 
|
| If you have additional CSS files to load - specific to the page - you can 
| load them here. Site wide CSS should be in the default template file.
| `/templates/layouts/default.php`
|
| TODO I know the formatting for the path is jacked up with extra ' in there. I will get to it at some point.
*/
$assets = new Assets();
$assets->addCss("$pageName.css")
    ->addJs("$pageName.js", true);


/*
|--------------------------------------------------------------------------
| Create the Layout Instance
|--------------------------------------------------------------------------
|
*/
$layout = new Layout();

/*
|--------------------------------------------------------------------------
| Set page variables
|--------------------------------------------------------------------------
|
| The default `'pageTitle' => $pageName` will default the page name to the 
| value passed in during page creation. Feel free to update to a string
| that better reflects the name of the page if desired
|
*/
$layout->setVars([
    'pageTitle' => $pageName,
    'language' => 'en',
    'bodyClass' => $pageName . '-page',
    'assets' => $assets
]);

/*
|--------------------------------------------------------------------------
| Set sidebar value
|--------------------------------------------------------------------------
|
| The default sidebar will be loaded in. In the event you create a custom
| sidebar, preferably in the `/components` folder (but thats up to you), you
| can replace the component name in the function call to load that asset.
|
*/
$layout->setSection('sidebar', APP_ROOT . '/components/sidenav.php');

/*
|--------------------------------------------------------------------------
| Add page specific custom javascript
|--------------------------------------------------------------------------
|
| `$layout->setSectionContent('scripts', '<script src="https://example.com/fake.js"></script>')`
| will load external js scripts. You can also load in local js scripts, from a 
| /functions or /utilities folder for example. 
|
*/
// $layout->setSectionContent('scripts', '<script src="/js/super-slick.js"></script>');

/*
|--------------------------------------------------------------------------
| Render the page with specific variables for the content area
|--------------------------------------------------------------------------
|
| You can either call renderPage and just pass in the $pageName value or 
| props
| The onboarding.php file is where you will add the html for the page. 
| You can add the props to the page as needed (example: second renderPage method call).
|
*/
// uncomment this line to render the page with no props
//$layout->renderPage("$pageName.php");

$layout->renderPage("$pageName.php", [
    'foundingYear' => '2025',
    'teamMembers' => [
        ['name' => 'Jon Ellwood', 'position' => 'Chief Data Architect'],
        ['name' => 'Slick', 'position' => 'Artistic Vision Coordinator']
    ]
]);
