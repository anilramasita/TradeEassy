<?php
// Start the session
session_start();

// Include the database connection file
include 'includes/db.php';

// Initialize error message
$error = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL to fetch user
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Check if user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables and redirect to dashboard
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        // Set error message for invalid credentials
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/styles.css"> <!-- Link to your CSS file if available -->
</head>
<style>
    button{
        background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    margin: 5px;
    border-radius: 5px;
    border-width:0px;
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
    <h2 class="name">Login to Your Account</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <label for="username" class="name">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password" class="name">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit" class="name">Login</button>
    </form>

    <p class="name">Don't have an account? <a href="register.php"><button>Register here</button></a></p>
</body>
</html>
