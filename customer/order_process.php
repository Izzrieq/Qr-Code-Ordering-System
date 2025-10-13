<?php
include '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $table = $data['table'];
    $cart = $data['cart'];

    if (empty($cart)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
        exit;
    }

    foreach ($cart as $item) {
        $name = $conn->real_escape_string($item['name']);
        $qty = intval($item['qty']);
        $price = floatval($item['price']);
        $total = $qty * $price;

        $sql = "INSERT INTO orders (table_no, menu_name, quantity, price, total) 
                VALUES ('$table', '$name', '$qty', '$price', '$total')";
        $conn->query($sql);
    }

    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);
}
?>