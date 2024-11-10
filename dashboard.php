<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        nav ul {
            display: flex;
            list-style-type: none;
            padding: 0;
            justify-content: center;
        }
        
        nav ul li {
            margin: 0 15px;
        }

        .head{
            text-align:center;
            font-size:50px;
            color:white;
        }

        .bg{
            background-image:url("https://media.licdn.com/dms/image/D4D12AQGZEFxxX2Dzrg/article-cover_image-shrink_600_2000/0/1694885800616?e=2147483647&v=beta&t=xIb8ep2dFt00qXLBqHbkflHNTszUQ7mtbqsRFmg0eiM");
            background-size:cover;
        }

        nav ul li a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
        }

        nav ul li a:hover {
            background-color: #0056b3;
        }
        .sub-head{
            color:white;
            
        }
    </style>
</head>
<body class="bg">
    <div>
    <h1 class="head">TradeEassy</h1>
    <h2 class="sub-head">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <h2 class="sub-head">Trading Options</h2>
    <nav>
        <ul>
            <li><a href="buy.php">Buy Stock</a></li>
            <li><a href="sell.php">Sell Stock</a></li>
            <li><a href="view_portfolio.php">View Portfolio</a></li>
            <li><a href="view_transactions.php">View Transactions</a></li>
            <li><a href="profit_loss.php">Check Profit/Loss</a></li>
            <li><a href="https://in.tradingview.com/#main-market-summary" target="_blank">View Live Stock Chart</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>

    </div>
</body>
</html>

