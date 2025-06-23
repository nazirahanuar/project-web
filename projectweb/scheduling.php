<?php
include 'connect.php';

$successMessage = "";

// Dropdown values
$orderIDs = [];
$staffIDs = [];
$adminIDs = [];
$serviceIDs = [];
$premiseIDs = [];

// Orders
$orderQuery = $conn->query("SELECT orderID FROM orders ORDER BY orderID ASC");
while ($row = $orderQuery->fetch_assoc()) $orderIDs[] = $row['orderID'];

// Staff
$staffQuery = $conn->query("SELECT staffID FROM staff ORDER BY staffID ASC");
while ($row = $staffQuery->fetch_assoc()) $staffIDs[] = $row['staffID'];

// Admins
$adminQuery = $conn->query("SELECT adminID FROM admin ORDER BY adminID ASC");
while ($row = $adminQuery->fetch_assoc()) $adminIDs[] = $row['adminID'];

// Services
$serviceQuery = $conn->query("SELECT serviceID FROM service ORDER BY serviceID ASC");
while ($row = $serviceQuery->fetch_assoc()) $serviceIDs[] = $row['serviceID'];

// Premises
$premiseQuery = $conn->query("SELECT premiseID FROM premise ORDER BY premiseID ASC");
while ($row = $premiseQuery->fetch_assoc()) $premiseIDs[] = $row['premiseID'];

// Delete customer request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["done"])) {
  $customerID = $_POST["customerID"];
  $serviceID = $_POST["serviceID"];

  $stmt = $conn->prepare("DELETE FROM request WHERE customerID = ? AND serviceID = ?");
  $stmt->bind_param("ss", $customerID, $serviceID);
  $stmt->execute();
  $stmt->close();
}

// Create schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create"])) {
  $staffID = $_POST["staffID"];
  $orderID = $_POST["orderID"];
  $scheduleDate = $_POST["scheduleDate"];
  $location = $_POST["location"];
  $serviceID = $_POST["serviceID"];
  $premiseID = $_POST["premiseID"];
  $adminID = $_POST["adminID"];

  if ($staffID && $orderID && $scheduleDate && $location && $serviceID && $premiseID && $adminID) {
    $check = $conn->prepare("SELECT * FROM schedule WHERE staffID = ? AND orderID = ?");
    $check->bind_param("ss", $staffID, $orderID);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
      $successMessage = "<span style='color:red;'>This schedule already exists.</span>";
    } else {
      $stmt = $conn->prepare("INSERT INTO schedule (staffID, orderID, scheduleDate, Location, serviceID, premiseID, adminID) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssss", $staffID, $orderID, $scheduleDate, $location, $serviceID, $premiseID, $adminID);

      if ($stmt->execute()) {
        $successMessage = "<span style='color:green;'>Schedule created successfully.</span>";
      } else {
        $successMessage = "<span style='color:pink;'>Failed to create schedule.</span>";
      }
      $stmt->close();
    }
    $check->close();
  }
}

// DELETE schedule + order + fire extinguisher
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_orderID"], $_POST["delete_staffID"])) {
  $orderID = $_POST["delete_orderID"];
  $staffID = $_POST["delete_staffID"];

  // Step 1: Get serialNo
  $serialQuery = $conn->prepare("SELECT serialNo FROM orders WHERE orderID = ?");
  $serialQuery->bind_param("s", $orderID);
  $serialQuery->execute();
  $serialQuery->bind_result($serialNo);
  $serialQuery->fetch();
  $serialQuery->close();

  // Step 2: Delete schedule
  $stmt = $conn->prepare("DELETE FROM schedule WHERE orderID = ? AND staffID = ?");
  $stmt->bind_param("ss", $orderID, $staffID);
  $stmt->execute();
  $stmt->close();

  // Step 3: Delete order
  $deleteOrderStmt = $conn->prepare("DELETE FROM orders WHERE orderID = ?");
  $deleteOrderStmt->bind_param("s", $orderID);
  $deleteOrderStmt->execute();
  $deleteOrderStmt->close();

  // Step 4: Delete fire extinguisher
  if (!empty($serialNo)) {
    $deleteFE = $conn->prepare("DELETE FROM fire_extinguisher WHERE serialNo = ?");
    $deleteFE->bind_param("s", $serialNo);
    $deleteFE->execute();
    $deleteFE->close();
  }

  $successMessage = "<span style='color:orange;'>Schedule, order, and extinguisher deleted successfully.</span>";
}

// Get updated schedule
$scheduleResult = $conn->query("SELECT * FROM schedule ORDER BY scheduleDate ASC");

