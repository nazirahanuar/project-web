<?php
include 'connect.php';

// Fetch premise data
$premises = $conn->query("SELECT premiseID, premiseType FROM premise");

// Fetch service data
$services = $conn->query("SELECT serviceID, serviceType FROM service");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Request Service</title>
  <link rel="stylesheet" href="customer.css" />
</head>
<body id="top" class="customer">
  <nav class="navbar">
    <div class="nav-left">
      <a href="customerHome.php" class="nav-item">HOME</a>
      <a href="knowledgeHub.html" class="nav-item">KNOWLEDGE<br>HUB</a>
      <a href="requestService.php" class="nav-item">REQUEST<br>SERVICE</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="mySchedule.php" class="nav-item active">MY SCHEDULE</a>
      <a href="custProfile.php#profile" class="nav-item">PROFILE</a>
    </div>
  </nav>

  <!-- My Schedule Section -->
  <section class="schedule-section">
    <h1 class="main-schedule-title">MY SCHEDULE</h1>

    <div class="order-id-box">
      <p class="order-label">
        Your Order ID: 
        <input type="text" id="orderID" name="orderID" placeholder="Enter Order ID" oninput="checkOrderID()">
      </p>
    </div>

    <p id="errorMsg" style="color: red; font-weight: bold; margin-left: 10px;"></p>

    <div class="schedule-table-box" id="scheduleBox" style="display: none;">
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
          </tr>
        </thead>
      </table>
    </div>

    <p class="profile-note">
      Don't know your Order ID? Go to 
      <a href="custProfile.php#profile" style="font-weight: bold; text-decoration: none; color: inherit;">
        PROFILE
      </a>
    </p>
  </section>

  <!-- Reference Section -->
  <section class="reference-section">
    <h2 class="sec-heading">[ FOR REFERENCE ]</h2>
    <div class="reference-grids">
      <div>
        <h3 class="subheading">PREMISE DETAILS</h3>
        <table class="reference-table">
          <thead>
            <tr>
              <th>Premise ID</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $premises->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['premiseID']) ?></td>
                <td><?= htmlspecialchars($row['premiseType']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <div>
        <h3 class="subheading">SERVICE DETAILS</h3>
        <table class="reference-table">
          <thead>
            <tr>
              <th>Service ID</th>
              <th>Type</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $services->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['serviceID']) ?></td>
                <td><?= htmlspecialchars($row['serviceType']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

<script>
  function checkOrderID() {
    const orderID = document.getElementById("orderID").value.trim().toUpperCase();
    const errorMsg = document.getElementById("errorMsg");
    const scheduleBox = document.getElementById("scheduleBox");
    const table = document.querySelector(".schedule-table");

    if (orderID === "") {
      errorMsg.textContent = "";
      scheduleBox.style.display = "none";
      return;
    }

    fetch('checkOrder.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'orderID=' + encodeURIComponent(orderID)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        errorMsg.textContent = "";
        scheduleBox.style.display = "block";

        // Remove old rows
        const oldRows = table.querySelectorAll("tbody tr");
        oldRows.forEach(row => row.remove());

        let tbody = table.querySelector("tbody");
        if (!tbody) {
          tbody = document.createElement("tbody");
          table.appendChild(tbody);
        }

        const s = data.data;
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${s.staffID}</td>
          <td>${s.orderID}</td>
          <td>${s.scheduleDate}</td>
          <td>${s.Location}</td>
          <td>${s.serviceID}</td>
          <td>${s.premiseID}</td>
          <td>${s.adminID}</td>
        `;
        tbody.appendChild(row);
      } else {
        errorMsg.textContent = data.message;
        scheduleBox.style.display = "none";
      }
    });
  }
</script>
</body>
</html>