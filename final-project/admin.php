<?php
require_once 'auth_check.php';
requireRole(['super_admin']);

$users = [
    "superadmin" => "super_admin",
    "analyst1" => "analyst",
    "viewer1" => "viewer",
    "grader" => "super_admin"
];
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>

<style>
body{
    font-family:Arial;
    padding:30px;
}

table{
    border-collapse:collapse;
    width:400px;
}

th,td{
    border:1px solid #ccc;
    padding:8px;
}

th{
    background:#eee;
}
</style>

</head>
<body>

<h1>User Management</h1>

<p>This page allows the super admin to view system users and roles.</p>

<table>
<tr>
<th>Username</th>
<th>Role</th>
</tr>

<?php foreach($users as $username => $role): ?>

<tr>
<td><?= htmlspecialchars($username) ?></td>
<td><?= htmlspecialchars($role) ?></td>
</tr>

<?php endforeach; ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
