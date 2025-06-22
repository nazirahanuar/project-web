<?php
include 'connect.php';

// Fetch serial numbers from fire_extinguisher
$serialOptions = [];
$serialQuery = $conn->query("SELECT serialNo FROM fire_extinguisher ORDER BY serialNo ASC");
while ($row = $serialQuery->fetch_assoc()) {
  $serialOptions[] = $row['serialNo'];
}
$serialsJS = json_encode($serialOptions);

// Handle Create Order
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create"])) {
  $orderID = $_POST["orderID"];
  $customerID = $_POST["customerID"];
  $quantity = isset($_POST["quantity"]) && $_POST["quantity"] !== '' ? intval($_POST["quantity"]) : 0;
  $additionalNotes = $_POST["additionalNotes"] ?? '';
  $serials = isset($_POST["serials"]) ? implode(", ", $_POST["serials"]) : "";

  // Check for duplicate Order ID
  $check = $conn->prepare("SELECT * FROM orders WHERE orderID = ?");
  $check->bind_param("s", $orderID);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Order ID already exists in the database.');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO orders (orderID, serialNo, customerID, Quantity, Additional_Notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $orderID, $serials, $customerID, $quantity, $additionalNotes);
    if ($stmt->execute()) {
      echo "<script>alert('Order is created successfully.');</script>";
    } else {
      echo "<script>alert('Failed to create order.');</script>";
    }
  }
}

// Handle Delete Request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["done"])) {
  $customerID = $_POST["customerID"];
  $stmt = $conn->prepare("DELETE FROM request WHERE customerID = ?");
  $stmt->bind_param("s", $customerID);
  $stmt->execute();
  echo "<script>alert('Request deleted successfully.');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Request Management</title>
  <link rel="stylesheet" href="adminFormat.css" />
</head>
<body class="admin">
  <nav class="navbar">
    <div class="nav-left">
      <a href="adminHome.html" class="nav-item">HOME</a>
      <a href="request_management.php" class="nav-item active">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.php" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

  <h2 class="title">CREATE ORDER</h2>
  <p class="note">Click "CREATE" once you've finished creating the order based on the customer request below.</p>
  <div class="container">
    <form method="POST" class="create-order">
      <h3 class="sub-title">ORDER DETAILS</h3>
      <label>Order ID</label>
      <input type="text" name="orderID" id="order-id" required />

      <label>Customer ID</label>
      <input type="text" name="customerID" id="customer-id" required />

      <label>Quantity of Fire Extinguisher</label>
      <input type="number" name="quantity" id="quantity" value="0" min="0" />

      <label>Serial No.</label>
      <div id="serial-container"></div>

      <label>Additional Notes</label>
      <textarea name="additionalNotes" id="additional-notes"></textarea>

      <button type="submit" name="create" id="create-btn">CREATE</button>
    </form>

    <a id="see-order-details" href="orderDetails.php">SEE ORDER DETAILS</a>

    <hr>
    <h2 class="title">CUSTOMER REQUEST</h2>
    <p class="note">Click "DONE" once you've finished creating the order and schedule.</p>

    <?php
    $result = $conn->query("SELECT * FROM request LIMIT 1");
    if ($result && $row = $result->fetch_assoc()):
    ?>
    <div id="customer-request" class="customer-request">
      <p><strong>Customer ID:</strong> <?= $row['customerID'] ?></p>
      <p><strong>Service ID:</strong> <?= $row['serviceID'] ?></p>
      <p><strong>Premise ID:</strong> <?= $row['premiseID'] ?></p>
      <p><strong>Quantity:</strong> <?= $row['Quantity'] ?></p>
      <p><strong>Location:</strong> <?= $row['Location'] ?></p>
      <p><strong>Preferred Date:</strong> <?= $row['preferredDate'] ?></p>
      <p><strong>Additional Notes:</strong> <?= $row['Additional_Notes'] ?></p>

      <form method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
        <input type="hidden" name="customerID" value="<?= $row['customerID'] ?>" />
        <button type="submit" name="done" id="done-btn">DONE</button>
      </form>
    </div>
    <?php else: ?>
      <p>No customer request found.</p>
    <?php endif; ?>

    <p class="note">*WARNING: Once the “DONE” button is clicked, the customer request details
    will be removed from this page as a mark that you’ve created an order and
    schedule.</p>
  </div>

  <script>
    const serialOptions = <?= $serialsJS ?>;

    function generateSerialDropdowns(quantity) {
      const container = document.getElementById('serial-container');
      container.innerHTML = '';

      if (quantity < 1) return;

      for (let i = 1; i <= quantity; i++) {
        const label = document.createElement('label');
        label.textContent = `Serial No. ${i}`;

        const select = document.createElement('select');
        select.name = "serials[]";
        select.required = true;

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'Select';
        select.appendChild(defaultOption);

        serialOptions.forEach(serial => {
          const option = document.createElement('option');
          option.value = serial;
          option.textContent = serial;
          select.appendChild(option);
        });

        label.appendChild(select);
        container.appendChild(label);
      }
    }

    document.getElementById('quantity').addEventListener('input', e => {
      const qty = parseInt(e.target.value, 10) || 0;
      generateSerialDropdowns(qty);
    });

    window.addEventListener('DOMContentLoaded', () => {
      const initialQty = parseInt(document.getElementById('quantity').value, 10);
      if (initialQty > 0) {
        generateSerialDropdowns(initialQty);
      }
    });
  </script>
</body>
</html>
