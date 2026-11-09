<?php
session_start();
require_once __DIR__ . '/../model/db.php';

$sql = "
    SELECT
        `Product ID` AS product_id,
        `Product Name` AS product_name,
        `Product Description` AS description,
        `Product Cost` AS price,
        `Quantity currently in the cart` AS stock
    FROM `products`
";

$result = $conn->query($sql);

if (!$result) {
    die('Product query failed: ' . htmlspecialchars($conn->error));
}

$cartCount = array_sum($_SESSION['cart'] ?? []);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Our Products</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 20px;
            background: #f3f6fb;
            color: #202a3a;
            font-family: Arial, sans-serif;
        }

        .page {
            max-width: 1100px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            color: #172554;
        }

        .cart-link {
            padding: 10px 16px;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .cart-link:hover {
            background: #1d4ed8;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .product-card {
            padding: 20px;
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 12px rgba(30, 50, 80, 0.08);
        }

        .product-card h2 {
            margin: 0 0 10px;
            color: #1e3a8a;
            font-size: 20px;
        }

        .description {
            min-height: 48px;
            color: #536176;
            line-height: 1.5;
        }

        .price {
            margin: 16px 0 6px;
            font-size: 22px;
            font-weight: bold;
        }

        .stock {
            margin: 0 0 16px;
            color: #536176;
        }

        .product-card form {
            display: flex;
            gap: 8px;
        }

        input[type="number"] {
            width: 75px;
            padding: 9px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        button {
            flex: 1;
            padding: 10px;
            border: 0;
            border-radius: 6px;
            background: #16a34a;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #15803d;
        }

        .out-of-stock {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="page-header">
            <h1>Our Products</h1>
            <a class="cart-link" href="cart.php">
                View Cart (<?= (int) $cartCount ?>)
            </a>
        </header>

        <section class="products">
            <?php while ($product = $result->fetch_assoc()): ?>
                <article class="product-card">
                    <h2><?= htmlspecialchars($product['product_name']) ?></h2>

                    <p class="description">
                        <?= htmlspecialchars($product['description']) ?>
                    </p>

                    <p class="price">
                        $<?= number_format((float) $product['price'], 2) ?>
                    </p>

                    <p class="stock">
                        In stock: <?= (int) $product['stock'] ?>
                    </p>

                    <?php if ((int) $product['stock'] > 0): ?>
                        <form action="../controller/add_to_cart.php" method="post">
                            <input
                                type="hidden"
                                name="product_id"
                                value="<?= (int) $product['product_id'] ?>"
                            >
                            <input
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                                max="<?= (int) $product['stock'] ?>"
                                required
                            >
                            <button type="submit">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <p class="out-of-stock">Out of stock</p>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </section>
    </main>
</body>
</html>