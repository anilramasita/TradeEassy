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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Transactions</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<style>
        a {
            color: blue;
            text-decoration: underline;
        }
        .name{
            color:white;
        }

        .bg{
            background-image:url("https://media.licdn.com/dms/image/D4D12AQGZEFxxX2Dzrg/article-cover_image-shrink_600_2000/0/1694885800616?e=2147483647&v=beta&t=xIb8ep2dFt00qXLBqHbkflHNTszUQ7mtbqsRFmg0eiM");
            background-size:cover;
        }
    </style>
<body class="bg">
    <h2 class="name">Your Transactions</h2>

    <table>
        <thead>
            <tr>
                <th class="name">Transaction Type</th>
                <th class="name">Stock Ticker</th>
                <th class="name">Quantity</th>
                <th class="name">Price per Unit</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)) { ?>
                <tr>
                    <td colspan="5" class="name">No transactions found.</td>
                </tr>
            <?php } else { ?>
                <?php foreach ($transactions as $transaction) { ?>
                    <tr>
                        <td class="name"><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td class="name"><?php echo htmlspecialchars($transaction['ticker']); ?></td>
                        <td class="name"><?php echo $transaction['quantity']; ?></td>
                        <td class="name"><?php echo $transaction['price']; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>

    <br>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php">
        Back to Dashboard
    </a>
</body>
</html>
