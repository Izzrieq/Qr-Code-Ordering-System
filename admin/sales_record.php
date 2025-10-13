<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../database/db_connect.php';

$result = $conn->query("
    SELECT * FROM table_record
    ORDER BY payment_time DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Record | Sup Meletup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
    }

    th,
    td {
        vertical-align: middle !important;
    }

    .table thead th {
        background-color: #343a40;
        color: white;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-light shadow-sm px-3">
        <a class="navbar-brand fw-bold text-dark">Sup Meletup Admin Panel</a>
        <div class="ms-auto d-flex gap-2">
            <a href="admin_dashboard.php" class="btn btn-outline-dark fw-semibold">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-secondary"><i class="bi bi-clipboard-data"></i> Sales Record</h3>
                <button class="btn btn-success fw-semibold" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
            </div>

            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Table No</th>
                            <th>Subtotal (RM)</th>
                            <th>SST (6%)</th>
                            <th>Service Charge (10%)</th>
                            <th>Total (RM)</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $grandTotalAll = 0;
                            while ($row = $result->fetch_assoc()): 
                                $grandTotalAll += $row['total'];
                        ?>
                        <tr>
                            <td><?php echo $row['record_id']; ?></td>
                            <td><span
                                    class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['table_no']); ?></span>
                            </td>
                            <td><?php echo number_format($row['subtotal'], 2); ?></td>
                            <td><?php echo number_format($row['sst'], 2); ?></td>
                            <td><?php echo number_format($row['service_charge'], 2); ?></td>
                            <td class="fw-bold text-success"><?php echo number_format($row['total'], 2); ?></td>
                            <td><?php echo date("d/m/Y h:i A", strtotime($row['payment_time'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total Sales:</th>
                            <th class="text-success fw-bold">RM <?php echo number_format($grandTotalAll, 2); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">No sales records found.</div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>