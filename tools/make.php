#!php
<?php

// Ensure the script is run from the project root
$rootDir = realpath(__DIR__ . '/..');
require_once $rootDir . '/tools/data/pageIndex.php';
$existingPageGroups = require_once $rootDir . '/tools/data/pageGroupIndex.php';
require_once $rootDir . '/tools/data/linkIndex.php';
require_once $rootDir . '/classes/Page.php';
require_once $rootDir . '/classes/SidenavItem.php';

if ($argc < 3 || $argv[1] !== 'make:page') {
    echo "Usage: php tools\\make.php make:page <PageName> [--output-dir=<path>] [--skip-class-check] [--page-group=<index>]\n";
    exit(1);
}

// Parse arguments
$pageName = strtolower($argv[2]);
$className = ucfirst($pageName);
$outputDir = $rootDir; // Default to the project root

// Check for optional --output-dir parameter
foreach ($argv as $arg) {
    if (strpos($arg, '--output-dir=') === 0) {
        $outputDir = realpath(substr($arg, 13)); // Extract the directory path
        if (!$outputDir || !is_dir($outputDir)) {
            echo "Error: Invalid output directory specified.\n";
            exit(1);
        }
    }
}

// Check for optional --skip-class-check parameter
$skipClassCheck = false;
foreach ($argv as $arg) {
    if ($arg === '--skip-class-check') {
        $skipClassCheck = true;
    }
}

// Parse arguments
$pageGroupIndex = null;
foreach ($argv as $arg) {
    if (strpos($arg, '--page-group=') === 0) {
        $pageGroupIndex = (int)substr($arg, 13); // Extract the page group index
    }
}

// If no page group is provided, prompt the user
if ($pageGroupIndex === null) {
    echo "Select a Page Group for your new page:\n";
    echo "[0] Create new PageGroup\n";
    foreach ($existingPageGroups as $index => $group) {
        echo "[" . ($index + 1) . "] {$group['sFeatureName']}\n";
    }
    echo "Enter the number for the Page Group: ";
    $pageGroupIndex = trim(fgets(STDIN));
}

if ($pageGroupIndex === "0") {
    // echo "Enter a Name for the page group: ";
    $newPageGroupName = $pageName;

    $pageGroup = new PageGroup();
    $pageGroup->add($newPageGroupName, $newPageGroupName, 100); // add to db

    try {

        $lastId = new PageGroup();
        $lastIdValue = $lastId->getLastInsertedId($newPageGroupName);

        if (isset($lastIdValue)) {
            $pageGroupId = $lastIdValue;
            echo "New PageGroup '$newPageGroupName' created with ID: $pageGroupId.\n";
        } else {
            echo "Error: Failed to fetch the ID of the newly created PageGroup. Aborting...\n";
            exit(1);
        }
    } catch (PDOException $e) {
        echo "Error: Failed to create new PageGroup. " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    // Adjust the index since we added the "Create new PageGroup" option
    $pageGroupIndex -= 1;

    // Validate the page group index
    if (!isset($existingPageGroups[$pageGroupIndex])) {
        echo "Invalid selection. Aborting...\n";
        exit(1);
    }

    $pageGroupId = $existingPageGroups[$pageGroupIndex]['id'];
}



// Validate the page group index
if (!isset($existingPageGroups[$pageGroupIndex])) {
    echo "Invalid selection. Aborting...\n";
    exit(1);
}

$pageGroupId = $existingPageGroups[$pageGroupIndex]['id'];

$pageDir = $outputDir . DIRECTORY_SEPARATOR . $pageName;
$classDir = $rootDir . DIRECTORY_SEPARATOR . 'classes';

// Check if the class already exists (only if not skipping the check)
if (!$skipClassCheck && file_exists("$classDir/$className.php")) {
    echo "Class '$className' already exists. Please create a page name that is not an existing class.\n";
    exit(1);
}

// Check if the page folder already exists
if (is_dir($pageDir)) {
    echo "Page '$pageName' already exists in the specified directory.\n";
    exit(1);
}

// Create new Page Entry in the database
$page = new Page();
$pageLoc = '/' . $pageName;
$sPageId = $page->add($pageName, $pageLoc, $pageGroupId);

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
$cssTemplate = str_replace('$pageName', "$pageName", $cssTemplate);
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

echo "Page '$pageName' created successfully in '$outputDir'.\n";