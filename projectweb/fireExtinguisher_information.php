<?php
include 'connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Delete extinguisher
  if (isset($_POST['delete_serial'])) {
    $deleteSerial = $_POST['delete_serial'];
    $stmt = $conn->prepare("DELETE FROM fire_extinguisher WHERE serialNo = ?");
    $stmt->bind_param("s", $deleteSerial);
    $stmt->execute();
  }

  // Add extinguisher
  elseif (isset($_POST['serial'], $_POST['type'], $_POST['date'])) {
    $serial = trim($_POST['serial']);
    $type = trim($_POST['type']);
    $date = $_POST['date'];

    if (!empty($serial) && !empty($type) && !empty($date)) {
      $check = $conn->prepare("SELECT serialNo FROM fire_extinguisher WHERE serialNo = ?");
      $check->bind_param("s", $serial);
      $check->execute();
      $result = $check->get_result();

      if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO fire_extinguisher (serialNo, fireextinguisherType, expiredDate) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $serial, $type, $date);
        $stmt->execute();
      }
    }
  }
}

// Fetch data
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
      <a href="scheduling.html" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item active">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

  <h2 class="title">CREATE AND VIEW FIRE EXTINGUISHER</h2>
  <p class="note">Add and view the fire extinguishers.</p>

  <section class="fire-extinguisher-form-section">
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
