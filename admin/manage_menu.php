<?php
include '../database/db_connect.php';

if (isset($_GET['delete'])) {
    $menu_id = $_GET['delete'];

    $result = $conn->query("SELECT image FROM menu WHERE menu_id='$menu_id'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = __DIR__ . "/uploads/menu_images/" . $row['image'];
        if (file_exists($imagePath)) unlink($imagePath);
    }

    $conn->query("DELETE FROM menu WHERE menu_id='$menu_id'");
    $conn->query("DELETE FROM menu_variants WHERE menu_id='$menu_id'");

    echo "<script>alert('Menu deleted successfully!'); window.location='manage_menu.php';</script>";
    exit;
}

if (isset($_POST['update_menu'])) {
    $menu_id = $_POST['menu_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $image_update_sql = "";
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $folder = __DIR__ . "/uploads/menu_images/";
        $unique_name = time() . "_" . basename($image);
        $target_path = $folder . $unique_name;

        if (move_uploaded_file($tmp_name, $target_path)) {
            $old = $conn->query("SELECT image FROM menu WHERE menu_id='$menu_id'")->fetch_assoc();
            $oldPath = $folder . $old['image'];
            if (file_exists($oldPath)) unlink($oldPath);
            $image_update_sql = ", image='$unique_name'";
        }
    }

    $sql = "UPDATE menu 
            SET name='$name', type='$type', description='$desc', price='$price' $image_update_sql 
            WHERE menu_id='$menu_id'";

    if ($conn->query($sql)) {
        echo "<script>alert('Menu updated successfully!'); window.location='manage_menu.php';</script>";
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
    <title>Manage Menu | Sup Meletup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    th {
        vertical-align: middle !important;
    }

    .variant-badge {
        margin: 2px;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-light shadow-sm px-3">
        <a class="navbar-brand fw-bold text-dark">Sup Meletup Admin Panel</a>
        <div class="ms-auto d-flex gap-2">
            <a href="admin_dashboard.php" class="btn btn-outline-dark fw-semibold">← Back</a>
            <a href="add_menu.php" class="btn btn-light fw-semibold">+ Add New Menu</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="card p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-secondary">Manage Menu</h3>
            </div>

            <?php
            $result = $conn->query("SELECT * FROM menu ORDER BY menu_id DESC");
            if ($result->num_rows > 0):
            ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Price (RM)</th>
                            <th>Variants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()):
                            $menu_id = $row['menu_id'];
                            $variants = $conn->query("SELECT * FROM menu_variants WHERE menu_id='$menu_id'");
                        ?>
                        <tr>
                            <td><?php echo $menu_id; ?></td>
                            <td>
                                <img src="uploads/menu_images/<?php echo htmlspecialchars($row['image']); ?>" width="70"
                                    height="70" class="rounded" style="object-fit:cover;">
                            </td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><span
                                    class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['type']); ?></span>
                            </td>
                            <td class="text-muted small"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><strong><?php echo number_format($row['price'], 2); ?></strong></td>
                            <td>
                                <?php if ($variants->num_rows > 0):
                                    while ($v = $variants->fetch_assoc()): ?>
                                <span class="badge bg-info text-dark variant-badge">
                                    <?php echo htmlspecialchars($v['name']) . ' (+' . number_format($v['price_diff'],2) . ')'; ?>
                                </span>
                                <?php endwhile; else: ?>
                                <span class="text-muted small">No variants</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $menu_id; ?>">Edit</button>
                                <a href="manage_menu.php?delete=<?php echo $menu_id; ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this menu item?');">Delete</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?php echo $menu_id; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit Menu:
                                                <?php echo htmlspecialchars($row['name']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">

                                            <div class="mb-3">
                                                <label class="fw-semibold">Menu Name</label>
                                                <input type="text" name="name"
                                                    value="<?php echo htmlspecialchars($row['name']); ?>"
                                                    class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-semibold">Menu Type</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="">-- Select Type --</option>
                                                    <option value="Signature Dish"
                                                        <?= $row['type']=='Signature Dish'?'selected':''; ?>>Signature
                                                        Dish</option>
                                                    <option value="Beverage"
                                                        <?= $row['type']=='Beverage'?'selected':''; ?>>Beverage</option>
                                                    <option value="Dessert"
                                                        <?= $row['type']=='Dessert'?'selected':''; ?>>Dessert</option>
                                                    <option value="Side Dish"
                                                        <?= $row['type']=='Side Dish'?'selected':''; ?>>Side Dish
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-semibold">Description</label>
                                                <textarea name="description" class="form-control"
                                                    rows="3"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-semibold">Price (RM)</label>
                                                <input type="number" step="0.01" name="price"
                                                    value="<?php echo $row['price']; ?>" class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-semibold">Change Image (optional)</label>
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                <div class="mt-2">
                                                    <small class="text-muted">Current:</small><br>
                                                    <img src="uploads/menu_images/<?php echo htmlspecialchars($row['image']); ?>"
                                                        width="100" class="rounded">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_menu" class="btn btn-warning">Save
                                                Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">
                No menu items found. Click “<strong>Add New Menu</strong>” to create one.
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>