<?php
include 'connect.php';

$successMessage = "";

// Get available serials (not already used in schedule)
$serialOptions = [];
$serialQuery = $conn->query("
  SELECT serialNo 
  FROM fire_extinguisher 
  WHERE serialNo NOT IN (SELECT serialNo FROM schedule) 
  ORDER BY serialNo ASC
");
while ($row = $serialQuery->fetch_assoc()) {
  $serialOptions[] = $row['serialNo'];
}

// Fetch Service IDs
$serviceOptions = [];
$serviceQuery = $conn->query("SELECT serviceID FROM service");
while ($row = $serviceQuery->fetch_assoc()) {
  $serviceOptions[] = $row['serviceID'];
}

// Fetch Premise IDs
$premiseOptions = [];
$premiseQuery = $conn->query("SELECT premiseID FROM premise");
while ($row = $premiseQuery->fetch_assoc()) {
  $premiseOptions[] = $row['premiseID'];
}

// Handle Create Order
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create"])) {
  $orderID = $_POST["orderID"];
  $customerID = $_POST["customerID"];
  $premiseID = $_POST["premiseID"];
  $serviceID = $_POST["serviceID"];
  $additionalNotes = $_POST["additionalNotes"] ?? '';

  $check = $conn->prepare("SELECT * FROM orders WHERE orderID = ?");
  $check->bind_param("s", $orderID);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows > 0) {
    echo "<script>alert('Order ID already exists.');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO orders (orderID, customerID, premiseID, serviceID, Additional_Notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $orderID, $customerID, $premiseID, $serviceID, $additionalNotes);
    $success = $stmt->execute();

    echo $success
      ? "<script>alert('Order created successfully.'); window.location.href='request_management.php';</script>"
      : "<script>alert('Failed to create order.');</script>";
  }
}

// Handle request deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["done"])) {
  $customerID = $_POST["customerID"];
  $serviceID = $_POST["serviceID"];

  $stmt = $conn->prepare("DELETE FROM request WHERE customerID = ? AND serviceID = ?");
  $stmt->bind_param("ss", $customerID, $serviceID);
  $stmt->execute();
  echo "<script>alert('Request deleted.'); window.location.href='request_management.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Request Management</title>
  <link rel="stylesheet" href="adminFormat.css" />
</head>
<body class="admin">
  <nav class="navbar">
    <div class="nav-left">
      <a href="adminHome.php" class="nav-item">HOME</a>
      <a href="request_management.php" class="nav-item active">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.php" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

  <h2 class="title">CREATE ORDER</h2>
  <p class="note">Click "CREATE" once you've finished creating the order based on the customer request below.</p>
  <div class="container">
    <form method="POST" class="create-order">
      <h3 class="sub-title">ORDER DETAILS</h3>

      <label>Order ID</label>
      <input type="text" name="orderID" value="<?= isset($_POST['orderID']) ? htmlspecialchars($_POST['orderID']) : '' ?>" required />

      <label>Customer ID</label>
      <input type="text" name="customerID" value="<?= isset($_POST['customerID']) ? htmlspecialchars($_POST['customerID']) : '' ?>" required />

      <label>Premise ID</label>
      <select name="premiseID" required>
        <option value="">-- Select Premise ID --</option>
        <?php foreach ($premiseOptions as $pid): ?>
          <option value="<?= htmlspecialchars($pid) ?>" <?= (isset($_POST['premiseID']) && $_POST['premiseID'] === $pid) ? 'selected' : '' ?>>
            <?= htmlspecialchars($pid) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Service ID</label>
      <select name="serviceID" required>
        <option value="">-- Select Service ID --</option>
        <?php foreach ($serviceOptions as $sid): ?>
          <option value="<?= htmlspecialchars($sid) ?>" <?= (isset($_POST['serviceID']) && $_POST['serviceID'] === $sid) ? 'selected' : '' ?>>
            <?= htmlspecialchars($sid) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Additional Notes</label>
      <textarea name="additionalNotes"><?= isset($_POST['additionalNotes']) ? htmlspecialchars($_POST['additionalNotes']) : '' ?></textarea>

      <button type="submit" name="create">CREATE</button>
    </form>

    <a id="see-order-details" href="orderDetails.php">SEE ORDER DETAILS</a>

    <hr>
    <h2 class="title">CUSTOMER REQUESTS</h2>
    <p class="note">Click "DONE" once you've finished creating the order and schedule.</p>

    <?php
    $result = $conn->query("SELECT * FROM request");
    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
    <div class="customer-request">
      <p><strong>Customer ID:</strong> <?= $row['customerID'] ?></p>
      <p><strong>Service ID:</strong> <?= $row['serviceID'] ?></p>
      <p><strong>Premise ID:</strong> <?= $row['premiseID'] ?></p>
      <p><strong>Quantity:</strong> <?= $row['Quantity'] ?></p>
      <p><strong>Location:</strong> <?= $row['Location'] ?></p>
      <p><strong>Preferred Date:</strong> <?= $row['preferredDate'] ?></p>
      <p><strong>Additional Notes:</strong> <?= $row['Additional_Notes'] ?></p>

      <form method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
        <input type="hidden" name="customerID" value="<?= $row['customerID'] ?>" />
        <input type="hidden" name="serviceID" value="<?= $row['serviceID'] ?>" />
        <button type="submit" name="done">DONE</button>
      </form>
    </div>
    <?php endwhile; else: ?>
      <p>No customer request found.</p>
    <?php endif; ?>

    <p class="note">*WARNING: Once the “DONE” button is clicked, the customer request will be removed as a mark that you've created the order and schedule.</p>
  </div>
</body>
</html>
