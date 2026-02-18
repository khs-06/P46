<?php
session_start();
require("config.php");
if (!isset($_SESSION['auser'])) {
	header("location:dashboard.php");
}
///code
$error = "";
$msg = "";
if (isset($_POST['insert'])) {
	$sid = $_GET['id'];
	$ustate = $_POST['ustate'];

	$sql = "UPDATE state SET sname = '{$ustate}'  WHERE sid = {$sid}";
	$result = mysqli_query($con, $sql);
	if ($result) {
		$msg = "<p class='alert alert-success'>State Updated</p>";
		header("Location:stateadd.php?msg=$msg");
	} else {
		$msg = "<p class='alert alert-warning'>State Not Updated</p>";
		header("Location:stateadd.php?msg=$msg");
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>State Edit - FB Admin Panel</title>
	<link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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

						<!-- state add section -->
						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-header">
										<h1 class="card-title">Add State</h1>

									</div>
									<?php
									$sid = $_GET['id'];
									$sql = "SELECT * FROM state where sid = {$sid}";
									$result = mysqli_query($con, $sql);
									while ($row = mysqli_fetch_row($result)) {
									?>
										<form method="post">
											<div class="card-body">
												<div class="row">
													<div class="col-xl-6">
														<h5 class="card-title">State Details</h5>
														<div class="form-group row">
															<label class="col-lg-3 col-form-label">State Name</label>
															<div class="col-lg-9">
																<input type="text" class="form-control" name="ustate" value="<?php echo $row['1']; ?>">
															</div>
														</div>
													</div>
												</div>
												<div class="text-left">
													<input type="submit" class="btn btn-primary" value="Submit" name="insert" style="margin-left:200px;">
												</div>
											</div>
										</form>
									<?php } ?>
								</div>
							</div>
						</div>
						<!----End state add section  --->
					</div>
				</div>
				<!-- /Main Wrapper -->

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