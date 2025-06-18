<?php
session_start();
include 'connect.php';

$userID = trim($_POST['userID']);
$password = trim($_POST['password']);

if (empty($userID) || empty($password)) {
    echo "<script>alert('Please fill in all fields.'); window.location.href='login.html';</script>";
    exit();
}

$roleTables = [
    'admin' => ['table' => 'admin', 'id_col' => 'adminID', 'pass_col' => 'adminPassword', 'redirect' => 'adminHome.html'],
    'staff' => ['table' => 'staff', 'id_col' => 'staff_id', 'pass_col' => 'password', 'redirect' => 'staffHome.html'],
    'customer' => ['table' => 'customer', 'id_col' => 'customer_id', 'pass_col' => 'password', 'redirect' => 'customerHome.html']
];

foreach ($roleTables as $role => $info) {
    $stmt = $conn->prepare("SELECT * FROM {$info['table']} WHERE {$info['id_col']} = ?");
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row[$info['pass_col']])) {
            $_SESSION['userID'] = $userID;
            $_SESSION['role'] = $role;
            echo "<script>alert('Logged in as " . ucfirst($role) . "'); window.location.href='{$info['redirect']}';</script>";
            exit();
        }
    }
}

echo "<script>alert('Invalid ID or Password.'); window.location.href='login.html';</script>";
?>
