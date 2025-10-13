<?php
include '../database/db_connect.php';
$result = $conn->query("SELECT COUNT(*) AS total FROM orders");
$row = $result->fetch_assoc();
echo json_encode(['total' => $row['total']]);
?>