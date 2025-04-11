<?php
include_once __DIR__ . '/../classes/Logger.php';

$logFiles = [
    __DIR__ . '/../logs/API_log.txt',
    __DIR__ . '/../logs/error_log.txt',
    __DIR__ . '/../logs/auth_log.txt',
    __DIR__ . '/../logs/info_log.txt',
];
$archiveDir = __DIR__ . '/../log_archives';
$daysToKeep = 7;
$archiveRetention = 30;

if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0755, true);
}

$threshold = strtotime("-{$daysToKeep} days");

foreach ($logFiles as $logFile) {
    if (!file_exists($logFile)) continue;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $recentEntries = [];
    $archiveEntries = [];
    $currentEntry = "";

    foreach ($lines as $line) {
        if (preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $line)) {
            // New log entry starts — decide where the previous one goes
            if (!empty($currentEntry)) {
                $entryDate = strtotime(substr($currentEntry, 1, 19));
                if ($entryDate < $threshold) {
                    $archiveEntries[] = $currentEntry;
                } else {
                    $recentEntries[] = $currentEntry;
                }
            }
            $currentEntry = $line; // Start a new log entry
        } else {
            $currentEntry .= "\n" . $line; // Append to current log entry
        }
    }

    // Handle the final log entry
    if (!empty($currentEntry)) {
        $entryDate = strtotime(substr($currentEntry, 1, 19));
        if ($entryDate < $threshold) {
            $archiveEntries[] = $currentEntry;
        } else {
            $recentEntries[] = $currentEntry;
        }
    }

    // Archive old entries
    if (!empty($archiveEntries)) {
        $archiveFile = $archiveDir . '/' . basename($logFile) . '_' . date('Y-m-d') . '.txt';
        file_put_contents($archiveFile, implode("\n", $archiveEntries) . "\n", FILE_APPEND);
    }

    // Overwrite original file with recent entries
    file_put_contents($logFile, implode("\n", $recentEntries) . "\n");
}

// Clean up old archive files
foreach (glob("$archiveDir/*.txt") as $file) {
    if (filemtime($file) < strtotime("-{$archiveRetention} days")) {
        unlink($file);
    }
}
Logger::logAPI('Log Files Cleaned and Archived Successfully');
//echo "Log files cleaned and archived successfully.\n";