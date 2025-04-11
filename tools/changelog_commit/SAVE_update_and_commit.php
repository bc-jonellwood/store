<?php
// Created: 2025/03/28 08:40:31
// Last modified: 2025/03/30 09:39:41

// $output = shell_exec("python3 update_changelog.py 2>&1");
// include_once '../../config.php';
// include_once APP_ROOT . '/classes/Logger.php';
// include_once APP_ROOT . '/data/appConfig.php';

// $start = microtime(true);
// $output = shell_exec("python3 update_changelog.py 2>&1");
// $end = microtime(true);
// Logger::logInfo("Raw Python output: '$output' ");
// // $newVersion = trim($output);

// Logger::logLocal("Python script execution time: " . ($end - $start) . " seconds");

// // Split output into lines
// $lines = explode("\n", trim($output));

// // Extract version (last line)
// $newVersion = trim(array_pop($lines));

// // Remaining lines are Git info
// $sNotes = implode("\n", $lines);

// Logger::logLocal("Extracted version: '$newVersion'");
// Logger::logLocal("Extracted notes: '$sNotes'");



// $dbconf = new appConfig;
// $serverName = $dbconf->serverName;
// $database = $dbconf->database;
// $uid = $dbconf->uid;
// $pwd = $dbconf->pwd;

// if ($newVersion) {
//     Logger::logLocal("Attempting to insert version: '$newVersion' into database.");
//     // Step 2: Insert into database
//     try {
//         $conn = new PDO("sqlsrv:Server=$serverName;Database=$database;ConnectionPooling=0;TrustServerCertificate=true", $uid, $pwd);
//         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//         // $sql = "INSERT INTO app_version_control(sVersion, sNotes) VALUES (:newVersion, :notes)";
//         $sql = "INSERT INTO app_version_control (sVersion, sNotes) VALUES (?, ?)";
//         $stmt = $conn->prepare($sql);
//         // $stmt->bindParam(':newVersion', $newVersion, PDO::PARAM_STR);
//         $stmt->execute([$newVersion, $sNotes]);
//     } catch (PDOException $e) {
//         Logger::logLocal('Error inserting into database in update_and_commit.php' . $e->getMessage());
//     }

//     // $stmt->execute([$newVersion, "Automated update"]);

//     // Step 3: Log the process
//     Logger::LogLocal("Changelog updated to version $newVersion and stored in database.");
// } else {
//     Logger::logLocal("Failed to retrieve new version from Python script.");
// }

// // Get commit messages since last database entry
// $output = shell_exec("git log --pretty=format:'%H|||%s' --since='1 day ago'");
// $commits = explode("\n", trim($output));

// $dbconf = new appConfig;
// $serverName = $dbconf->serverName;
// $database = $dbconf->database;
// $uid = $dbconf->uid;
// $pwd = $dbconf->pwd;

// try {
//     $conn = new PDO("sqlsrv:Server=$serverName;Database=$database;ConnectionPooling=0;TrustServerCertificate=true", $uid, $pwd);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//     // Get the latest commit hash in the database
//     $stmt = $conn->query("SELECT TOP 1 sCommitHash FROM app_commits ORDER BY dtCreated DESC");
//     $lastCommitInDB = $stmt->fetchColumn();

//     // Insert new commits
//     $newCommitMessages = [];
//     $stmt = $conn->prepare("INSERT INTO app_commits (sCommitHash, sCommitMessage) VALUES (?, ?)");

//     foreach ($commits as $commit) {
//         if (empty($commit)) continue;

//         list($hash, $message) = explode("|||", $commit, 2);

//         // Skip if this commit is already in DB or is a changelog update
//         if ($hash === $lastCommitInDB || strpos($message, "Updated changelog") !== false) {
//             continue;
//         }

//         $stmt->execute([$hash, $message]);
//         $newCommitMessages[] = "- " . $message;
//     }

//     // If we have new commits, update version and changelog
//     if (!empty($newCommitMessages)) {
//         // Get current version from changelog
//         $pythonOutput = shell_exec("python3 get_current_version.py");
//         $currentVersion = trim($pythonOutput);

//         // Increment version
//         list($major, $minor, $patch) = explode(".", $currentVersion);
//         $newVersion = "$major.$minor." . ((int)$patch + 1);

//         // Update changelog
//         $commitMessagesText = implode("\n", $newCommitMessages);
//         $updateOutput = shell_exec("python3 update_changelog.py \"$newVersion\" \"$commitMessagesText\"");

//         // Store new version in database
//         $stmt = $conn->prepare("INSERT INTO app_version_control (sVersion, sNotes) VALUES (?, ?)");
//         $stmt->execute([$newVersion, $commitMessagesText]);

//         Logger::LogLocal("Changelog updated to version $newVersion and stored in database.");
//     } else {
//         Logger::logLocal("No new commits to add to changelog.");
//     }
// } catch (PDOException $e) {
//     Logger::logLocal('Error in update_and_commit.php: ' . $e->getMessage());
// }
include_once '../../config.php';
include_once APP_ROOT . '/classes/Logger.php';
include_once APP_ROOT . '/data/appConfig.php';

$start = microtime(true);
$output = shell_exec("python3 update_changelog_simple.py 2>&1");
$end = microtime(true);

Logger::logLocal("Python script execution time: " . ($end - $start) . " seconds");
Logger::logLocal("Raw Python output: '$output'");

// Parse the output
$parts = explode('|', $output, 3);
if (count($parts) >= 2) {
    $version = trim($parts[0]);
    $status = trim($parts[1]);
    $notes = isset($parts[2]) ? trim($parts[2]) : '';

    Logger::logLocal("Version: '$version', Status: '$status', Notes length: " . strlen($notes));

    // Only insert into database if we have a successful update
    if ($status === 'SUCCESS') {
        $dbconf = new appConfig;
        $serverName = $dbconf->serverName;
        $database = $dbconf->database;
        $uid = $dbconf->uid;
        $pwd = $dbconf->pwd;

        try {
            $conn = new PDO("sqlsrv:Server=$serverName;Database=$database;ConnectionPooling=0;TrustServerCertificate=true", $uid, $pwd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Make sure notes aren't too long for the database field
            $maxNotesLength = 1000; // Set this to match your database field size
            if (strlen($notes) > $maxNotesLength) {
                $notes = substr($notes, 0, $maxNotesLength - 3) . '...';
            }

            $sql = "INSERT INTO app_version_control (sVersion, sNotes) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$version, $notes]);

            Logger::logLocal("Changelog updated to version $version and stored in database.");
        } catch (PDOException $e) {
            Logger::logLocal('Error inserting into database: ' . $e->getMessage());
        }
    } else if ($status === 'NOCHANGE') {
        Logger::logLocal("No changelog update needed. No new commits.");
    } else {
        Logger::logLocal("Failed to update changelog: $notes");
    }
} else {
    Logger::logLocal("Failed to parse Python script output: '$output'");
}