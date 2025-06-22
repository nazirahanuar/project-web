<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['userID'];

$stmt = $conn->prepare("SELECT * FROM customer WHERE customerID = ?");
$stmt->bind_param("s", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$orderResult = $conn->query("SELECT * FROM orders WHERE customerID = '$customerID' ORDER BY orderID DESC LIMIT 1");
$order = $orderResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="customer.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <a href="customerHome.php" class="nav-item">HOME</a>
        <a href="knowledgeHub.html" class="nav-item">KNOWLEDGE<br>HUB</a>
        <a href="requestService.html" class="nav-item">REQUEST<br>SERVICE</a>
    </div>
    <div class="nav-center">
        <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
        <a href="mySchedule.html" class="nav-item">MY SCHEDULE</a>
        <a href="custProfile.php" class="nav-item active">PROFILE</a>
    </div>
</nav>

<main>
    <?php if (isset($_GET['edit']) && $_GET['edit'] === 'true'): ?>
        <section class="profile-section">
            <h2 class="sec-title">EDIT PROFILE</h2>
            <form class="edit-form" action="updateCustProfile.php" method="POST" enctype="multipart/form-data">

                <div class="edit-photo">
                    <label for="profilePic">
                        <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="circle-pic" alt="Edit Profile Picture">
                    </label>
                    <input type="file" name="profilePic" id="profilePic" accept="image/*" hidden>
                </div>

                <div class="edit-center-header">
                    <p class="customer-id">Customer ID: <strong><?= htmlspecialchars($user['customerID']) ?></strong></p>
                </div>

                <input type="hidden" name="customerID" value="<?= htmlspecialchars($user['customerID']) ?>">

                <div class="edit-row">
                    <label>Name:</label>
                    <input type="text" name="customerName" value="<?= htmlspecialchars($user['customerName']) ?>" required>
                </div>

                <div class="edit-row">
                    <label>Gender:</label>
                    <div class="radio-group">
                        <input type="radio" id="male" name="gender" value="male" <?= ($user['Gender'] == 'male') ? 'checked' : '' ?>>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="female" <?= ($user['Gender'] == 'female') ? 'checked' : '' ?>>
                        <label for="female">Female</label>
                    </div>
                </div>

                <div class="edit-row">
                    <label>No. Tel:</label>
                    <input type="text" name="NoTel" value="<?= htmlspecialchars($user['noTel']) ?>" required>
                </div>

                <div class="edit-row">
                    <label>Email:</label>
                    <input type="email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>
                </div>

                <div class="edit-row">
                    <label>Change Password:</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password">
                </div>

                <div class="button-row">
                    <a href="custProfile.php" class="cancel-btn">CANCEL</a>
                    <button type="submit" class="save-btn">SAVE</button>
                </div>
            </form>
        </section>

    <?php else: ?>
        <section class="profile-section">
            <h2 class="sec-title">YOUR PROFILE</h2>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-header-left">
                        <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="profile-pic" alt="Profile Picture">
                    </div>

                    <div class="profile-header-right centered-info">
                        <h3 class="customer-id">Customer ID: <?= htmlspecialchars($user['customerID']) ?></h3>
                        <a href="custProfile.php?edit=true" class="edit-btn">Edit Profile</a>
                    </div>
                </div>

                <div class="profile-divider"></div>

                <div class="profile-info">
                    <h2 class="th-title">CUSTOMER DETAILS:</h2>
                    <br><p><strong>Name:</strong> <?= htmlspecialchars($user['customerName']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($user['Gender']) ?></p>
                    <p><strong>No. Tel:</strong> <?= htmlspecialchars($user['noTel']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
                </div>
            </div>
        </section>

        <section class="order-section">
            <h2 class="sec-title">YOUR ORDER</h2>
            <div class="order-box">
                <?php if ($order): ?>
                    <p><strong>Order ID:</strong> <?= htmlspecialchars($order['orderID']) ?></p>
                    <p><strong>Serial No.:</strong> <?= htmlspecialchars($order['serialNo'] ?? '-') ?></p>
                    <p><strong>Customer ID:</strong> <?= htmlspecialchars($user['customerID']) ?></p>
                    <p><strong>Quantity of Fire Extinguisher:</strong> <?= htmlspecialchars($order['quantity']) ?></p>
                    <p><strong>Additional Notes:</strong> <?= htmlspecialchars($order['notes'] ?? '-') ?></p>
                <?php else: ?>
                    <p>No orders found.</p>
                <?php endif; ?>
            </div>
            <p class="schedule-hint">Ready to track your schedule? Go to <a href="mySchedule.html#schedule-section"><strong>MY SCHEDULE</strong></a></p>
        </section>
    <?php endif; ?>
</main>

</body>
</html>
