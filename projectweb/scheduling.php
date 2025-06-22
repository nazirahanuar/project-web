<?php
include 'connect.php';

$successMessage = "";

// Get available serials (not yet used in schedule)
$serialOptions = [];
$serialQuery = $conn->query("
  SELECT serialNo FROM fire_extinguisher
  WHERE serialNo NOT IN (SELECT serialNo FROM schedule)
  ORDER BY serialNo ASC
");
if ($serialQuery) {
  while ($row = $serialQuery->fetch_assoc()) {
    $serialOptions[] = $row['serialNo'];
  }
}

// Handle Create Schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create"])) {
  $staffID = $_POST["staffID"];
  $orderID = $_POST["orderID"];
  $serialNo = $_POST["serialNo"];
  $scheduleDate = $_POST["scheduleDate"];
  $location = $_POST["location"];
  $adminID = $_POST["adminID"];

  if ($staffID && $orderID && $serialNo && $scheduleDate && $location && $adminID) {
    $stmt = $conn->prepare("INSERT INTO schedule (orderID, serialNo, staffID, Location, scheduleDate, adminID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $orderID, $serialNo, $staffID, $location, $scheduleDate, $adminID);

    if ($stmt->execute()) {
      $successMessage = "Schedule created successfully.";
    }
  }
}

// Handle Delete Schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_orderID"], $_POST["delete_serialNo"], $_POST["delete_adminID"])) {
  $orderID = $_POST["delete_orderID"];
  $serialNo = $_POST["delete_serialNo"];
  $adminID = $_POST["delete_adminID"];

  $stmt = $conn->prepare("DELETE FROM schedule WHERE orderID = ? AND serialNo = ? AND adminID = ?");
  $stmt->bind_param("sss", $orderID, $serialNo, $adminID);
  $stmt->execute();
}
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
    <p class="note">Create schedules for our committed staffs and customers to view.</p>

    <?php if (!empty($successMessage)) echo "<p style='color: lightgreen; text-align: center; font-weight: bold;'>$successMessage</p>"; ?>

    <form method="POST" class="schedule-form black-box">
      <div class="form-grid">
        <div class="form-group">
          <label for="orderId">Order ID</label>
          <input type="text" name="orderID" id="orderId" required />
        </div>

        <div class="form-group">
          <label for="serialNo">Serial No.</label>
          <select name="serialNo" id="serialNo" required>
            <option value="">-- Select Serial No. --</option>
            <?php foreach ($serialOptions as $serial): ?>
              <option value="<?= htmlspecialchars($serial) ?>"><?= htmlspecialchars($serial) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="staffId">Staff ID</label>
          <input type="text" name="staffID" id="staffId" required />
        </div>

        <div class="form-group">
          <label for="scheduleDate">Schedule Date</label>
          <input type="date" name="scheduleDate" id="scheduleDate" required />
        </div>

        <div class="form-group full-width">
          <label for="location">Location</label>
          <textarea name="location" id="location" rows="3" required></textarea>
        </div>

        <div class="form-group">
          <label for="adminId">Admin ID</label>
          <input type="text" name="adminID" id="adminId" required />
        </div>
      </div>

      <div class="form-submit-center">
        <button type="submit" name="create" class="btn-create">CREATE</button>
      </div>
    </form>
  </section>

  <hr>
  <h2 class="title">SCHEDULE DETAILS</h2>
  <p class="note">Click “DELETE” to delete the selected row.</p>

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search" />
    <button class="search-icon" onclick="filterTable()" type="button">🔍</button>
  </div>

  <div class="table-wrapper">
    <table class="schedule-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Serial No.</th>
          <th>Staff ID</th>
          <th>Schedule Date</th>
          <th>Location</th>
          <th>Admin ID</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="scheduleTableBody">
        <?php
        $result = $conn->query("SELECT * FROM schedule");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
              <td>{$row['orderID']}</td>
              <td>{$row['serialNo']}</td>
              <td>{$row['staffID']}</td>
              <td>{$row['scheduleDate']}</td>
              <td>{$row['Location']}</td>
              <td>{$row['adminID']}</td>
              <td>
                <form method='POST' onsubmit='return confirm(\"Delete this schedule?\");'>
                  <input type='hidden' name='delete_orderID' value='{$row['orderID']}'>
                  <input type='hidden' name='delete_serialNo' value='{$row['serialNo']}'>
                  <input type='hidden' name='delete_adminID' value='{$row['adminID']}'>
                  <button type='submit' class='btn-delete'>DELETE</button>
                </form>
              </td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='7'>No schedule found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <script>
    function filterTable() {
      const searchValue = document.getElementById('searchInput').value.toLowerCase();
      const rows = document.querySelectorAll('#scheduleTableBody tr');
      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchValue) ? '' : 'none';
      });
    }
  </script>
</body>
</html>
