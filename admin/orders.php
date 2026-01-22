<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
include "../db_config.php";

$res = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Orders</title>
</head>
<body>

<h2>Orders</h2>

<table border="1" cellpadding="8">
<tr>
  <th>Order ID</th>
  <th>User ID</th>
  <th>Total</th>
  <th>Final</th>
  <th>Payment</th>
  <th>Status</th>
  <th>Date</th>
  <th>Action</th>
</tr>

<?php while($o = $res->fetch_assoc()) { ?>
<tr>
  <td><?php echo $o["id"]; ?></td>
  <td><?php echo $o["user_id"]; ?></td>
  <td>₹<?php echo $o["order_total"]; ?></td>
  <td>₹<?php echo $o["final_amount"]; ?></td>
  <td><?php echo $o["payment_method"]; ?></td>
  <td><?php echo $o["order_status"]; ?></td>
  <td><?php echo $o["created_at"]; ?></td>
  <td>
    <a href="view_order.php?id=<?php echo $o["id"]; ?>">View</a>
  </td>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard.php">⬅ Back</a>

</body>
</html>
