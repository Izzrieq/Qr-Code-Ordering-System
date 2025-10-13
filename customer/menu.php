<?php
include '../database/db_connect.php';
$table = isset($_GET['table']) ? htmlspecialchars($_GET['table']) : 'Unknown';

$result = $conn->query("SELECT * FROM menu ORDER BY type, name");
$menus_by_type = [];
while ($row = $result->fetch_assoc()) {
    $menu_id = $row['menu_id'];
    $variants_res = $conn->query("SELECT * FROM menu_variants WHERE menu_id='$menu_id'");
    $variants = [];
    while ($v = $variants_res->fetch_assoc()) {
        $variants[] = $v;
    }
    $row['variants'] = $variants;
    $menus_by_type[$row['type']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Our Menu | Sup Meletup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    #menu-sections {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 0.5rem;
    }

    #menu-sections .nav-link {
        display: inline-block;
        margin-right: 0.5rem;
        cursor: pointer;
    }

    #menu-sections .nav-link.active {
        background-color: #ffc107;
        color: #000 !important;
    }

    #menu-sections::-webkit-scrollbar {
        display: none;
    }
    </style>
</head>

<body class="bg-light" data-bs-spy="scroll" data-bs-target="#menu-sections" data-bs-offset="100" tabindex="0">

    <nav class="navbar navbar-light bg-warning sticky-top shadow-sm px-3">
        <a class="navbar-brand fw-bold">Sup Meletup</a>
        <div class="ms-auto d-flex align-items-center">
            <span class="me-3 fw-semibold">Table: <span class="text-white"><?php echo $table; ?></span></span>
            <button class="btn btn-light position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                🛒
                <span id="cart-count"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </button>
        </div>
    </nav>

    <div class="container mt-4">
        <h3 class="text-center mb-2 text-secondary">Browse Our Menu</h3>

        <ul class="nav nav-pills justify-content-center mb-4" id="menu-sections">
            <?php foreach (array_keys($menus_by_type) as $type): ?>
            <li class="nav-item">
                <a class="nav-link" href="#<?php echo strtolower(str_replace(' ', '-', $type)); ?>">
                    <?php echo htmlspecialchars($type); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if (empty($menus_by_type)): ?>
        <div class="alert alert-info text-center">No menu items found.</div>
        <?php else: ?>
        <?php foreach ($menus_by_type as $type => $menus): ?>
        <h4 id="<?php echo strtolower(str_replace(' ', '-', $type)); ?>"
            class="mt-5 mb-3 border-bottom pb-2 text-uppercase text-secondary">
            <?php echo htmlspecialchars($type); ?>
        </h4>
        <div class="row">
            <?php foreach ($menus as $row): ?>
            <div class="col-md-4 col-12 mb-4">
                <div class="card h-100 text-center shadow-sm">
                    <?php if (!empty($row['image'])): ?>
                    <img src="../admin/uploads/menu_images/<?php echo $row['image']; ?>" class="card-img-top"
                        style="height:230px;object-fit:cover;">
                    <?php else: ?>
                    <img src="assets/img/no-image.png" class="card-img-top" alt="No image available">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text small text-muted"><?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>RM <?php echo number_format($row['price'], 2); ?></strong></p>

                        <?php if (!empty($row['variants'])): ?>
                        <div class="mb-2">
                            <select class="form-select form-select-sm variant-select"
                                data-menu-id="<?php echo $row['menu_id']; ?>" onchange="updateVariantPrice(this)">
                                <option value="0" data-price="<?php echo $row['price']; ?>">-- Choose Variant --
                                </option>
                                <?php foreach ($row['variants'] as $v): ?>
                                <option value="<?php echo $v['variant_id']; ?>"
                                    data-price="<?php echo $row['price'] + $v['price_diff']; ?>">
                                    <?php echo htmlspecialchars($v['name']); ?> (+RM
                                    <?php echo number_format($v['price_diff'],2); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <button class="btn btn-warning w-100"
                            onclick="addToCart(<?php echo $row['menu_id']; ?>, '<?php echo htmlspecialchars(addslashes($row['name'])); ?>', <?php echo $row['price']; ?>)">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Your Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="past-orders">
                        <h6 class="text-muted">Past Orders</h6>
                        <div id="past-orders-list" class="mb-3"></div>
                    </div>
                    <hr>
                    <h6 class="text-muted">New Order</h6>
                    <table class="table align-middle text-center" id="cart-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Variant</th>
                                <th>Qty</th>
                                <th>Price (RM)</th>
                                <th>Total (RM)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="text-end">
                        <h5>Total: RM <span id="cart-total">0.00</span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <button class="btn btn-success" onclick="confirmOrder()">Confirm Order</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const variantPrices = {};
    updateCartDisplay();

    function updateVariantPrice(select) {
        const menuId = select.dataset.menuId;
        const selectedOption = select.selectedOptions[0];
        const price = parseFloat(selectedOption.dataset.price);
        variantPrices[menuId] = {
            id: selectedOption.value,
            price: price,
            name: selectedOption.text
        };
    }

    function addToCart(id, name, basePrice) {
        let price = basePrice;
        let variant = null;
        if (variantPrices[id]) {
            price = variantPrices[id].price;
            variant = variantPrices[id].name;
        }
        const item = cart.find(i => i.id === id && i.variant === variant);
        if (item) item.qty++;
        else cart.push({
            id,
            name,
            variant,
            price,
            qty: 1
        });
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
        Swal.fire({
            icon: 'success',
            title: name + (variant ? ' (' + variant + ')' : '') + ' added to cart!',
            showConfirmButton: false,
            timer: 1000
        });
    }

    function updateCartDisplay() {
        const tbody = document.querySelector("#cart-table tbody");
        const countBadge = document.getElementById("cart-count");
        const totalDisplay = document.getElementById("cart-total");
        tbody.innerHTML = "";
        let total = 0,
            count = 0;
        cart.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            total += subtotal;
            count += item.qty;
            tbody.innerHTML += `<tr>
            <td>${item.name}</td>
            <td>${item.variant || '-'}</td>
            <td><input type="number" min="1" value="${item.qty}" class="form-control form-control-sm"
            onchange="updateQuantity(${index}, this.value)"></td>
            <td>${item.price.toFixed(2)}</td>
            <td>${subtotal.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-danger" onclick="removeItem(${index})">X</button></td>
        </tr>`;
        });
        totalDisplay.textContent = total.toFixed(2);
        countBadge.textContent = count;
    }

    function updateQuantity(index, qty) {
        cart[index].qty = parseInt(qty);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }

    async function confirmOrder() {
        if (cart.length === 0) {
            Swal.fire('Cart is empty!', '', 'warning');
            return;
        }
        const table = "<?php echo $table; ?>";
        const response = await fetch('order_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                table,
                cart
            })
        });
        const result = await response.json();
        if (result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Order sent!',
                text: 'Your order has been sent to the kitchen.',
                showConfirmButton: false,
                timer: 1500
            });
            cart = [];
            localStorage.removeItem('cart');
            updateCartDisplay();
            loadPastOrders();
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    }

    async function loadPastOrders() {
        const res = await fetch(`get_orders.php?table=<?php echo urlencode($table); ?>`);
        const data = await res.json();
        const container = document.getElementById('past-orders-list');
        container.innerHTML = "";
        if (data.length === 0) {
            container.innerHTML = '<div class="text-muted">No past orders yet.</div>';
            return;
        }
        data.forEach(order => {
            const badgeClass = order.status === 'Served' ? 'bg-success' : order.status === 'Preparing' ?
                'bg-warning text-dark' : 'bg-secondary';
            container.innerHTML += `<div class="d-flex justify-content-between border-bottom py-1">
            <div>${order.menu_name} ${order.variant ? '('+order.variant+')':''} (RM ${order.price}) x${order.quantity}</div>
            <span class="badge ${badgeClass}">${order.status}</span>
        </div>`;
        });
    }

    loadPastOrders();
    setInterval(loadPastOrders, 10000);
    </script>

</body>

</html>