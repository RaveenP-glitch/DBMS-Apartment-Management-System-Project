<?php
require_once __DIR__ . '/../config.php';

$sql = "SELECT COUNT(*) AS total_employees FROM employee";
$result = $conn->query($sql);

$data = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data["totalEmployees"] = $row["total_employees"];
    }
}

echo json_encode($data);
