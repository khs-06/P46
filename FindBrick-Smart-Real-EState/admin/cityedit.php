<?php
session_start();
include("config.php");
if (!isset($_SESSION['auser'])) {
  header("location:index.php");
}
///code
$error = "";
$msg = "";
if (isset($_POST['insert'])) {
  $cid = $_GET['id'];

  $ustate = $_POST['ustate'];
  $ucity = $_POST['ucity'];

  if (!empty($ustate) && !empty($ucity)) {
    $sql = "UPDATE city SET cname = '{$ucity}' ,sid = '{$ustate}' WHERE cid = {$cid}";
    $result = mysqli_query($con, $sql);
    if ($result) {
      $msg = "<p class='alert alert-success'>City Updated</p>";
      header("Location:cityadd.php?msg=$msg");
    } else {
      $msg = "<p class='alert alert-warning'>City Not Updated</p>";
      header("Location:cityadd.php?msg=$msg");
    }
  } else {
    $error = "<p class='alert alert-warning'>* Please Fill all the Fields</p>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>City Edit - FB Admin Panel</title>
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

        <div class="page-wrapper">
          <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
              <div class="row">
                <div class="col">
                  <h3 class="page-title">Edit City</h3>
                  <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="cityadd.php">City</a></li>
                    <li class="breadcrumb-item active">Edit City</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- City Edit Form -->
            <div class="row">
              <div class="col-md-12">
                <div class="card card-custom">
                  <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Update City</h5>
                  </div>
                  <div class="card-body">
                    <?php echo $error; ?>
                    <?php echo $msg; ?>
                    <?php if (isset($_GET['msg'])) echo $_GET['msg']; ?>

                    <?php
                    $cid = $_GET['id'];
                    $sql = "SELECT * FROM city WHERE cid = {$cid}";
                    $result = mysqli_query($con, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                      <form method="post">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">State Name</label>
                            <select class="form-select" name="ustate" required>
                              <option value="">-- Select State --</option>
                              <?php
                              $query1 = mysqli_query($con, "SELECT * FROM state");
                              while ($row1 = mysqli_fetch_assoc($query1)) {
                                $selected = ($row1['sid'] == $row['2']) ? "selected" : "";
                                echo "<option value='{$row1['sid']}' $selected>{$row1['sname']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">City Name</label>
                            <input type="text" class="form-control" name="ucity"
                              value="<?php echo $row['cname']; ?>" required>
                          </div>
                        </div>

                        <div class="text-end mt-4">
                          <button type="submit" class="btn btn-primary btn-custom" name="insert">
                            <i class="bi bi-check-circle"></i> Update
                          </button>
                          <a href="cityadd.php" class="btn btn-outline-secondary btn-custom">
                            <i class="bi bi-arrow-left"></i> Cancel
                          </a>
                        </div>
                      </form>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- /City Edit Form -->

          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Bootstrap & JS -->
  <script src="assets/js/jquery-3.2.1.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
</body>

</html>