<?php
include 'projectweb/connect.php';

// Handle Add
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
  $serialNo = $_POST["serialNo"];
  $type = $_POST["type"];
  $expirationDate = $_POST["expirationDate"];

  // Check if serial number exists
  $check = $conn->prepare("SELECT serialNo FROM fire_extinguisher WHERE serialNo = ?");
  $check->bind_param("s", $serialNo);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Serial number already exists in the database.');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO fire_extinguisher (serialNo, fireExtinguisherType, expiredDate) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $serialNo, $type, $expirationDate);
    if ($stmt->execute()) {
      echo "<script>alert('Fire extinguisher added successfully.');</script>";
    } else {
      echo "<script>alert('Failed to add extinguisher.');</script>";
    }
  }
}

// Handle Delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
  $deleteSerial = $_POST["delete"];
  $stmt = $conn->prepare("DELETE FROM fire_extinguisher WHERE serialNo = ?");
  $stmt->bind_param("s", $deleteSerial);
  if ($stmt->execute()) {
    echo "<script>alert('Fire extinguisher deleted.');</script>";
  }
}
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
      <a href="request_management.php" class="nav-item">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.php" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item active">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

  <h2 class="title">FIRE EXTINGUISHER INFORMATION</h2>
  <p class="note">Add and view the fire extinguishers.</p>

  <div class="extinguisher-container">
    <div class="form-container">
      <h2 class="sub-title">ADD FIRE EXTINGUISHER</h2>
      <form method="POST">
        <label>Serial No.</label>
        <input type="text" name="serialNo" required />

        <label>Fire Extinguisher Type</label>
        <input type="text" name="type" required />

        <label>Expiration Date</label>
        <input type="date" name="expirationDate" required />

        <button type="submit" name="add">ADD</button>
      </form>
    </div>
  </div>

  <hr />

  <div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search" />
    <button class="search-icon" onclick="searchTable()" type="button">🔍</button>
  </div>

  <table id="extinguisher-table" class="extinguisher-table">
    <thead>
      <tr>
        <th>Serial No.</th>
        <th>Type</th>
        <th>Expiration Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="fireExtinguisherTable">
      <?php
      $result = $conn->query("SELECT * FROM fire_extinguisher ORDER BY serialNo ASC");
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['serialNo']}</td>
                <td>{$row['fireExtinguisherType']}</td>
                <td>{$row['expiredDate']}</td>
                <td>
                  <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this item?\")'>
                    <input type='hidden' name='delete' value='{$row['serialNo']}' />
                    <button type='submit' class='delete-btn'>DELETE</button>
                  </form>
                </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>

  <script>
    function searchTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.getElementById("fireExtinguisherTable").getElementsByTagName("tr");

      for (let row of rows) {
        const cells = row.getElementsByTagName("td");
        let match = false;
        for (let i = 0; i < cells.length - 1; i++) {
          if (cells[i].textContent.toLowerCase().includes(input)) {
            match = true;
            break;
          }
        }
        row.style.display = match ? "" : "none";
      }
    }
  </script>
</body>
</html>
