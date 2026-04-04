<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "root123", "senior_portal");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all notifications (full tracking history)
$sql = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Request Tracking</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background-color: #f8f9fa;
}

.card {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    border-left: 6px solid #007BFF;
}

.status {
    font-weight: bold;
}

.pending { color: orange; }
.approved { color: green; }
.completed { color: blue; }

.date {
    font-size: 13px;
    color: gray;
}
</style>
</head>

<body>

<!-- ✅ NAVBAR (same as dashboard) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container">
<a class="navbar-brand" href="dashboard.php">Senior Support Portal</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="menu">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
<li class="nav-item"><a class="nav-link" href="request_service.php">Services</a></li>
<li class="nav-item"><a class="nav-link" href="medical_help.php">Medical Help</a></li>
<li class="nav-item"><a class="nav-link active" href="track_request.php">Track Request</a></li>
<li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>

<!-- Content -->
<div class="container mt-5">

<h2 class="text-center mb-4">Request Tracking</h2>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $statusClass = strtolower($row['status']);

        echo "<div class='card p-3 mb-3'>
                <h5>".$row['message']."</h5>
                <p class='status $statusClass'>Status: ".$row['status']."</p>
                <p class='date'>Updated on: ".$row['created_at']."</p>
              </div>";
    }
} else {
    echo "<div class='alert alert-info text-center'>No notifications yet.</div>";
}
?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>