<?php
include 'connect.php';

// Handle delete
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_order_id'])) {
  $orderID = $_POST['delete_order_id'];
  $stmt = $conn->prepare("DELETE FROM orders WHERE orderID = ?");
  $stmt->bind_param("s", $orderID);
  $stmt->execute();
  echo "<script>alert('Order deleted successfully.');</script>";
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
      <a href="adminHome.php" class="nav-item">HOME</a>
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

  <div class="back-button-container">
    <a href="request_management.php" class="back-button">⇤</a>
  </div>

  <div class="container">
    <h2 class="title">ORDER DETAILS 📝</h2>
    <table id="order-table" class="order-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer ID</th>
          <th>Service ID</th>
          <th>Premise ID</th>
          <th>Quantity</th>
          <th>Serial No.</th>
          <th>Location</th>
          <th>Preferred Date</th>
          <th>Additional Notes</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $orderResult = $conn->query("SELECT o.*, r.serviceID, r.premiseID, r.Location, r.preferredDate FROM orders o JOIN request r ON o.customerID = r.customerID");
        if ($orderResult && $orderResult->num_rows > 0) {
          while ($row = $orderResult->fetch_assoc()) {
            echo "<tr>
              <td>{$row['orderID']}</td>
              <td>{$row['customerID']}</td>
              <td>{$row['serviceID']}</td>
              <td>{$row['premiseID']}</td>
              <td>{$row['Quantity']}</td>
              <td>{$row['serialNo']}</td>
              <td>{$row['Location']}</td>
              <td>{$row['preferredDate']}</td>
              <td>{$row['Additional_Notes']}</td>
              <td>
                <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this order?\");'>
                  <input type='hidden' name='delete_order_id' value='{$row['orderID']}'>
                  <button type='submit' class='delete-btn'>DELETE</button>
                </form>
              </td>
            </tr>";
          }
        } else {
          echo "<tr><td colspan='10'>No orders found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
