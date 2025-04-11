<?php
// Created: 2025/03/14 10:49:12
// Last modified: 2025/03/14 11:46:14


$rootDir = realpath(__DIR__ . '/../..');
require_once $rootDir . '/classes/Page.php';


$existingPages = [];

$page = new Page();

$pages = $page->getPages();

$existingPages = array_merge($existingPages, $pages);

// echo "<pre>";
// print_r($existingPages);
// echo "</pre>";