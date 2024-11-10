<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'includes/db.php';

// Fetch transactions for the logged-in user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM Transactions WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$transactions = $stmt->fetchAll();

$buy_data = []; // Store data for bought stocks
$profit_loss_data = []; // To store profit/loss calculations

// Process transactions
foreach ($transactions as $transaction) {
    $ticker = $transaction['ticker'];
    $quantity = $transaction['quantity'];
    $price = $transaction['price'];
    $transaction_type = $transaction['type'];

    if ($transaction_type === 'buy') {
        // Add buy transactions to the buy_data array
        if (!isset($buy_data[$ticker])) {
            $buy_data[$ticker] = [];
        }

        $buy_data[$ticker][] = ['quantity' => $quantity, 'price' => $price];
    }

    if ($transaction_type === 'sell') {
        // For each sell transaction, calculate profit/loss
        if (isset($buy_data[$ticker]) && count($buy_data[$ticker]) > 0) {
            $total_buy_price = 0;
            $total_sell_price = 0;
            $sell_quantity = $quantity;

            // Sell stocks, deducting from buy data
            foreach ($buy_data[$ticker] as $key => $buy) {
                if ($sell_quantity <= 0) break; // Stop if all stocks have been sold
                
                $buy_quantity = $buy['quantity'];
                $buy_price = $buy['price'];
                
                // Sell as much as the quantity bought (FIFO - first in, first out)
                $quantity_to_sell = min($buy_quantity, $sell_quantity);
                $total_buy_price += $quantity_to_sell * $buy_price;
                $total_sell_price += $quantity_to_sell * $price;

                // Update remaining buy quantity
                $buy_data[$ticker][$key]['quantity'] -= $quantity_to_sell;
                $sell_quantity -= $quantity_to_sell;
            }

            // Calculate profit/loss for the sold quantity
            $profit_loss = $total_sell_price - $total_buy_price;
            $profit_loss_data[$ticker][] = [
                'quantity' => $quantity,
                'buy_price' => $total_buy_price,
                'sell_price' => $total_sell_price,
                'profit_loss' => $profit_loss
            ];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profit and Loss</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .profit {
            color: green;
        }
        .loss {
            color: red;
        }
        .no-profit-loss {
            color: black;
        }
        .bg{
            background-image:url("https://media.licdn.com/dms/image/D4D12AQGZEFxxX2Dzrg/article-cover_image-shrink_600_2000/0/1694885800616?e=2147483647&v=beta&t=xIb8ep2dFt00qXLBqHbkflHNTszUQ7mtbqsRFmg0eiM");
            background-size:cover;
        }
        .name{
            color:white;
        }
        
        a {
            color: blue;
            text-decoration: underline;
        
        }
    </style>
</head>
<body class="bg">
    <h2 class="name">Profit and Loss Report</h2>

    <table>
        <thead>
            <tr>
                <th class="name">Stock Ticker</th>
                <th class="name">Quantity Sold</th>
                <th class="name">Buy Price</th>
                <th class="name">Sell Price</th>
                <th class="name">Profit/Loss</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_profit_loss = 0;
            if (empty($profit_loss_data)) {
                echo "<tr><td colspan='5'>No transactions to calculate profit or loss.</td></tr>";
            } else {
                foreach ($profit_loss_data as $ticker => $transactions) {
                    foreach ($transactions as $transaction) {
                        $profit_loss = $transaction['profit_loss'];
                        $total_profit_loss += $profit_loss;

                        // Set the class based on profit/loss
                        $profit_loss_class = 'no-profit-loss';
                        if ($profit_loss > 0) {
                            $profit_loss_class = 'profit';
                        } elseif ($profit_loss < 0) {
                            $profit_loss_class = 'loss';
                        }
                        ?>
                        <tr>
                            <td class="name"><?php echo htmlspecialchars($ticker); ?></td>
                            <td class="name"><?php echo $transaction['quantity']; ?></td>
                            <td class="name">₹<?php echo number_format($transaction['buy_price'], 2); ?></td>
                            <td class="name">₹<?php echo number_format($transaction['sell_price'], 2); ?></td>
                            <td class="<?php echo $profit_loss_class; ?> name">
                                <?php 
                                    if ($profit_loss > 0) {
                                        echo "Profit: ₹" . number_format($profit_loss, 2);
                                    } elseif ($profit_loss < 0) {
                                        echo "Loss: ₹" . number_format(abs($profit_loss), 2);
                                    } else {
                                        echo "No Profit or Loss";
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>

    <h3 class="name">Total Profit/Loss: ₹<?php echo number_format($total_profit_loss, 2); ?></h3>

    <br>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php">
        Back to Dashboard
    </a>
</body>
</html>
