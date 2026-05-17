<?php
require_once __DIR__ . '/../config.php';

$type = $_GET["type"] ?? '';
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    die("Invalid id");
}

if ($type === "employee") {
    $stmt = $conn->prepare("DELETE FROM employee WHERE employee_id = ?");
} elseif ($type === "tenant") {
    $stmt = $conn->prepare("DELETE FROM tenant WHERE tenant_id = ?");
} elseif ($type === "owner") {
    $stmt = $conn->prepare("DELETE FROM owner WHERE owner_id = ?");
} else {
    die("Invalid type");
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: manage_data.php");
    exit();
}

echo "Error deleting record: " . $stmt->error;
$stmt->close();
