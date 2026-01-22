<?php
session_start();
if (!isset($_SESSION["admin"])) {
  header("Location: login.php");
  exit;
}

require "../db_config.php";

// ADD VIDEO
if (isset($_POST["add"])) {
  $title = $_POST["title"];
  $video_url = $_POST["video_url"];

  $stmt = $conn->prepare("INSERT INTO home_minivideos (title, video_url) VALUES (?, ?)");
  $stmt->bind_param("ss", $title, $video_url);
  $stmt->execute();
}

// TOGGLE
if (isset($_GET["toggle"])) {
  $id = (int)$_GET["toggle"];
  $conn->query("UPDATE home_minivideos SET is_active = IF(is_active=1,0,1) WHERE id=$id");
}

// DELETE
if (isset($_GET["delete"])) {
  $id = (int)$_GET["delete"];
  $conn->query("DELETE FROM home_minivideos WHERE id=$id");
}

$result = $conn->query("SELECT * FROM home_minivideos ORDER BY id DESC");
?>

<h2>Mini Video Manager</h2>

<form method="post">
  <input name="title" placeholder="Title" required>
  <input name="video_url" placeholder="Video URL (https mp4)" required style="width:400px">
  <button name="add">Add Video</button>
</form>

<hr>

<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Title</th>
  <th>Video</th>
  <th>Status</th>
  <th>Actions</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row["id"] ?></td>
  <td><?= htmlspecialchars($row["title"]) ?></td>
  <td style="max-width:300px;word-break:break-all;"><?= htmlspecialchars($row["video_url"]) ?></td>
  <td><?= $row["is_active"] ? "ACTIVE" : "OFF" ?></td>
  <td>
    <a href="?toggle=<?= $row["id"] ?>">Toggle</a> |
    <a href="?delete=<?= $row["id"] ?>" onclick="return confirm('Delete?')">Delete</a>
  </td>
</tr>
<?php endwhile; ?>
</table>

<br>
<a href="dashboard.php">← Back to Dashboard</a>
