<?php
include 'connect.php';

$successMessage = "";

// Fetch Premise IDs
$premiseOptions = [];
$premiseQuery = $conn->query("SELECT premiseID FROM premise");
if ($premiseQuery) {
  while ($row = $premiseQuery->fetch_assoc()) {
    $premiseOptions[] = $row['premiseID'];
  }
}

// Fetch Service IDs
$serviceOptions = [];
$serviceQuery = $conn->query("SELECT serviceID FROM service");
if ($serviceQuery) {
  while ($row = $serviceQuery->fetch_assoc()) {
    $serviceOptions[] = $row['serviceID'];
  }
}

// Handle Create Schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create"])) {
  $staffID = $_POST["staffID"];
  $orderID = $_POST["orderID"];
  $scheduleDate = $_POST["scheduleDate"];
  $location = $_POST["location"];
  $serviceID = $_POST["serviceID"];
  $premiseID = $_POST["premiseID"];
  $adminID = $_POST["adminID"];

  if ($staffID && $orderID && $scheduleDate && $location && $serviceID && $premiseID && $adminID) {
    $stmt = $conn->prepare("INSERT INTO schedule (staffID, orderID, scheduleDate, Location, serviceID, premiseID, adminID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $staffID, $orderID, $scheduleDate, $location, $serviceID, $premiseID, $adminID);

    if ($stmt->execute()) {
      $successMessage = "Schedule created successfully.";
    }
  }
}

// Handle Delete Schedule
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_schedule_id"])) {
  $deleteID = $_POST["delete_schedule_id"];
  $stmt = $conn->prepare("DELETE FROM schedule WHERE staffID = ?");
  $stmt->bind_param("s", $deleteID);
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
          <label for="staffId">Staff ID</label>
          <input type="text" name="staffID" id="staffId" required />
        </div>

        <div class="form-group">
          <label for="orderId">Order ID</label>
          <input type="text" name="orderID" id="orderId" required />
        </div>

        <div class="form-group">
          <label for="scheduleDate">Schedule Date</label>
          <input type="date" name="scheduleDate" id="scheduleDate" required />
        </div>

        <div class="form-group">
          <label for="serviceId">Service ID</label>
          <select name="serviceID" id="serviceId" required>
            <option value="">-- Select Service --</option>
            <?php foreach ($serviceOptions as $sid): ?>
              <option value="<?= htmlspecialchars($sid) ?>"><?= htmlspecialchars($sid) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group full-width">
          <label for="location">Location</label>
          <textarea name="location" id="location" rows="3" required></textarea>
        </div>

        <div class="form-group">
          <label for="premiseId">Premise ID</label>
          <select name="premiseID" id="premiseId" required>
            <option value="">-- Select Premise --</option>
            <?php foreach ($premiseOptions as $pid): ?>
              <option value="<?= htmlspecialchars($pid) ?>"><?= htmlspecialchars($pid) ?></option>
            <?php endforeach; ?>
          </select>
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
      <tbody id="scheduleTableBody">
        <?php
        $result = $conn->query("SELECT * FROM schedule");
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
              <td>{$row['staffID']}</td>
              <td>{$row['orderID']}</td>
              <td>{$row['scheduleDate']}</td>
              <td>{$row['Location']}</td>
              <td>{$row['serviceID']}</td>
              <td>{$row['premiseID']}</td>
              <td>{$row['adminID']}</td>
              <td>
                <form method='POST' onsubmit='return confirm(\"Delete this schedule?\");'>
                  <input type='hidden' name='delete_schedule_id' value='{$row['staffID']}'>
                  <button type='submit' class='btn-delete'>DELETE</button>
                </form>
              </td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='8'>No schedule found.</td></tr>";
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
