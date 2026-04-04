<?php
session_start();

// Database connection
$conn = new mysqli("localhost","root","root123","senior_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_msg = "";

// Show success message from registration redirect
if(isset($_GET['success'])){
    echo "<div class='alert alert-success text-center'>Registration successful! Please login.</div>";
}

if(isset($_POST['login'])){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Fetch user by email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        // VERIFY HASHED PASSWORD
        if(password_verify($password, $row['password'])){
            // Store session
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error_msg = "Incorrect password!";
        }
    } else {
        $error_msg = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Senior Support Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{ background-color:#f8f9fa; }
.login-box{ max-width:400px; margin:auto; margin-top:60px; }
.card{ border-radius:15px; }
</style>
</head>
<body>

<div class="login-box">
<div class="card shadow p-4">
<h3 class="text-center mb-3">User Login</h3>

<?php
if($error_msg){
    echo "<div class='alert alert-danger'>$error_msg</div>";
}
?>

<form method="POST">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
</form>

<div class="text-center mt-3">
<p>Don't have an account?</p>
<a href="register.php" class="btn btn-success">Register</a>
</div>

</div>
</div>

</body>
</html>