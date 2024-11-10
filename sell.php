<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

include 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticker = $_POST['ticker'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    // Check if the user has the stock in their portfolio
    $stmt = $conn->prepare("SELECT * FROM Portfolio WHERE user_id = :user_id AND ticker = :ticker");
    $stmt->execute(['user_id' => $user_id, 'ticker' => $ticker]);
    $portfolio_item = $stmt->fetch();

    if ($portfolio_item && $portfolio_item['quantity'] >= $quantity) {
        // Insert the sell transaction
        $stmt = $conn->prepare("INSERT INTO Transactions (user_id, ticker, price, quantity, type) VALUES (:user_id, :ticker, :price, :quantity, 'sell')");
        $stmt->execute([
            'user_id' => $user_id,
            'ticker' => $ticker,
            'price' => $price,
            'quantity' => $quantity
        ]);

        // Update portfolio quantity
        $new_quantity = $portfolio_item['quantity'] - $quantity;
        if ($new_quantity > 0) {
            $stmt = $conn->prepare("UPDATE Portfolio SET quantity = :quantity WHERE user_id = :user_id AND ticker = :ticker");
            $stmt->execute(['quantity' => $new_quantity, 'user_id' => $user_id, 'ticker' => $ticker]);
        } else {
            // Remove stock from portfolio if quantity is zero
            $stmt = $conn->prepare("DELETE FROM Portfolio WHERE user_id = :user_id AND ticker = :ticker");
            $stmt->execute(['user_id' => $user_id, 'ticker' => $ticker]);
        }

        $message = "Stock sold successfully!";
    } else {
        $message = "You do not own enough of this stock to sell.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Stock</title>
    <script>
        // JavaScript function to update the live chart link based on stock ticker input
        function updateChartLink() {
            const tickerInput = document.getElementById('ticker').value;
            const chartLink = document.getElementById('chartLink');

            if (tickerInput) {
                chartLink.href = `https://in.tradingview.com/chart/?symbol=${tickerInput.toUpperCase()}`;
                chartLink.textContent = `View ${tickerInput.toUpperCase()} Live Chart`;
                chartLink.style.display = 'inline';
            } else {
                chartLink.style.display = 'none';
            }
        }
    </script>

<style>
        /* Green button for Buy */
        button[type="submit"] {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: red;
        }

        .name{
            color:white;
        }
        a {
            color: blue;
            text-decoration: underline;
        }
        .bg{
            background-image:url("https://media.licdn.com/dms/image/D4D12AQGZEFxxX2Dzrg/article-cover_image-shrink_600_2000/0/1694885800616?e=2147483647&v=beta&t=xIb8ep2dFt00qXLBqHbkflHNTszUQ7mtbqsRFmg0eiM");
            background-size:cover;
        }
    </style>
</head>
<body class="bg">
    <h2 class="name">Sell Stock</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="sell.php">
        <label for="ticker" class="name">Stock Ticker:</label>
        <input type="text" id="ticker" name="ticker" oninput="updateChartLink()" required><br><br>

        <a id="chartLink" href="#" target="_blank" style="display: none; color: blue; text-decoration: underline;"></a><br><br>

        <label for="price" class="name">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br><br>

        <label for="quantity" class="name">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <button type="submit">Sell</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>