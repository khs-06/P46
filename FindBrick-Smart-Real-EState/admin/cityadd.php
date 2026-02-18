<?php
session_start();
include("config.php");
if (!isset($_SESSION['auser'])) {
  header("location:dashboard.php");
}
///code
$error = "";
$msg = "";
if (isset($_POST['insert'])) {
  $state = $_POST['state'];
  $city = $_POST['city'];

  if (!empty($state) && !empty($city)) {
    $sql = "insert into city (cname,sid) values('$city','$state')";
    $result = mysqli_query($con, $sql);
    if ($result) {
      $msg = "<p class='alert alert-success'>City Inserted Successfully</p>";
    } else {
      $error = "<p class='alert alert-warning'>* City Not Inserted</p>";
    }
  } else {
    $error = "<p class='alert alert-warning'>* Fill all the Fields</p>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>City Management - FB Admin Panel</title>
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
        <!-- Page Wrapper -->
        <div class="page-wrapper">
          <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
              <div class="row">
                <div class="col">
                  <h3 class="page-title">State</h3>
                  <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">State</li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- /Page Header -->

            <!-- city add section -->
            <!-- City Add Section -->
            <div class="row">
              <div class="col-md-12">
                <div class="card shadow-sm border-0">
                  <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Add City</h5>
                  </div>
                  <form method="post" enctype="multipart/form-data">
                    <div class="card-body">
                      <?php echo $error; ?>
                      <?php echo $msg; ?>
                      <?php if (isset($_GET['msg'])) echo $_GET['msg']; ?>

                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">State Name</label>
                          <select class="form-select" name="state" required>
                            <option value="">-- Select State --</option>
                            <?php
                            $query1 = mysqli_query($con, "SELECT * FROM state");
                            while ($row1 = mysqli_fetch_row($query1)) {
                            ?>
                              <option value="<?php echo $row1[0]; ?>"><?php echo $row1[1]; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">City Name</label>
                          <input type="text" class="form-control" name="city" placeholder="Enter city name" required>
                        </div>
                      </div>

                      <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4" name="insert">
                          <i class="bi bi-check-circle"></i> Submit
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- View City Section -->
            <div class="row mt-4">
              <div class="col-sm-12">
                <div class="card shadow-sm border-0">
                  <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> City List</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table id="basic-datatable" class="table table-hover table-striped align-middle">
                        <thead class="table-primary">
                          <tr>
                            <th>ID</th>
                            <th>City Name</th>
                            <th>State ID</th>
                            <th>State Name</th>
                            <th class="text-center">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $query = mysqli_query($con, "SELECT city.*, state.sname FROM city, state WHERE city.sid=state.sid");
                          while ($row = mysqli_fetch_array($query)) {
                          ?>
                            <tr>
                              <td><?php echo $row[0]; ?></td>
                              <td class="fw-semibold"><?php echo $row[1]; ?></td>
                              <td><?php echo $row[2]; ?></td>
                              <td><?php echo $row['sname']; ?></td>
                              <td class="text-center">
                                <a href="cityedit.php?id=<?php echo $row[0]; ?>" class="btn btn-sm btn-outline-primary me-2">
                                  <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="citydelete.php?id=<?php echo $row[0]; ?>" class="btn btn-sm btn-outline-danger"
                                  onclick="return confirm('Are you sure you want to delete this city?');">
                                  <i class="bi bi-trash"></i>
                                </a>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
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