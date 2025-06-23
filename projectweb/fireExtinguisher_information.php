<?php
include 'connect.php';

// ADD NEW EXTINGUISHER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $serialNo = $_POST['serialNo'];
  $type = $_POST['fireExtinguisherType'];
  $expiredDate = $_POST['expiredDate'];
  $status = $_POST['status'];

  if ($serialNo && $type && $expiredDate && $status) {
    $check = $conn->prepare("SELECT * FROM FIRE_EXTINGUISHER WHERE serialNo = ?");
    $check->bind_param("s", $serialNo);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
      echo "<script>alert('Serial number already exists.');</script>";
    } else {
      $insert = $conn->prepare("INSERT INTO FIRE_EXTINGUISHER (serialNo, fireExtinguisherType, expiredDate, status) VALUES (?, ?, ?, ?)");
      $insert->bind_param("ssss", $serialNo, $type, $expiredDate, $status);
      $insert->execute();
      echo "<script>alert('Fire extinguisher added successfully.'); window.location.href='fireExtinguisher_information.php';</script>";
    }
  }
}

// UPDATE STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $serialNo = $_POST['serialNo'];
  $status = $_POST['status'];

  $stmt = $conn->prepare("UPDATE FIRE_EXTINGUISHER SET status = ? WHERE serialNo = ?");
  $stmt->bind_param("ss", $status, $serialNo);
  $stmt->execute();
  echo "<script>alert('Update successfully.'); window.location.href='fireExtinguisher_information.php';</script>";
}

// DELETE EXTINGUISHER
if (isset($_GET['delete'])) {
  $deleteID = $_GET['delete'];
  $delete = $conn->prepare("DELETE FROM FIRE_EXTINGUISHER WHERE serialNo = ?");
  $delete->bind_param("s", $deleteID);
  $delete->execute();
  echo "<script>alert('Fire extinguisher deleted successfully.'); window.location.href='fireExtinguisher_information.php';</script>";
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
      <a href="adminHome.php" class="nav-item">HOME</a>
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
  <p class="note">View and manage the fire extinguishers.</p>

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

        <div class="form-group">
          <label>Status</label>
          <select name="status" required>
            <option value="">-- Select Status --</option>
            <option value="Available">Available</option>
            <option value="Unavailable">Unavailable</option>
          </select>
        </div>

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
        <th>Status</th>
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
            <form method="POST" style="display: flex; gap: 5px;">
              <input type="hidden" name="serialNo" value="<?= $row['serialNo'] ?>" />
              <div class="form-group">
                <select name="status" required>
                  <option value="Available" <?= $row['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                  <option value="Unavailable" <?= $row['status'] === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
                </select>
              </div>
              <button type="submit" name="update_status" class="update-btn">Update</button>
            </form>
          </td>
          <td>
            <form method="GET" onsubmit="return confirmDelete();">
              <input type="hidden" name="delete" value="<?= htmlspecialchars($row['serialNo']) ?>">
              <button type="submit" class="delete-btn">DELETE</button>
            </form>
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

    function confirmDelete() {
      return confirm("Are you sure you want to delete this details?");
    }
  </script>
</body>
</html>