// Get updated requests
$requestResult = $conn->query("SELECT * FROM request ORDER BY preferredDate ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Scheduling</title>
  <link rel="stylesheet" href="adminFormat.css" />
</head>
<body class="admin">

<nav class="navbar">
  <div class="nav-left">
    <a href="adminHome.php" class="nav-item">HOME</a>
    <a href="request_management.php" class="nav-item">REQUEST<br>MANAGEMENT</a>
    <a href="scheduling.php" class="nav-item active">SCHEDULING</a>
  </div>
  <div class="nav-center">
    <img src="image/logo.png" class="logo" alt="Logo" />
  </div>
  <div class="nav-right">
    <a href="fireExtinguisher_information.php" class="nav-item">FIRE EXTINGUISHER<br>INFORMATION</a>
    <a href="information_management.php" class="nav-item">INFORMATION<br>MANAGEMENT</a>
  </div>
</nav>

<section class="schedule-form-section">
  <h2 class="title">CREATE SCHEDULE</h2>
  <p class="note">Create schedules for our committed staff and customers to view.</p>
  <?php if (!empty($successMessage)) echo "<p style='text-align:center;font-weight:bold;'>$successMessage</p>"; ?>

  <form method="POST" class="schedule-form black-box">
    <div class="form-grid">
      <div class="form-group">
        <label>Order ID</label>
        <select name="orderID" required>
          <option value="">-- Select Order ID --</option>
          <?php foreach ($orderIDs as $orderID): ?>
            <option value="<?= htmlspecialchars($orderID) ?>"><?= htmlspecialchars($orderID) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Staff ID</label>
        <select name="staffID" required>
          <option value="">-- Select Staff ID --</option>
          <?php foreach ($staffIDs as $staffID): ?>
            <option value="<?= htmlspecialchars($staffID) ?>"><?= htmlspecialchars($staffID) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Schedule Date</label>
        <input type="date" name="scheduleDate" required />
      </div>

      <div class="form-group full-width">
        <label>Location</label>
        <textarea name="location" rows="3" required></textarea>
      </div>

      <div class="form-group">
        <label>Service ID</label>
        <select name="serviceID" required>
          <option value="">-- Select Service ID --</option>
          <?php foreach ($serviceIDs as $serviceID): ?>
            <option value="<?= htmlspecialchars($serviceID) ?>"><?= htmlspecialchars($serviceID) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Premise ID</label>
        <select name="premiseID" required>
          <option value="">-- Select Premise ID --</option>
          <?php foreach ($premiseIDs as $premiseID): ?>
            <option value="<?= htmlspecialchars($premiseID) ?>"><?= htmlspecialchars($premiseID) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Admin ID</label>
        <select name="adminID" required>
          <option value="">-- Select Admin ID --</option>
          <?php foreach ($adminIDs as $adminID): ?>
            <option value="<?= htmlspecialchars($adminID) ?>"><?= htmlspecialchars($adminID) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-submit-center">
      <button type="submit" name="create" class="btn-create">CREATE</button>
    </div>
  </form>
</section>

<hr>
<h2 class="title">SCHEDULE DETAILS</h2>

<div class="search-bar" style="text-align: center; margin-bottom: 15px;">
  <input type="text" id="searchInput" placeholder="Search">
  <button class="search-icon" onclick="searchTable()" type="button">🔍</button>
</div>

<div class="table-wrapper">
  <table class="schedule-table">
    <thead>
      <tr>
        <th>Staff ID</th>
        <th>Order ID</th>
        <th>Schedule Date</th>
        <th>Location</th>
        <th>Service ID</th>
        <th>Premise ID</th>
        <th>Admin ID</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($scheduleResult && $scheduleResult->num_rows > 0): ?>
        <?php while ($row = $scheduleResult->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['staffID']) ?></td>
            <td><?= htmlspecialchars($row['orderID']) ?></td>
            <td><?= htmlspecialchars($row['scheduleDate']) ?></td>
            <td><?= htmlspecialchars($row['Location']) ?></td>
            <td><?= htmlspecialchars($row['serviceID']) ?></td>
            <td><?= htmlspecialchars($row['premiseID']) ?></td>
            <td><?= htmlspecialchars($row['adminID']) ?></td>
            <td>
              <form method="POST" onsubmit="return confirm('Delete this schedule and order?');">
                <input type="hidden" name="delete_orderID" value="<?= $row['orderID'] ?>">
                <input type="hidden" name="delete_staffID" value="<?= $row['staffID'] ?>">
                <button type="submit" class="btn-delete">DELETE</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8">No schedule found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<hr>
<h2 class="title">CUSTOMER REQUESTS</h2>
<p class="note">These customer requests are awaiting orders and schedules.</p>

<?php if ($requestResult && $requestResult->num_rows > 0): ?>
  <?php while ($row = $requestResult->fetch_assoc()): ?>
    <div class="customer-request">
      <p><strong>Customer ID:</strong> <?= htmlspecialchars($row['customerID']) ?></p>
      <p><strong>Service ID:</strong> <?= htmlspecialchars($row['serviceID']) ?></p>
      <p><strong>Premise ID:</strong> <?= htmlspecialchars($row['premiseID']) ?></p>
      <p><strong>Quantity:</strong> <?= htmlspecialchars($row['Quantity']) ?></p>
      <p><strong>Location:</strong> <?= htmlspecialchars($row['Location']) ?></p>
      <p><strong>Preferred Date:</strong> <?= htmlspecialchars($row['preferredDate']) ?></p>
      <p><strong>Additional Notes:</strong> <?= htmlspecialchars($row['Additional_Notes']) ?></p>

      <form method="POST" onsubmit="return confirm('Mark this request as done?');">
        <input type="hidden" name="customerID" value="<?= $row['customerID'] ?>" />
        <input type="hidden" name="serviceID" value="<?= $row['serviceID'] ?>" />
        <button type="submit" name="done" class="btn-done">DONE</button>
      </form>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p>No pending customer requests.</p>
<?php endif; ?>

<p class="note">*WARNING: When “DELETE” is clicked, both the schedule and order will be deleted.</p>

<script>
function searchTable() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const rows = document.querySelectorAll(".schedule-table tbody tr");
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(input) ? "" : "none";
  });
}
</script>
</body>
</html>
