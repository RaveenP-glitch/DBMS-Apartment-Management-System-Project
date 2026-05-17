<?php
require_once __DIR__ . '/../config.php';

$sql = "SELECT * FROM employee";
$result = $conn->query($sql);

$employees = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($employees);
