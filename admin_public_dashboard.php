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

// Track update
$updated = false;

// ---------------- UPDATE ----------------
if(isset($_POST['update'])){
    $id = $_POST['request_id'];
    $status = $_POST['status'];

    $check = $conn->query("SELECT status,user_id FROM requests WHERE request_id='$id'");
    $data = $check->fetch_assoc();

    if($data && $data['status'] != $status){

        $conn->query("UPDATE requests SET status='$status' WHERE request_id='$id'");

        $msg = "Your request #$id has been updated to $status";

        $conn->query("INSERT INTO notifications (user_id,message,status,created_at)
                      VALUES ('{$data['user_id']}','$msg','unread',NOW())");

        $updated = true;
    }
}

// ---------------- AJAX TABLE ----------------
if(isset($_GET['ajax'])){

    // ✅ ONLY PUBLIC REQUESTS
    $result = $conn->query("
    SELECT * FROM requests 
    WHERE request_type IN ('Water Supply','Electricity','Sanitation','General','Road Issue','Drainage')
    ORDER BY created_at DESC
    ");

    while($r = $result->fetch_assoc()){ ?>
    <tr>
    <td><?= $r['request_id'] ?></td>
    <td><?= $r['user_id'] ?></td>
    <td><?= $r['request_type'] ?></td>
    <td><?= $r['description'] ?></td>

    <td>
        <?php
        $color = "secondary";
        if($r['status']=="Pending") $color="warning";
        if($r['status']=="Approved") $color="primary";
        if($r['status']=="Completed") $color="success";
        ?>
        <span class="badge bg-<?= $color ?>"><?= $r['status'] ?></span>
    </td>

    <td>
    <form method="POST" action="admin_public_dashboard.php" class="d-flex gap-2">
        <input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">

        <select name="status" class="form-select form-select-sm">
            <option <?= $r['status']=="Pending"?"selected":"" ?>>Pending</option>
            <option <?= $r['status']=="Approved"?"selected":"" ?>>Approved</option>
            <option <?= $r['status']=="Completed"?"selected":"" ?>>Completed</option>
        </select>

        <button name="update" onclick="isInteracting=true" class="btn btn-primary btn-sm">Update</button>
    </form>

    <form method="POST" action="admin_public_dashboard.php" class="mt-1">
        <input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
        <input type="hidden" name="status" value="Completed">
        <button name="update" onclick="isInteracting=true" class="btn btn-success btn-sm">✔ Done</button>
    </form>
    </td>
    </tr>
<?php }
exit();
}

// ---------------- NOTIFICATION ----------------
if(isset($_GET['notif'])){
    $res = $conn->query("SELECT * FROM notifications WHERE status='unread' ORDER BY created_at DESC LIMIT 1");

    if($row = $res->fetch_assoc()){
        echo $row['message'];

        if(isset($row['id'])){
            $conn->query("UPDATE notifications SET status='read' WHERE id='".$row['id']."'");
        } 
        elseif(isset($row['notification_id'])){
            $conn->query("UPDATE notifications SET status='read' WHERE notification_id='".$row['notification_id']."'");
        }
    }
    exit();
}

// Notification count
$count = $conn->query("SELECT COUNT(*) total FROM notifications WHERE status='unread'")
              ->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Public Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f8f9fa; }
.card {
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark">
<div class="container">

<span class="navbar-brand">Admin Panel</span>

<div>
<a href="admin_public_dashboard.php" class="btn btn-primary btn-sm">Public</a>
<a href="admin_medical_dashboard.php" class="btn btn-danger btn-sm">Medical</a>

<a href="notifications.php" class="btn btn-light position-relative">
🔔
<?php if($count>0){ ?>
<span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
<?= $count ?>
</span>
<?php } ?>
</a>

<span class="text-white mx-3"><?= $_SESSION['admin'] ?></span>
<a href="logout.php" class="btn btn-warning btn-sm">Logout</a>
</div>

</div>
</nav>

<!-- MAIN -->
<div class="container mt-4">

<h3 class="mb-3">Public Service Requests (Live)</h3>

<?php if($updated){ ?>
<script>
window.onload = function(){
    showSuccess("Status updated successfully!");
};
</script>
<?php } ?>

<div class="card p-3">
<table class="table table-bordered table-striped">
<tr>
<th>ID</th>
<th>User</th>
<th>Type</th>
<th>Description</th>
<th>Status</th>
<th>Action</th>
</tr>

<tbody id="tableData"></tbody>
</table>
</div>

</div>

<script>
let isInteracting = false;

// Load data
function loadData(){
    if(isInteracting) return;

    fetch("admin_public_dashboard.php?ajax=1")
    .then(res => res.text())
    .then(data => document.getElementById("tableData").innerHTML = data);
}

// Detect interaction
document.addEventListener("focusin", function(e){
    if(e.target.tagName === "SELECT" || e.target.tagName === "BUTTON"){
        isInteracting = true;
    }
});

document.addEventListener("focusout", function(){
    isInteracting = false;
});

// Notifications
function checkNotif(){
    fetch("admin_public_dashboard.php?notif=1")
    .then(res => res.text())
    .then(msg => {
        if(msg.trim()!="") showPopup(msg);
    });
}

// Success popup
function showSuccess(message){
    let div = document.createElement("div");
    div.innerHTML = message;
    div.style = "position:fixed;top:20px;right:20px;background:#007bff;color:white;padding:15px;border-radius:10px;";
    document.body.appendChild(div);
    setTimeout(()=>div.remove(),3000);
}

// Notification popup
function showPopup(msg){
    let div = document.createElement("div");
    div.innerHTML = msg;
    div.style = "position:fixed;bottom:20px;right:20px;background:#28a745;color:white;padding:15px;border-radius:10px;";
    document.body.appendChild(div);
    setTimeout(()=>div.remove(),4000);
}

// 🔥 Slower refresh (better UX)
loadData();
setInterval(loadData,5000);
setInterval(checkNotif,4000);
</script>

</body>
</html>