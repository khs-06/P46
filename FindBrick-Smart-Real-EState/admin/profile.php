<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
	header("location:dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Profile - FB Admin Panel</title>
	<link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
			<main class="col-md-10 col-lg-10 px-md-4">



				<div class="page-wrapper">
					<div class="content container-fluid">
						<div class="page-header">
							<div class="row">
								<div class="col">
									<h3 class="page-title">Profile</h3>
									<ul class="breadcrumb">
										<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
										<li class="breadcrumb-item active">Profile</li>
									</ul>
								</div>
							</div>
						</div>

						<div class="row">
							<?php
							$id = $_SESSION['auser'];
							$sql = "SELECT * FROM admin WHERE aemail='$id'";
							$result = mysqli_query($con, $sql);
							while ($row = mysqli_fetch_array($result)) {
							?>
								<div class="col-md-12">
									<div class="profile-header">
										<div class="row align-items-center">
											<div class="col-auto profile-image">
												<a href="#">
													<img class="rounded-circle" alt="User Image" src="assets/img/profiles/avatar-01.png">
												</a>
											</div>
											<div class="col ml-md-n2 profile-user-info">
												<h4 class="user-name mb-2 text-uppercase"><?php echo $row['1']; ?></h4>
												<h6 class="text-muted"><?php echo $row['2']; ?></h6>
												<div class="user-Location"><i class="fa fa-id-badge" aria-hidden="true"></i> ID: <?php echo $row['0']; ?></div>
												<div class="about-text"></div>
											</div>
										</div>
									</div>

									<div class="profile-menu">
										<ul class="nav nav-tabs nav-tabs-solid">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" href="#per_details_tab">About</a>
											</li>
										</ul>
									</div>

									<div class="tab-content profile-tab-cont">
										<div class="tab-pane fade show active" id="per_details_tab">
											<div class="row">
												<div class="col-lg-9">
													<div class="card">
														<div class="card-body">
															<div class="row">
																<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Name</p>
																<p class="col-sm-9"><?php echo $row['1']; ?></p>
															</div>
															<div class="row">
																<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Date of Birth</p>
																<p class="col-sm-9">N/A</p>
															</div>
															<div class="row">
																<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Email ID</p>
																<p class="col-sm-9"><a href="#"><?php echo $row['2']; ?></a></p>
															</div>
															<div class="row">
																<p class="col-sm-3 text-muted text-sm-right mb-0 mb-sm-3">Mobile</p>
																<p class="col-sm-9">N/A</p>
															</div>
															<div class="row">
																<p class="col-sm-3 text-muted text-sm-right mb-0">Address</p>
																<p class="col-sm-9 mb-0">101 aadi avdi poll ma ,<br> eno,<br> Gujarat - 33165,<br> India.</p>
															</div>
														</div>
													</div>
												</div>

												<div class="col-lg-3">
													<div class="card">
														<div class="card-body">
															<h5 class="card-title d-flex justify-content-between">
																<span>Account Status</span>
															</h5>
															<button class="btn btn-success" type="button"><i class="fe fe-check-verified"></i> Active</button>
														</div>
													</div>

													<div class="card">
														<div class="card-body">
															<h5 class="card-title d-flex justify-content-between">
																<span>Skills </span>
															</h5>
															<div class="skill-tags">
																<span>Html5</span>
																<span>CSS3</span>
																<span>Bootstrap</span>
																<span>Javascript</span>
																<span>Jquery</span>
																<span>PHP</span>
																<span>Mysql</span>
																<span>ASP</span>
															</div>
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<script src="assets/js/jquery-3.2.1.min.js"></script>
				<script src="assets/js/popper.min.js"></script>
				<script src="assets/js/bootstrap.min.js"></script>
				<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
				<script src="assets/js/script.js"></script>

</body>

</html>