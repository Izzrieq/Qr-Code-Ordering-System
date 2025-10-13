<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Sup Meletup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
    body {
        background-color: #fffbea;
        font-family: 'Poppins', sans-serif;
    }

    .navbar {
        background-color: #ffc107 !important;
    }

    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.25s, box-shadow 0.25s;
        cursor: pointer;
        background-color: #fff;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .icon-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #fff3cd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #ff9800;
        margin: 0 auto 15px;
    }

    h5 {
        font-weight: 600;
        color: #333;
    }

    p {
        color: #6c757d;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-light shadow-sm px-3">
        <a class="navbar-brand fw-bold text-dark">Sup Meletup Admin Panel</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3">Welcome, <strong><?php echo $_SESSION['admin_username']; ?></strong></span>
            <a href="logout.php" class="btn btn-outline-dark btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="text-center fw-bold text-secondary mb-4">Admin Dashboard</h3>
        <div class="row g-4 justify-content-center">

            <div class="col-md-4 col-sm-6">
                <a href="manage_menu.php" class="text-decoration-none text-dark">
                    <div class="card text-center p-4">
                        <div class="icon-circle">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h5>Manage Menu</h5>
                        <p class="small mb-0">Add, edit, and remove menu items.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="manage_orders.php" class="text-decoration-none text-dark">
                    <div class="card text-center p-4">
                        <div class="icon-circle">
                            <i class="bi bi-basket-fill"></i>
                        </div>
                        <h5>Manage Orders</h5>
                        <p class="small mb-0">View and update customer orders.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="payment.php" class="text-decoration-none text-dark">
                    <div class="card text-center p-4">
                        <div class="icon-circle">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h5>Payment</h5>
                        <p class="small mb-0">Generate bills and handle payments.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="generate_qr.php" class="text-decoration-none text-dark">
                    <div class="card text-center p-4">
                        <div class="icon-circle">
                            <i class="bi bi-qr-code"></i>
                        </div>
                        <h5>Generate QR</h5>
                        <p class="small mb-0">Create QR codes for each table.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-sm-6">
                <a href="sales_record.php" class="text-decoration-none text-dark">
                    <div class="card text-center p-4">
                        <div class="icon-circle">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5>Sales Record</h5>
                        <p class="small mb-0">View completed orders and reports.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

</body>

</html>