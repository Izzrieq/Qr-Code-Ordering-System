<?php
include '../database/db_connect.php';
$table = $_GET['table'] ?? '';
$result = $conn->query("SELECT * FROM orders WHERE table_no='$table' ORDER BY order_time DESC");
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
echo json_encode($orders);
?>