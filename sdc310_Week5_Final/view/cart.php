<?php
session_start();
require_once __DIR__ . '/../model/db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove an item when its Remove button is pressed.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $productId = filter_var($_POST['remove_id'], FILTER_VALIDATE_INT);

    if ($productId !== false && $productId > 0) {
        unset($_SESSION['cart'][$productId]);
    }

    header('Location: cart.php');
    exit;
}

// Load products and index them by their database ID.
$result = $conn->query("
    SELECT
        `Product ID` AS product_id,
        `Product Name` AS product_name,
        `Product Cost` AS price
    FROM `products`
");

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[(int) $row['product_id']] = $row;
}

$subtotal = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Shopping Cart</title>
    <style>
        body {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 16px;
            font-family: Arial, sans-serif;
            color: #202a3a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border: 1px solid #d5dce5;
            text-align: left;
        }

        th {
            background: #f0f4f9;
        }

        button, .continue-link {
            display: inline-block;
            padding: 9px 14px;
            border: 0;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
        }

        button {
            background: #c62828;
            color: white;
        }

        .continue-link {
            background: #2563eb;
            color: white;
        }

        .total {
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Review Your Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <p>Review your items below. Remove anything you no longer want.</p>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Line total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $id => $quantity): ?>
                    <?php
                    $id = (int) $id;
                    $quantity = (int) $quantity;

                    if (!isset($products[$id]) || $quantity < 1) {
                        continue;
                    }

                    $product = $products[$id];
                    $price = (float) $product['price'];
                    $lineTotal = $price * $quantity;
                    $subtotal += $lineTotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td>$<?= number_format($price, 2) ?></td>
                        <td><?= $quantity ?></td>
                        <td>$<?= number_format($lineTotal, 2) ?></td>
                        <td>
                            <form method="post" action="cart.php">
                                <button type="submit" name="remove_id" value="<?= $id ?>">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        $discount = $subtotal * 0.05;
        $total = $subtotal - $discount;
        ?>

        <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
        <p>5% discount: −$<?= number_format($discount, 2) ?></p>
        <p class="total">Total: $<?= number_format($total, 2) ?></p>
    <?php endif; ?>

    <p><a class="continue-link" href="index.php">Continue Shopping</a></p>
</body>
</html>