<?php
include '../database/db_connect.php';
$table = $_GET['table'] ?? '';

if (!$table) {
    die("Invalid table selected.");
}

$result = $conn->query("SELECT * FROM orders WHERE table_no='$table'");
$orders = [];
$subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
    $subtotal += $row['total'];
}

$sst_rate = 0.06;
$service_rate = 0.10;
$sst = $subtotal * $sst_rate;
$service = $subtotal * $service_rate;
$total = $subtotal + $sst + $service;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Summary | Sup Meletup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        text-align: center;
    }

    .table thead th {
        background-color: #343a40;
        color: white;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-light shadow-sm px-3 mb-4">
        <a class="navbar-brand fw-bold text-dark">🍲 Sup Meletup Admin Panel</a>
        <div class="ms-auto">
            <a href="payment.php" class="btn btn-outline-dark fw-semibold">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </nav>

    <div class="container my-4">
        <h3 class="fw-bold text-secondary mb-4 text-center">Table <?php echo htmlspecialchars($table); ?> - Payment
            Summary</h3>

        <?php if (empty($orders)): ?>
        <div class="alert alert-info text-center">No orders found for this table.</div>
        <?php else: ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price (RM)</th>
                                <th>Total (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; foreach ($orders as $o): ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($o['menu_name']); ?></td>
                                <td><?php echo $o['quantity']; ?></td>
                                <td><?php echo number_format($o['price'], 2); ?></td>
                                <td><?php echo number_format($o['total'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <p>Subtotal: RM <?php echo number_format($subtotal, 2); ?></p>
                    <p>SST (6%): RM <?php echo number_format($sst, 2); ?></p>
                    <p>Service Charge (10%): RM <?php echo number_format($service, 2); ?></p>
                    <h5 class="fw-bold text-success">Grand Total: RM <?php echo number_format($total, 2); ?></h5>
                </div>
            </div>
        </div>

        <div class="text-end mb-4">
            <button class="btn btn-success fw-semibold" onclick="finalizePayment()">
                <i class="bi bi-check-circle"></i> Confirm & Print
            </button>
        </div>
        <?php endif; ?>
    </div>

    <script>
    async function finalizePayment() {
        const table = <?php echo json_encode($table); ?>;
        const subtotal = <?php echo $subtotal; ?>;
        const sst = <?php echo $sst; ?>;
        const service = <?php echo $service; ?>;
        const total = <?php echo $total; ?>;

        try {
            const response = await fetch('process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    table,
                    subtotal,
                    sst,
                    service,
                    total
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Completed!',
                    text: 'Bill has been saved and table cleared.',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                    <html>
                        <head>
                            <title>Receipt</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                        </head>
                        <body>
                            <h4>Table ${table} Receipt</h4>
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price (RM)</th>
                                        <th>Total (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $count=1; foreach($orders as $o): ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo htmlspecialchars($o['menu_name']); ?></td>
                                        <td><?php echo $o['quantity']; ?></td>
                                        <td><?php echo number_format($o['price'],2); ?></td>
                                        <td><?php echo number_format($o['total'],2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <p>Subtotal: RM <?php echo number_format($subtotal,2); ?></p>
                            <p>SST: RM <?php echo number_format($sst,2); ?></p>
                            <p>Service: RM <?php echo number_format($service,2); ?></p>
                            <h5>Grand Total: RM <?php echo number_format($total,2); ?></h5>
                        </body>
                    </html>
                `);
                    printWindow.document.close();
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();
                    window.location.href = 'payment.php';
                });
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Something went wrong.', 'error');
            console.error(err);
        }
    }
    </script>

</body>

</html>