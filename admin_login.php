<?php
session_start();

$conn = new mysqli("localhost", "root", "root123", "senior_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        $_SESSION['admin'] = $row['username'];
        $_SESSION['admin_id'] = $row['admin_id'];

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f4f6f9;
}
.card {
    margin-top: 100px;
    border-radius: 12px;
}
</style>

</head>
<body>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-4">

<div class="card p-4">
<h3 class="text-center mb-3">Admin Login</h3>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST">

<input type="email" name="email" class="form-control mb-3" placeholder="Email" required>

<input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

<button type="submit" name="login" class="btn btn-primary w-100">Login</button>

</form>

</div>

</div>
</div>
</div>

</body>
</html>