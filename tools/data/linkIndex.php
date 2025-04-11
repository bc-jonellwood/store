<?php
// Created: 2025/03/14 09:54:06
// Last modified: 2025/03/14 11:46:34


$rootDir = realpath(__DIR__ . '/../..');
require_once $rootDir . '/classes/SidenavItem.php';

$existingLinks = [];

$sidenavItem = new SidenavItem();

$sidenavItems = $sidenavItem->getAllSidenavItems();

$existingLinks = array_merge($existingLinks, $sidenavItems);

// echo "<pre>";
// print_r($existingLinks);
// echo "</pre>";