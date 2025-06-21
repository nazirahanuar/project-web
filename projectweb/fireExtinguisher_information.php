<?php
include 'connect.php';

// ADD NEW EXTINGUISHER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $serialNo = $_POST['serialNo'];
  $type = $_POST['fireExtinguisherType'];
  $expiredDate = $_POST['expiredDate'];

  if ($serialNo && $type && $expiredDate) {
    $check = $conn->prepare("SELECT * FROM FIRE_EXTINGUISHER WHERE serialNo = ?");
    $check->bind_param("s", $serialNo);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
      echo "<script>alert('Serial number already exists.');</script>";
    } else {
      $insert = $conn->prepare("INSERT INTO FIRE_EXTINGUISHER (serialNo, fireExtinguisherType, expiredDate) VALUES (?, ?, ?)");
      $insert->bind_param("sss", $serialNo, $type, $expiredDate);
      $insert->execute();
      echo "<script>window.location.href='fireExtinguisher_information.php';</script>";
    }
  }
}

// DELETE EXTINGUISHER
if (isset($_GET['delete'])) {
  $deleteID = $_GET['delete'];
  $delete = $conn->prepare("DELETE FROM FIRE_EXTINGUISHER WHERE serialNo = ?");
  $delete->bind_param("s", $deleteID);
  $delete->execute();
  echo "<script>window.location.href='fireExtinguisher_information.php';</script>";
}

// FETCH ALL EXTINGUISHERS
$data = $conn->query("SELECT * FROM FIRE_EXTINGUISHER");
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
      <form method="POST" action="">
        <label>Serial No.</label>
        <input type="text" name="serialNo" required />

        <label>Fire Extinguisher Type</label>
        <input type="text" name="fireExtinguisherType" required />

        <label>Expiration Date</label>
        <input type="date" name="expiredDate" required />

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
      <?php while ($row = $data->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['serialNo']) ?></td>
          <td><?= htmlspecialchars($row['fireExtinguisherType']) ?></td>
          <td><?= htmlspecialchars($row['expiredDate']) ?></td>
          <td>
            <a href="?delete=<?= urlencode($row['serialNo']) ?>" onclick="return confirm('Delete this extinguisher?')">
              <button class="delete-btn">DELETE</button>
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
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
