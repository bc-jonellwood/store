<?php

// Configuration
$internalClassesDir = __DIR__ . '/../internal/classes'; // Target directory for class documentation
$sFeatureId = 'd0605fbe-035f-4e28-a4ba-dffa8b5dc315'; // Fixed feature ID for the database entry

require_once __DIR__ . '/../classes/Page.php'; // Include the Page class

/**
 * Main function to create class documentation.
 * 
 * @param string $className Name of the class (e.g., 'Vehicle').
 */
function createClassDocs($className)
{
    global $internalClassesDir, $sFeatureId;

    // Normalize the class name and folder path
    $className = ucfirst($className);
    $classFolder = "$internalClassesDir/$className";

    // Step 1: Check if the folder already exists
    if (is_dir($classFolder)) {
        echo "Error: Documentation folder for class '$className' already exists.\n";
        exit(1);
    }

    // Step 2: Create the folder
    mkdir($classFolder, 0777, true);

    // Step 3: Create the index.php file
    $indexTemplate = <<<PHP
<?php
// Documentation page for class $className
include '../../config.php';
include_once APP_ROOT . '/classes/Layout.php';

\$layout = new Layout();
\$layout->setVars([
    'pageTitle' => '$className Documentation',
    'language' => 'en',
    'bodyClass' => '$className-page',
]);

\$layout->renderPage('$className.php');
PHP;
    file_put_contents("$classFolder/index.php", $indexTemplate);

    // Step 4: Create the {className}.php file
    $phpTemplate = <<<PHP
<?php
// Documentation content for class $className
?>
<h1>$className Documentation</h1>
<p>This is the documentation page for the <strong>$className</strong> class.</p>
PHP;
    file_put_contents("$classFolder/$className.php", $phpTemplate);

    // Step 5: Create the {className}.js file
    $jsTemplate = <<<JS
// JavaScript for $className documentation
console.log('$className documentation loaded.');
JS;
    file_put_contents("$classFolder/$className.js", $jsTemplate);

    // Step 6: Create the {className}.css file
    $cssTemplate = <<<CSS
/* CSS for $className documentation */
body.$className-page {
    font-family: Arial, sans-serif;
}
CSS;
    file_put_contents("$classFolder/$className.css", $cssTemplate);

    // Step 7: Add the page to the database
    $page = new Page();
    $sPageName = strtolower($className);
    $sPageLoc = "/internal/classes/$className";
    $sPageId = $page->add($sPageName, $sPageLoc, $sFeatureId);

    if ($sPageId) {
        echo "Page for class '$className' successfully added to the database with ID: $sPageId.\n";
    } else {
        echo "Error: Failed to add page for class '$className' to the database.\n";
    }

    // Step 8: Generate the markdown file
    echo "Generating markdown documentation for class '$className'...\n";
    $markdownFile = "$classFolder/$className.md";
    $markdownContent = generateMarkdown($className);
    file_put_contents($markdownFile, $markdownContent);

    echo "Documentation for class '$className' has been successfully created in '$classFolder'.\n";
}

/**
 * Generates markdown documentation for a given class.
 * 
 * @param string $className Name of the class.
 * @return string Markdown content.
 */
function generateMarkdown($className)
{
    // Example markdown content for the class
    $markdown = <<<MD
# $className Class Documentation

## Overview

The `$className` class is responsible for managing the functionality related to `$className`. This documentation provides an overview of its methods and properties.

---

## Class: `$className`

### Properties

- **`private \$exampleProperty`**:  
  Description of the example property.

---

### Methods

#### `exampleMethod()`

Usage: `exampleMethod()`

Description of what this method does.

- **Parameters**:  
  None.

- **Returns**:  
  Description of the return value.

---

## Example Usage

### Initialize the `$className` Class

```php
\$instance = new $className();
```
MD;
    return $markdown;
}

// Example usage
$className = $argv[1] ?? null;
if (!$className) {
    echo "Usage: php createClassDocs.php <ClassName>\n";
    exit(1);
}

createClassDocs($className);
