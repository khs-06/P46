<?php
session_start();
include("config.php");

if (!isset($_SESSION['auser'])) {
  header("location:dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Agent Management - FB Admin Panel</title>
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


        <div class="container-fluid px-4 py-4">
          <!-- Page Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Manage Agents</h2>

          </div>

          <!-- Agents Table -->
          <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm rounded-3 overflow-hidden">
              <thead class="table-primary">
                <tr>
                  <th>#</th>
                  <th>Name & Photo</th>
                  <th>Email</th>
                  <th>User Type</th>
                  <th>Phone</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = mysqli_query($con, "SELECT * FROM user WHERE utype='agent'");
                $cnt = 1;
                while ($row = mysqli_fetch_row($query)) {
                ?>
                  <tr>
                    <td><?php echo $cnt; ?></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="user/<?php echo htmlspecialchars($row[6]); ?>"
                          class="rounded-circle me-2" width="40" height="40"
                          onerror="this.src='assets/img/log.png'">
                        <span class="fw-semibold"><?php echo $row[1]; ?></span>
                      </div>
                    </td>
                    <td><?php echo $row[2]; ?></td>
                    <td><?php echo $row[3]; ?></td>
                    <td>
                      <span class="badge bg-info px-3 py-2">
                        <?php echo ucfirst($row[4]); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <a href="useragentdelete.php?id=<?php echo $row[0]; ?>"
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Are you sure you want to delete this agent? if you can delete agent then their properties will be deleted');">
                        <i class="bi bi-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php $cnt++;
                } ?>
              </tbody>
            </table>
          </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>