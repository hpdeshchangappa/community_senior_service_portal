<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "root123", "senior_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest notifications
$sql = "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Senior Support Portal</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color:#f8f9fa; }

.card{
    border-radius:15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.dashboard-btn{
    font-size:18px;
    padding:20px;
    border-radius:10px;
}

.success-msg {
    color: green;
    margin-bottom: 15px;
    font-weight: 500;
}
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container">
<a class="navbar-brand" href="dashboard.php">Senior Support Portal</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="menu">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
<li class="nav-item"><a class="nav-link" href="request_service.php">Services</a></li>
<li class="nav-item"><a class="nav-link" href="medical_help.php">Medical Help</a></li>
<li class="nav-item"><a class="nav-link" href="track_request.php">Track Request</a></li>
<li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>

<!-- Main Content -->
<div class="container mt-5">

<h2 class="text-center mb-4">Welcome, <?= $_SESSION['name'] ?></h2>

<?php
if (isset($_SESSION['success_msg'])) {
    echo "<p class='success-msg'>{$_SESSION['success_msg']}</p>";
    unset($_SESSION['success_msg']);
}
?>

<div class="row g-4">

<!-- Public Service -->
<div class="col-md-6">
<div class="card text-center p-4">
<h4>Public Service Request</h4>
<p>Request help for water, electricity or other services.</p>
<a href="request_service.php" class="btn btn-primary dashboard-btn">Request Service</a>
</div>
</div>

<!-- Medical Help -->
<div class="col-md-6">
<div class="card text-center p-4">
<h4>Medical Assistance</h4>
<p>Submit a request for medical help.</p>
<a href="medical_help.php" class="btn btn-danger dashboard-btn">Medical Help</a>
</div>
</div>

<!-- Track Request -->
<div class="col-md-6">
<div class="card text-center p-4">
<h4>Track Request</h4>
<p>Check the status of your submitted request.</p>
<a href="track_request.php" class="btn btn-success dashboard-btn">Track Status</a>
</div>
</div>

<!-- Notifications -->
<div class="col-md-6">
<div class="card text-center p-4">
<h4>Notifications</h4>
<p>View updates about your requests.</p>
<a href="notifications.php" class="btn btn-warning dashboard-btn">View Notifications</a>
</div>
</div>

</div>

<!-- Latest Notifications -->
<div class="mt-5">
<h4>Your Latest Notifications</h4>

<?php
if ($result->num_rows > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead class='table-light'>
            <tr>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
          </thead><tbody>";

    while ($row = $result->fetch_assoc()) {

        $statusColor = "";
        if ($row['status'] == "Pending") $statusColor = "text-warning";
        elseif ($row['status'] == "Approved") $statusColor = "text-success";
        elseif ($row['status'] == "Completed") $statusColor = "text-primary";

        echo "<tr>
                <td>{$row['message']}</td>
                <td class='$statusColor fw-bold'>{$row['status']}</td>
                <td>{$row['created_at']}</td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p>No notifications yet.</p>";
}
?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>