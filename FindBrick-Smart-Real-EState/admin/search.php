<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
  header("location:dashboard.php");
  exit();
}

if (isset($_GET['q'])) {
  $q = mysqli_real_escape_string($con, $_GET['q']);

  // Example: Search in properties
  $sql = "SELECT * FROM property WHERE title LIKE '%$q%' OR pcontent LIKE '%$q%'";
  $result = mysqli_query($con, $sql);
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Search Results - FB Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

</head>

<body class="p-4">
  <h3>Search Results for "<?php echo htmlspecialchars($q); ?>"</h3>
  <table class="table table-bordered mt-3">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['pid']; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['status']; ?></td>
      </tr>
    <?php } ?>
  </table>
</body>

</html>