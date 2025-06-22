<?php
include 'connect.php';

// Retrieve all premises
$premiseQuery = "SELECT * FROM premise";
$premiseResult = $conn->query($premiseQuery);

// Retrieve all services
$serviceQuery = "SELECT * FROM service";
$serviceResult = $conn->query($serviceQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Request Service</title>
  <link rel="stylesheet" href="customer.css">
</head>
<body id="top" class="customer">

  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-left">
      <a href="customerHome.php" class="nav-item">HOME</a>
      <a href="knowledgeHub.html" class="nav-item">KNOWLEDGE<br>HUB</a>
      <a href="requestService.php" class="nav-item active">REQUEST<br>SERVICE</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="mySchedule.html" class="nav-item">MY SCHEDULE</a>
      <a href="custProfile.php" class="nav-item">PROFILE</a>
    </div>
  </nav>

  <h1 class="sec-title">REQUEST SERVICE</h1>

  <!-- Request Form -->
  <div class="request-form">
    <form action="submitRequest.php" method="POST">
      <div class="form-row">
        <div class="form-group">
          <label for="customerId">Customer ID</label>
          <input type="text" id="customerId" name="customerID" required>
        </div>

        <div class="form-group">
          <label for="serviceId">Service Type</label>
          <select id="serviceId" name="serviceID" required>
            <option value="">Select Service</option>
            <?php while ($row = $serviceResult->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($row['serviceID']) ?>">
                <?= htmlspecialchars($row['serviceType']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="premiseId">Premise Type</label>
          <select id="premiseId" name="premiseID" required>
            <option value="">Select Premise</option>
            <?php while ($row = $premiseResult->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($row['premiseID']) ?>">
                <?= htmlspecialchars($row['premiseType']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="preferredDate">Preferred Date</label>
          <input type="date" id="preferredDate" name="preferredDate" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="quantity">Quantity of Fire Extinguisher</label>
          <input type="number" id="quantity" name="quantity" min="0" max="200" value="0" required>
        </div>
      </div>

      <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" required>
      </div>

      <div class="form-group">
        <label for="notes">Additional Notes</label>
        <textarea id="notes" name="notes" placeholder="e.g. ABC Fire Extinguisher"></textarea>
      </div>

      <button type="submit" class="submit-btn">SUBMIT</button>
    </form>
  </div>

  <p class="note">*Note: We will assign the fire extinguishers based on the suitability of the premises. Please specify the types of fire extinguishers you require in the ‘Additional Notes’ section.</p>

</body>
</html>
