<?php
session_start();
include "../db_config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST["username"];
    $p = md5($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $_SESSION["admin"] = $u;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid login";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>SabJab Admin Login</title>
</head>
<body>
  <h2>SabJab Admin Login</h2>
  <form method="post">
    <input name="username" placeholder="Username" required><br><br>
    <input name="password" type="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
  </form>
  <p style="color:red;"><?php echo $error; ?></p>
</body>
</html>
