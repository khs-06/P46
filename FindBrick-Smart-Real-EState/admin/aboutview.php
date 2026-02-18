<?php
session_start();
require("config.php");
////code

if (!isset($_SESSION['auser'])) {
	header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>About View - FB Admin Panel</title>
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
									<h3 class="page-title">View About</h3>
									<ul class="breadcrumb">
										<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
										<li class="breadcrumb-item active">View About</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="row">
							<div class="col-sm-12">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">List Of About</h4>
										<?php
										if (isset($_GET['msg']))
											echo $_GET['msg'];

										?>
									</div>
									<div class="card-body">

										<div class="table-responsive">
											<table class="table table-stripped">
												<thead>
													<tr>
														<th>Id</th>
														<th>Title</th>
														<th>Description</th>
														<th>Image</th>
														<th>Edit</th>
														<th>Delete</th>

													</tr>
												</thead>
												<?php

												$query = mysqli_query($con, "select * from about");
												$cnt = 1;
												while ($row = mysqli_fetch_row($query)) {
												?>
													<tbody>
														<tr>
															<td><?php echo $cnt; ?></td>
															<td><?php echo $row['1']; ?></td>
															<td><?php echo $row['2']; ?></td>
															<td><img src="uploads/about/<?php echo $row['3']; ?>" height="80px" width="80px"></td>
															<td><a href="aboutedit.php?id=<?php echo $row['0']; ?>">Edit</a></td>
															<td><a href="aboutdelete.php?id=<?php echo $row['0']; ?>">Delete</a></td>
														</tr>
													</tbody>
												<?php
													$cnt = $cnt + 1;
												}
												?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

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

				<!-- Custom JS -->
				<script src="assets/js/script.js"></script>

</body>

</html>