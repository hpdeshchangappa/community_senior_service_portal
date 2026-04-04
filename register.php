<?php
session_start();

// Database connection
$conn = new mysqli("localhost","root","root123","senior_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_msg = $error_msg = "";

if(isset($_POST['register'])){
    $name = $conn->real_escape_string($_POST['name']);
    $age = (int)$_POST['age'];
    $phone = $conn->real_escape_string($_POST['phone']); // NEW
    $language = $conn->real_escape_string($_POST['language']); // NEW
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if($password !== $confirm_password){
        $error_msg = "Passwords do not match!";
    } 
    elseif($age <= 0){
        $error_msg = "Please enter a valid age!";
    }
    elseif(!preg_match("/^[0-9]{10}$/", $phone)){
        $error_msg = "Enter a valid 10-digit phone number!";
    }
    else {
        // Check if email already exists
        $check_sql = "SELECT * FROM users WHERE email='$email'";
        $check_result = $conn->query($check_sql);

        if($check_result->num_rows > 0){
            $error_msg = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert (NEW FIELDS ADDED)
            $sql = "INSERT INTO users (name, age, phone, language, email, password, created_at) 
                    VALUES ('$name', '$age', '$phone', '$language', '$email', '$hashed_password', NOW())";

            if($conn->query($sql) === TRUE){
                header("Location: login.php?success=1");
                exit();
            } else {
                $error_msg = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Senior Support Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{ background-color:#f8f9fa; }
.register-box{ max-width:450px; margin:auto; margin-top:50px; }
.card{ border-radius:15px; }
</style>
</head>
<body>

<div class="register-box">
<div class="card shadow p-4">
<h3 class="text-center mb-3">User Registration</h3>

<?php
if($error_msg){ echo "<div class='alert alert-danger'>$error_msg</div>"; }
?>

<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Age</label>
        <input type="number" name="age" class="form-control" required min="1">
    </div>

    <!-- PHONE FIELD -->
    <div class="mb-3">
        <label>Phone Number</label>
        <input type="text" name="phone" class="form-control" required maxlength="10">
    </div>

    

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <!-- LANGUAGE DROPDOWN -->
    <div class="mb-3">
        <label>Preferred Language</label>
        <select name="language" class="form-control" required>
            <option value="">Select Language</option>
            <option value="English">English</option>
            <option value="Hindi">Hindi</option>
            <option value="Kannada">Kannada</option>
            <option value="Tamil">Tamil</option>
            <option value="Malayalam">Malayalam</option>
            <option value="Telugu">Telugu</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>

    <button type="submit" name="register" class="btn btn-success w-100">Register</button>
</form>

<div class="text-center mt-3">
<p>Already have an account?</p>
<a href="login.php" class="btn btn-primary">Login</a>
</div>
</div>
</div>

</body>
</html>