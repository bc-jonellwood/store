#!php
<?php

// Ensure the script is run from the project root
$rootDir = realpath(__DIR__ . '/..');
// echo $rootDir . '\classes\Page.php';
// die();
require_once $rootDir . '/tools/data/pageIndex.php';
$existingPageGroups = require_once $rootDir . '/tools/data/pageGroupIndex.php';
require_once $rootDir . '/tools/data/linkIndex.php';
require_once $rootDir . '/classes/Page.php';
require_once $rootDir . '/classes/SidenavItem.php';

if ($argc < 3 || $argv[1] !== 'make:page') {
    echo "Usage: php tools\\make.php make:page <PageName>\n";
    exit(1);
}

// Convert names to lowercase
$pageName = strtolower($argv[2]);
$className = ucfirst($pageName);

$pageDir = $rootDir . DIRECTORY_SEPARATOR . $pageName;
$classDir = $rootDir . DIRECTORY_SEPARATOR . 'classes';


// Check if the class already exists
if (file_exists("$classDir/$className.php")) {
    echo "Class '$className' already exists. Please create a pagename that is not an existing class.\n";
    exit(1);
}

// check if the page folder already exists
if (is_dir($pageDir)) {
    echo "Page '$pageName' already exists.\n";
    exit(1);
}

// Show available Page Groups 
echo "Select a Page Group for your new page:\n";
foreach ($existingPageGroups as $index => $group) {
    echo "[$index] {$group['sFeatureName']}\n";
}
echo "Enter the number for the Page Group: ";
$pageGroupIndex = trim(fgets(STDIN));

if (!isset($existingPageGroups[$pageGroupIndex])) {
    echo "Invalid selection. Aborting...\n";
    exit(1);
}

$pageGroupId = $existingPageGroups[$pageGroupIndex]['id'];
// echo $pageGroupId;
// die();

// Create new Page Entry in the database 
$page = new Page();
$pageLoc = '/' . $pageName;
$sPageId = $page->add($pageName, $pageLoc, $pageGroupId);
// echo $sPageId;
// die();


if (!$sPageId) {
    echo "Failed to create page in the database.\n";
    exit(1);
}


// Prompt the user if they want a Sidebar Link Item
echo "Would you like to add a Sidenav Link for this page? (y/n)";
$response = trim(fgets(STDIN));

if (strtolower($response) === 'y') {
    $sidenavItem = new SidenavItem();
    $sidenavItem->add($pageLoc, $pageName, $sPageId);
    echo "Sidenav item added successfully.\n";
}


// Prompt User to create a class for the page
echo "Would you like to create a class for this page? (y/n): ";
$response = trim(fgets(STDIN));

$createClass = strtolower($response) === 'y';


// Create folder and files
mkdir($pageDir);

$indexTemplate = file_get_contents(__DIR__ . '/indexTemplate.php') ?: "<?php\n// \$pageName index page\n";
$indexTemplate = str_replace('$pageName', "'$pageName'", $indexTemplate);  // Add quotes around the replaced value
file_put_contents("$pageDir/index.php", $indexTemplate);

$contentTemplate = file_get_contents(__DIR__ . '/contentTemplate.php') ?: "<?php\n// \$pageName content file\n";
$contentTemplate = str_replace('$pageName', "'$pageName'", $contentTemplate);
file_put_contents("$pageDir/$pageName.php", $contentTemplate);

file_put_contents("$pageDir/$pageName.js", "// JavaScript for $pageName\n");

$cssTemplate = file_get_contents(__DIR__ . '/cssTemplate.css') ?: "<?php\n// \$pageName css file\n";
$cssTemplate = str_replace('$pageName', "'$pageName'", $cssTemplate);
file_put_contents("$pageDir/$pageName.css", $cssTemplate);


if ($createClass) {
    if (!is_dir($classDir)) {
        mkdir($classDir);
    }

    $classContent = <<<PHP
<?php

class $className
{
    public function __construct()
    {
        // Constructor logic for $className
    }

    // Add your methods here
}
PHP;

    file_put_contents("$classDir/$className.php", $classContent);
    echo "Class '$className' created successfully in '/classes'.\n";
}

echo "Page '$pageName' created successfully.\n";