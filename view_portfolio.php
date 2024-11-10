<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include 'includes/db.php';

// Fetch portfolio information
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM Portfolio WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$portfolio = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Portfolio</title>
    <link rel="stylesheet" href="assets/styles.css">

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
</head>
<body class="bg">
    <h2 class="name">Your Portfolio</h2>

    <table>
        <thead>
            <tr>
                <th class="name">Stock Ticker</th>
                <th class="name">Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($portfolio as $stock) { ?>
                <tr>
                    <td class="name"><?php echo htmlspecialchars($stock['ticker']); ?></td>
                    <td class="name"><?php echo $stock['quantity']; ?></td>
                    
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <br>

    <!-- Back to Dashboard Button -->
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
