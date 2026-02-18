<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
	header("location:index.php");
}

//// add code

$msg = "";
if (isset($_POST['update'])) {
	$fid = $_GET['id'];
	$status = $_POST['status'];

	$sql = "UPDATE feedback SET status = '{$status}' WHERE id = {$fid}";
	$result = mysqli_query($con, $sql);
	if ($result == true) {
		$msg = "<p class='alert alert-success'>Feedback Updated Successfully</p>";
		header("Location:feedbackview.php?msg=$msg");
	} else {
		$msg = "<p class='alert alert-warning'>Feedback Not Updated</p>";
		header("Location:feedbackview.php?msg=$msg");
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Feedback Edit - FB Admin Panel</title>
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
		}
	</style>
</head>

<body>
	<div class="container-fluid">

		<!-- Sidebar -->
		<?php include("sidebar.php"); ?>

		<!-- Main Content -->
		<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">

			<!-- Page Wrapper -->
			<div class="page-wrapper">

				<div class="content container-fluid">

					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col">
								<h3 class="page-title">Feedback</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
									<li class="breadcrumb-item active">Feedback</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->

					<div class="row">
						<div class="col-md-12">
							<div class="card">
								<div class="card-header">
									<h2 class="card-title">Update Feedback</h2>
								</div>
								<?php
								$fid = $_GET['id'];
								$sql = "SELECT * FROM feedback where fid = {$fid}";
								$result = mysqli_query($con, $sql);
								while ($row = mysqli_fetch_row($result)) {
								?>
									<form method="post">
										<div class="card-body">
											<div class="row">
												<div class="col-xl-12">
													<h5 class="card-title">Update Feedback</h5>

													<?php echo $msg; ?>
													<div class="form-group row">
														<label class="col-lg-2 col-form-label">Feedback Id</label>
														<div class="col-lg-9">
															<input type="text" class="form-control" name="fid" value="<?php echo $row['0']; ?>" disabled>
														</div>
													</div>
													<div class="form-group row">
														<label class="col-lg-2 col-form-label">Status</label>
														<div class="col-lg-9">
															<input type="text" class="form-control" name="status" required="" value="<?php echo $row['3']; ?>">
														</div>
													</div>

												</div>
											</div>
											<div class="text-left">
												<input type="submit" class="btn btn-primary" value="Submit" name="update" style="margin-left:200px;">
											</div>
									</form>
								<?php } ?>
							</div>

						</div>
					</div>
				</div>


			</div>
	</div>
	<!-- /Page Wrapper -->
	<!-- /Main Wrapper -->
	<script src="assets/plugins/tinymce/tinymce.min.js"></script>
	<script src="assets/plugins/tinymce/init-tinymce.min.js"></script>
	<!-- jQuery -->
	<script src="assets/js/jquery-3.2.1.min.js"></script>

	<!-- Bootstrap Core JS -->
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>

	<!-- Slimscroll JS -->
	<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<!-- Select2 JS -->
	<script src="assets/js/select2.min.js"></script>

	<!-- Custom JS -->
	<script src="assets/js/script.js"></script>
</body>

</html>