<?php
include '../database/db_connect.php';

$query = "
  SELECT table_no, SUM(total) AS subtotal
  FROM orders
  WHERE status != 'Served' OR status = 'Served'
  GROUP BY table_no
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment | Sup Meletup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            <h3 class="fw-bold text-secondary mb-4 text-center">Payment Overview</h3>

            <?php if ($result && $result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Table No</th>
                            <th>Subtotal (RM)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['table_no']); ?></strong></td>
                            <td><?php echo number_format($row['subtotal'], 2); ?></td>
                            <td>
                                <a href="payment_summary.php?table=<?php echo urlencode($row['table_no']); ?>"
                                    class="btn btn-warning fw-semibold">
                                    <i class="bi bi-cash-stack"></i> Proceed to Payment
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">
                No active tables found.
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>