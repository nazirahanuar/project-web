<?php
include 'connect.php';

// Handle new submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Delete action
  if (isset($_POST['delete_serial'])) {
    $deleteSerial = $_POST['delete_serial'];
    $deleteStmt = $conn->prepare("DELETE FROM fire_extinguisher WHERE serialNo = ?");
    $deleteStmt->bind_param("s", $deleteSerial);
    if ($deleteStmt->execute()) {
      echo "<script>alert('Fire extinguisher deleted successfully.');</script>";
    } else {
      echo "<script>alert('Error deleting record.');</script>";
    }
  }

  // Add action
  elseif (isset($_POST['serial'], $_POST['type'], $_POST['date'])) {
    $serialNo = trim($_POST['serial']);
    $type = trim($_POST['type']);
    $date = $_POST['date'];

    if (!empty($serialNo) && !empty($type) && !empty($date)) {
      $check = $conn->prepare("SELECT serialNo FROM fire_extinguisher WHERE serialNo = ?");
      $check->bind_param("s", $serialNo);
      $check->execute();
      $result = $check->get_result();

      if ($result->num_rows > 0) {
        echo "<script>alert('Serial No. already exists!');</script>";
      } else {
        $stmt = $conn->prepare("INSERT INTO fire_extinguisher (serialNo, fireextinguisherType, expiredDate) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $serialNo, $type, $date);
        if ($stmt->execute()) {
          echo "<script>alert('Fire extinguisher added successfully.');</script>";
        } else {
          echo "<script>alert('Error adding record.');</script>";
        }
      }
    } else {
      echo "<script>alert('Please fill in all fields.');</script>";
    }
  }
}

// Fetch all extinguisher data
$extinguishers = $conn->query("SELECT * FROM fire_extinguisher");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fire Extinguisher Information</title>
  <link rel="stylesheet" href="adminFormat.css" />
</head>
<body class="admin">
  <nav class="navbar">
    <div class="nav-left">
      <a href="adminHome.html" class="nav-item">HOME</a>
      <a href="request_management.html" class="nav-item">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.php" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item active">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="#" class="nav-item">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

  <section class="fire-extinguisher-form-section">
    <h2 class="title">CREATE AND VIEW FIRE EXTINGUISHER</h2>
    <p class="note">Add and view the fire extinguishers.</p>

    <div class="form-card">
      <h2>ADD FIRE EXTINGUISHER</h2>
      <form method="POST" id="extinguisherForm">
        <div class="form-row">
          <div class="input-group">
            <label>Serial No.</label>
            <input type="text" name="serial" required />
          </div>
          <div class="input-group">
            <label>Type</label>
            <input type="text" name="type" required />
          </div>
        </div>
        <div class="input-group">
          <label>Expiration Date</label>
          <input type="date" name="date" required />
        </div>
        <button type="submit" class="submit-btn">ADD</button>
      </form>
    </div>
  </section>

  <hr>
  <h2 class="title">FIRE EXTINGUISHER INFORMATION</h2>
  <section class="fire-extinguisher-table-section">
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search" />
      <button class="search-icon" onclick="filterTable()" type="button">🔍</button>
    </div>

    <table id="extinguisherTable">
      <thead>
        <tr>
          <th>Serial No.</th>
          <th>Type</th>
          <th>Expiration Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $extinguishers->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['serialNo']) ?></td>
          <td><?= htmlspecialchars($row['fireextinguisherType']) ?></td>
          <td><?= date("d/m/Y", strtotime($row['expiredDate'])) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');" style="display:inline;">
              <input type="hidden" name="delete_serial" value="<?= htmlspecialchars($row['serialNo']) ?>">
              <button type="submit" class="delete-btn">DELETE</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>

  <script>
    function filterTable() {
      const filter = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#extinguisherTable tbody tr");

      rows.forEach(row => {
        const match = Array.from(row.cells).some(cell =>
          cell.textContent.toLowerCase().includes(filter)
        );
        row.style.display = match ? "" : "none";
      });
    }

    document.getElementById("searchInput").addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        filterTable();
      }
    });
  </script>
</body>
</html>
