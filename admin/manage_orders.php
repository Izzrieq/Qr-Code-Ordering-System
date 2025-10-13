<?php
include '../database/db_connect.php';

if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $conn->query("UPDATE orders SET status='$status' WHERE order_id=$order_id");
    exit;
}

$result = $conn->query("SELECT * FROM orders ORDER BY table_no, order_time DESC");
$orders_by_table = [];
while ($row = $result->fetch_assoc()) {
    $orders_by_table[$row['table_no']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Orders | Sup Meletup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
    body {
        background-color: #f8f9fa;
    }

    .navbar {
        background-color: #ffc107 !important;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        font-weight: 600;
        font-size: 1.1rem;
    }

    th,
    td {
        vertical-align: middle !important;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-light shadow-sm px-3">
        <a class="navbar-brand fw-bold text-dark">Sup Meletup Admin Panel</a>
        <div class="ms-auto">
            <a href="admin_dashboard.php" class="btn btn-outline-dark fw-semibold">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <h3 class="fw-bold text-secondary mb-4 text-center">Kitchen Order Management</h3>

        <?php if (empty($orders_by_table)): ?>
        <div class="alert alert-info text-center">No orders received yet.</div>
        <?php else: ?>
        <?php foreach ($orders_by_table as $table => $orders): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="m-0"><i class="bi bi-person-fill"></i> Table: <?php echo htmlspecialchars($table); ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered m-0 text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Menu</th>
                                <th>Qty</th>
                                <th>Price (RM)</th>
                                <th>Total (RM)</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 1; 
                            foreach ($orders as $order): 
                            ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($order['menu_name']); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td><?php echo number_format($order['price'], 2); ?></td>
                                <td><?php echo number_format($order['total'], 2); ?></td>
                                <td>
                                    <select class="form-select form-select-sm text-center fw-semibold"
                                        onchange="updateStatus(<?php echo $order['order_id']; ?>, this.value)">
                                        <option value="Pending"
                                            <?php if ($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Preparing"
                                            <?php if ($order['status']=='Preparing') echo 'selected'; ?>>Preparing
                                        </option>
                                        <option value="Served"
                                            <?php if ($order['status']=='Served') echo 'selected'; ?>>Served</option>
                                    </select>
                                </td>
                                <td><?php echo date('h:i A', strtotime($order['order_time'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <audio id="newOrderSound" src="assets/sound/new_order.wav" preload="auto"></audio>

    <script>
    function updateStatus(orderId, status) {
        $.post('manage_orders.php', {
            update_status: true,
            order_id: orderId,
            status: status
        });
    }

    setInterval(() => {
        location.reload();
    }, 5000);
    </script>
</body>

</html>