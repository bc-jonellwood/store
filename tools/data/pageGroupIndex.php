<?php
// Created: 2025/03/14 10:06:24
// Last modified: 2025/03/14 11:47:27

$rootDir = realpath(__DIR__ . '/../..');
require_once $rootDir . '/classes/PageGroup.php';

$existingGroups = [];

$group = new PageGroup();

$groups = $group->getAllGroups();

$existingGroups = array_merge($existingGroups, $groups);

return $existingGroups;
// echo "<pre>";
// print_r($existingGroups);
// echo "</pre>";