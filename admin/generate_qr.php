<?php
include '../database/db_connect.php';
include 'phpqrcode/qrlib.php';

$qrFolder = __DIR__ . "/qr_codes/";
if (!file_exists($qrFolder)) {
    mkdir($qrFolder, 0777, true);
}

if (isset($_POST['generate_qr'])) {
    $table_number = $_POST['table_number'];
    $ip = getHostbyName(getHostName());

    $link = "http://$ip/QR-ORDERING-SYSTEM-FOR-SUP-MELETUP/customer/menu.php?table=" . urlencode($table_number);

    $filename = "table_" . $table_number . "_" . time() . ".png";
    $filepath = $qrFolder . $filename;

    QRcode::png($link, $filepath, QR_ECLEVEL_L, 6);

    $sql = "INSERT INTO tables (table_number, qr_image) VALUES ('$table_number', '$filename')";
    if ($conn->query($sql)) {
        echo "<script>alert('QR Code for Table $table_number generated successfully!'); window.location='generate_qr.php';</script>";
        exit;
    } else {
        echo "<div style='color:red;'>Database Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate Table QR Codes | Sup Meletup</title>
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
            <h3 class="fw-bold text-secondary mb-4 text-center">Generate Table QR Code</h3>

            <form method="POST" class="bg-white p-4 rounded shadow-sm mb-4">
                <div class="mb-3">
                    <label class="fw-semibold">Enter Table Number</label>
                    <input type="text" name="table_number" class="form-control" placeholder="e.g. Table 1" required>
                </div>
                <button type="submit" name="generate_qr" class="btn btn-warning w-100 fw-semibold">
                    <i class="bi bi-qr-code"></i> Generate QR Code
                </button>
            </form>

            <h4 class="fw-bold text-secondary mb-3">Generated QR Codes</h4>
            <div class="row">
                <?php
        $result = $conn->query("SELECT * FROM tables ORDER BY table_id DESC");
        if ($result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
        ?>
                <div class="col-md-3 col-6 mb-4 text-center">
                    <div class="card shadow-sm p-2">
                        <div class="card-body">
                            <img src="qr_codes/<?php echo $row['qr_image']; ?>" class="img-fluid rounded mb-2"
                                alt="QR Code">
                            <h6 class="fw-bold text-secondary mb-1">
                                <?php echo htmlspecialchars($row['table_number']); ?></h6>
                            <a href="qr_codes/<?php echo $row['qr_image']; ?>" download
                                class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div class="alert alert-info text-center">No QR codes generated yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>