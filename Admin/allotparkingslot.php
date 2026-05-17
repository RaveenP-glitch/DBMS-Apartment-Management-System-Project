<?php
require_once __DIR__ . '/../config.php';

if (isset($_POST['slotNumber'], $_POST['employeeId'])) {
    $slotNumber = $_POST['slotNumber'];
    $employeeId = (int) $_POST['employeeId'];

    $sql = "INSERT INTO parking (employee_id, parking_slot) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $employeeId, $slotNumber);

    if ($stmt->execute() === true) {
        echo "Parking slot allotted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: slotNumber or employeeId is not set";
}
