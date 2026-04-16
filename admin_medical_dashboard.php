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

$updated = false;

// ---------------- UPDATE ----------------
if(isset($_POST['update'])){
    $id = $_POST['request_id'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];

    $check = $conn->query("SELECT status,user_id FROM requests WHERE request_id='$id'");
    $data = $check->fetch_assoc();

    if($data && $data['status'] != $status){

        $conn->query("UPDATE requests SET status='$status', priority_level='$priority' WHERE request_id='$id'");

        $msg = "Medical request #$id updated to $status (Priority: $priority)";

        $conn->query("INSERT INTO notifications (user_id,message,status,created_at)
                      VALUES ('{$data['user_id']}','$msg','unread',NOW())");

        $updated = true;
    }
}

// ---------------- AJAX TABLE ----------------
if(isset($_GET['ajax'])){
    $result = $conn->query("SELECT * FROM requests WHERE request_type='Medical' ORDER BY created_at DESC");

    while($r = $result->fetch_assoc()){ ?>

<tr class="<?=
($r['priority_level']=="High")?"table-danger":
(($r['priority_level']=="Medium")?"table-warning":"")
?>">

<td><?= $r['request_id'] ?></td>
<td><?= $r['user_id'] ?></td>
<td><?= $r['description'] ?></td>
<td><?= $r['status'] ?></td>
<td><?= $r['priority_level'] ?></td>

<td>

<form method="POST" action="admin_medical_dashboard.php" class="d-flex gap-2">
<input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">

<select name="status" class="form-select form-select-sm">
<option <?= $r['status']=="Pending"?"selected":"" ?>>Pending</option>
<option <?= $r['status']=="Approved"?"selected":"" ?>>Approved</option>
<option <?= $r['status']=="Completed"?"selected":"" ?>>Completed</option>
</select>

<select name="priority" class="form-select form-select-sm">
<option <?= $r['priority_level']=="Low"?"selected":"" ?>>Low</option>
<option <?= $r['priority_level']=="Medium"?"selected":"" ?>>Medium</option>
<option <?= $r['priority_level']=="High"?"selected":"" ?>>High</option>
</select>

<button name="update" onclick="isInteracting=true" class="btn btn-primary btn-sm">Update</button>
</form>

<form method="POST" action="admin_medical_dashboard.php" class="mt-1">
<input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
<input type="hidden" name="status" value="Completed">
<input type="hidden" name="priority" value="<?= $r['priority_level'] ?>">
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

        // ✅ FIXED COLUMN NAME
        if(isset($row['notification_id'])){
            $conn->query("UPDATE notifications SET status='read' WHERE notification_id='".$row['notification_id']."'");
        }
    }
    exit();
}

// ---------------- COUNT ----------------
$count = $conn->query("SELECT COUNT(*) total FROM notifications WHERE status='unread'")
              ->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Medical Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

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

<div class="container mt-4">

<h3>Medical Requests (Live)</h3>

<?php if($updated){ ?>
<script>
window.onload = function(){
    showSuccess("Medical request updated successfully!");
};
</script>
<?php } ?>

<table class="table table-bordered">
<tr><th>ID</th><th>User</th><th>Description</th><th>Status</th><th>Priority</th><th>Action</th></tr>
<tbody id="tableData"></tbody>
</table>

</div>

<script>
let isInteracting = false;

// Load data
function loadData(){
    if(isInteracting) return;

    fetch("admin_medical_dashboard.php?ajax=1")
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
    fetch("admin_medical_dashboard.php?notif=1")
    .then(res => res.text())
    .then(msg => { if(msg.trim()!="") showPopup(msg); });
}

// Success popup
function showSuccess(message){
    let d=document.createElement("div");
    d.innerHTML=message;
    d.style="position:fixed;top:20px;right:20px;background:#007bff;color:white;padding:15px;border-radius:10px;";
    document.body.appendChild(d);
    setTimeout(()=>d.remove(),3000);
}

// Notification popup
function showPopup(msg){
    let d=document.createElement("div");
    d.innerHTML=msg;
    d.style="position:fixed;bottom:20px;right:20px;background:#28a745;color:white;padding:15px;border-radius:10px;";
    document.body.appendChild(d);
    setTimeout(()=>d.remove(),4000);
}

// 🔥 Smooth refresh
loadData();
setInterval(loadData,5000);
setInterval(checkNotif,4000);
</script>

</body>
</html>