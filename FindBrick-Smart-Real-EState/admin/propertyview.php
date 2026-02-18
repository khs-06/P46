<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
  header("location:dashboard.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Property View - FB Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f7fa;
    }

    .sidebar {
      height: 100vh;
      background: #fff;
      border-right: 1px solid #ddd;
      padding-top: 1rem;
    }

    .sidebar h4 {
      font-weight: bold;
      color: #0d6efd;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      margin: 6px 10px;
      color: #333;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 500;
      transition: 0.3s;
    }

    .sidebar a i {
      margin-right: 10px;
      font-size: 18px;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #0d6efd;
      color: #fff;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
    }

    .topbar {
      background: #fff;
      padding: 12px 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar input {
      border-radius: 20px;
      padding: 5px 15px;
      border: 1px solid #ccc;
    }

    .card-custom {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .table img {
      border-radius: 8px;
      object-fit: cover;
      margin: 2px;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">

      <!-- Sidebar -->
      <?php include("sidebar.php"); ?>

      <!-- Main content -->
      <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">

        <!-- Topbar -->
        <div class="topbar mt-2 mb-4 rounded">
          <h3 class="mb-0">Property View</h3>
          <div class="d-flex align-items-center gap-3">
            <!-- <input type="text" placeholder="Search..."> -->
            <i class="bi bi-bell fs-4 text-secondary"></i>
            <a href="profile.php">
              <img src="assets/img/profiles/avatar-01.png" class="rounded-circle" width="40" height="40" alt="Profile">
            </a>
          </div>
        </div>
        <!-- <div class="d-flex justify-content-end mb-3 gap-5">
          <a href="propertyadd.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Property
          </a>
        </div> -->

        <!-- Property Table -->
        <div class="card card-custom p-3 mb-5">
          <h5>All Properties</h5>
          <div class="mt-3">
            <table class="table table-hover table-bordered align-middle" id="basic-datatable">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Price</th>
                  <th>City</th>
                  <th>Status</th>
                  <th>Images</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = mysqli_query($con, "SELECT * FROM property ORDER BY pid ASC");
                if (mysqli_num_rows($query) > 0) {
                  while ($row = mysqli_fetch_assoc($query)) {
                    $pid     = $row['pid'];
                    $title   = $row['title'];
                    $type    = $row['ptype'];
                    $price   = $row['price'];
                    $price_type = $row['price_type'];
                    $city    = $row['city'];
                    $status  = $row['status'];

                    // Collect all available images
                    $images = [];
                    if (!empty($row['pimage']))  $images[] = "" . $row['pimage'];
                    if (!empty($row['pimage1'])) $images[] = "" . $row['pimage1'];
                    if (!empty($row['pimage2'])) $images[] = "" . $row['pimage2'];
                    if (!empty($row['pimage3'])) $images[] = "" . $row['pimage3'];
                    if (!empty($row['pimage4'])) $images[] = "" . $row['pimage4'];
                ?>
                    <tr>
                      <td><?= htmlspecialchars($pid) ?></td>
                      <td><?= htmlspecialchars($title) ?></td>
                      <td><?= htmlspecialchars($type) ?></td>
                      <td><?= htmlspecialchars($price) ?> <?= htmlspecialchars($price_type) ?></td>
                      <td><?= htmlspecialchars($city) ?></td>
                      <td>
                        <span class="badge <?= $status === 'available' ? 'bg-success' : 'bg-danger' ?>">
                          <?= htmlspecialchars($status) ?>
                        </span>
                      </td>
                      <td>
                        <?php
                        if (!empty($images)) {
                          foreach ($images as $img) {
                            echo "<img src='" . htmlspecialchars($img) . "' width='60' height='60'>";
                          }
                        } else {
                          echo "<img src='https://via.placeholder.com/60x60?text=No+Img'>";
                        }
                        ?>
                      </td>
                      <td>
                        <!--  -->
                        <a href="propertydelete.php?pid=<?= urlencode($pid) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?');">
                          <i class="bi bi-trash"></i>
                        </a>
                        <a href="propertyedit.php?pid=<?= urlencode($pid) ?>" class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-pencil-square"></i>
                      </td>
                    </tr>
                  <?php
                  }
                } else {
                  ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted">No Properties Found</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>
  <!-- jQuery -->
  <script src="assets/js/jquery-3.2.1.min.js"></script>

  <!-- Bootstrap Core JS -->
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  <!-- Slimscroll JS -->
  <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

  <!-- Datatables JS -->
  <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
  <script src="assets/plugins/datatables/responsive.bootstrap4.min.js"></script>

  <script src="assets/plugins/datatables/dataTables.select.min.js"></script>

  <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
  <script src="assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
  <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
  <script src="assets/plugins/datatables/buttons.flash.min.js"></script>
  <script src="assets/plugins/datatables/buttons.print.min.js"></script>

  <!-- Custom JS -->
  <script src="assets/js/script.js"></script>
</body>

</html>