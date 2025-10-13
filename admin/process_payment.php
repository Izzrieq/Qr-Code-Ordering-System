<?php
include '../database/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

$table = $data['table'];
$subtotal = $data['subtotal'];
$sst = $data['sst'];
$service = $data['service'];
$total = $data['total'];

$res = $conn->query("SELECT menu_name, quantity, total FROM orders WHERE table_no='$table'");
$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}
$items_json = json_encode($items);

$sql = "INSERT INTO table_record (table_no, items, subtotal, sst, service_charge, total)
        VALUES ('$table', '$items_json', '$subtotal', '$sst', '$service', '$total')";

if ($conn->query($sql)) {
    $conn->query("DELETE FROM orders WHERE table_no='$table'");
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
?>