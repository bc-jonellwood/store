<?php


// $output = shell_exec("python3 update_changelog.py 2>&1");
include_once '../../config.php';
include_once APP_ROOT . '/classes/Logger.php';
include_once APP_ROOT . '/data/appConfig.php';

$start = microtime(true);
$output = shell_exec("python3 update_changelog.py 2>&1");
$end = microtime(true);
Logger::logInfo("Raw Python output: '$output' ");
// $newVersion = trim($output);

Logger::logLocal("Python script execution time: " . ($end - $start) . " seconds");

// Split output into lines
$lines = explode("\n", trim($output));

// Extract version (last line)
$newVersion = trim(array_pop($lines));

// Remaining lines are Git info
$sNotes = implode("\n", $lines);

Logger::logLocal("Extracted version: '$newVersion'");
Logger::logLocal("Extracted notes: '$sNotes'");


$dbconf = new appConfig;
$serverName = $dbconf->serverName;
$database = $dbconf->database;
$uid = $dbconf->uid;
$pwd = $dbconf->pwd;

if ($newVersion) {
    Logger::logLocal("Attempting to insert version: '$newVersion' into database.");
    // Step 2: Insert into database
    try {
        $conn = new PDO("sqlsrv:Server=$serverName;Database=$database;ConnectionPooling=0;TrustServerCertificate=true", $uid, $pwd);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $sql = "INSERT INTO app_version_control(sVersion, sNotes) VALUES (:newVersion, :notes)";
        $sql = "INSERT INTO app_version_control (sVersion, sNotes) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        // $stmt->bindParam(':newVersion', $newVersion, PDO::PARAM_STR);
        $stmt->execute([$newVersion, $sNotes]);
    } catch (PDOException $e) {
        Logger::logLocal('Error inserting into database in update_and_commit.php' . $e->getMessage());
    }

    // $stmt->execute([$newVersion, "Automated update"]);

    // Step 3: Log the process
    Logger::LogLocal("Changelog updated to version $newVersion and stored in database.");
} else {
    Logger::logLocal("Failed to retrieve new version from Python script.");
}
