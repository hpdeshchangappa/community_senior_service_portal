<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "root123", "senior_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ----------------------
// UPDATE STATUS
// ----------------------
if(isset($_POST['update'])){

    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    // Update request
    $conn->query("UPDATE requests SET status='$status' WHERE id='$request_id'");

    // Get user_id
    $res = $conn->query("SELECT user_id FROM requests WHERE id='$request_id'");
    $row = $res->fetch_assoc();
    $user_id = $row['user_id'];

    // Insert notification
    $message = "Your request has been $status";

    $conn->query("INSERT INTO notifications (user_id, message, status, created_at)
                  VALUES ('$user_id', '$message', '$status', NOW())");
}

// ----------------------
// FETCH REQUESTS
// ----------------------
$result = $conn->query("SELECT * FROM requests ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color:#f8f9fa; }
</style>

</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark">
<div class="container">
<span class="navbar-brand">Admin Panel</span>
<span class="text-white">Welcome, <?= $_SESSION['admin'] ?></span>
<a href="logout.php" class="btn btn-warning btn-sm">Logout</a>
</div>
</nav>

<!-- CONTENT -->
<div class="container mt-4">

<h3 class="mb-3">All Requests</h3>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>User ID</th>
<th>Type</th>
<th>Description</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['user_id'] ?></td>
<td><?= $row['request_type'] ?></td>
<td><?= $row['description'] ?></td>
<td><?= $row['status'] ?></td>

<td>
<form method="POST" class="d-flex gap-2">

<input type="hidden" name="request_id" value="<?= $row['id'] ?>">

<select name="status" class="form-select">
<option value="Pending">Pending</option>
<option value="Approved">Approved</option>
<option value="Completed">Completed</option>
</select>

<button type="submit" name="update" class="btn btn-success btn-sm">
Update
</button>

</form>
</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>

</body>
</html>