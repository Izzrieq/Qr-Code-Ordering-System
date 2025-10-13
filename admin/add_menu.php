<?php
include '../database/db_connect.php';

if (isset($_POST['add_menu'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $folder = __DIR__ . "/uploads/menu_images/";
    if (!file_exists($folder)) mkdir($folder, 0777, true);

    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Only JPG, JPEG, PNG, or GIF files are allowed!');</script>";
        exit;
    }

    $unique_name = time() . "_" . basename($image);
    $target_path = $folder . $unique_name;

    if (move_uploaded_file($tmp_name, $target_path)) {
        $sql = "INSERT INTO menu (name, description, price, type, image)
                VALUES ('$name', '$desc', '$price', '$type', '$unique_name')";

        if ($conn->query($sql)) {
            $menu_id = $conn->insert_id;

            if (!empty($_POST['variant_name'])) {
                $names = $_POST['variant_name'];
                $prices = $_POST['variant_price'];

                for ($i = 0; $i < count($names); $i++) {
                    $v_name = $conn->real_escape_string($names[$i]);
                    $v_price = floatval($prices[$i]);
                    $conn->query("INSERT INTO menu_variants (menu_id, name, price_diff) VALUES ($menu_id, '$v_name', $v_price)");
                }
            }

            echo "<script>alert('Menu item added successfully with variants!'); window.location='manage_menu.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>Database error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Image upload failed! Please check folder permissions.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Menu Item | Sup Meletup</title>
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
    </style>
</head>

<body>
    <nav class="navbar navbar-light shadow-sm px-3">
        <a class="navbar-brand fw-bold text-dark">Sup Meletup Admin Panel</a>
        <div class="ms-auto">
            <a href="manage_menu.php" class="btn btn-outline-dark fw-semibold">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="card p-4">
            <h3 class="fw-bold text-secondary mb-4 text-center"><i class="bi bi-plus-circle"></i> Add New Menu Item</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Menu Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (RM)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Menu Type</label>
                    <select name="type" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <option value="Signature Dish">Signature Dish</option>
                        <option value="Beverage">Beverage</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Side Dish">Side Dish</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variants (optional)</label>
                    <div id="variants-container"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addVariant()">
                        <i class="bi bi-plus-circle"></i> Add Variant
                    </button>
                </div>

                <button type="submit" name="add_menu" class="btn btn-warning w-100 fw-semibold">
                    <i class="bi bi-save"></i> Save Menu Item
                </button>
            </form>
        </div>
    </div>

    <script>
    let variantCount = 0;

    function addVariant() {
        variantCount++;
        const container = document.getElementById('variants-container');
        const div = document.createElement('div');
        div.classList.add('mb-2', 'd-flex', 'gap-2');
        div.innerHTML = `
            <input type="text" name="variant_name[]" class="form-control" placeholder="Variant Name" required>
            <input type="number" step="0.01" name="variant_price[]" class="form-control" placeholder="Price Diff (RM)" value="0.00">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">❌</button>
        `;
        container.appendChild(div);
    }
    </script>
</body>

</html>