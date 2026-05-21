<?php
session_start();
header('Content-Type: application/json');

$timeout = 1 * 60 * 60; // 1 * 60 * 60

if (!isset($_SESSION['user'])) {
    echo json_encode(['remaining_seconds' => 0]);
    exit;
}

if (!isset($_SESSION['last_activity'])) {
    echo json_encode(['remaining_seconds' => $timeout]);
    exit;
}

$inactive_seconds = time() - $_SESSION['last_activity'];
$remaining_seconds = $timeout - $inactive_seconds;

if ($remaining_seconds <= 0) {
    echo json_encode(['remaining_seconds' => 0]);
    exit;
}

echo json_encode(['remaining_seconds' => $remaining_seconds]);
?>