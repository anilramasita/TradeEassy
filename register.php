<?php
// Include the database connection file
include 'includes/db.php';

// Initialize error and success messages
$error = '';
$success = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form input
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    try {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT * FROM Users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            $error = "Username or email already taken. Please try again.";
        } else {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO Users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password_hash]);
            $success = "Registration successful! <a href='index.php'>Click here to login</a>.";
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
</head>
<body class="bg">
    <h2  class="name">Register an Account</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="username" class="name">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email" class="name">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password" class="name">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>

    <p class="name">Already have an account? <a href="index.php"><button>Login here</button></a></p>
</body>
</html>
