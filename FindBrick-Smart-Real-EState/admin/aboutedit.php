<?php
session_start();
include("config.php");
if (!isset($_SESSION['auser'])) {
	header("location:index.php");
}
if (isset($_POST['update'])) {
	// $sql = "select * from about where id='{$_GET['id']}'";
	// $result = mysqli_query($con, $sql);
	// while($row = mysqli_fetch_array($result))
	// {
	//   $aimage=$row["image"];
	// }
	$aid = $_GET['id'];
	$title = $_POST['title'];
	$description = $_POST['description'];

	$image = $_FILES['image']['name'];
	$temp_name1 = $_FILES['image']['tmp_name'];


	move_uploaded_file($temp_name1, "uploads/about/$image");

	$sql = "UPDATE about SET title = '{$title}' , description = '{$description}', image ='{$image}' WHERE id = {$aid}";
	$result = mysqli_query($con, $sql);
	if ($result == true) {
		$msg = "<p class='alert alert-success'>About Updated</p>";
		header("Location:aboutview.php?msg=$msg");
	} else {
		$msg = "<p class='alert alert-warning'>About Not Updated</p>";
		header("Location:aboutview.php?msg=$msg");
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>About Edit - FB Admin Panel</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

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

		body {
			background: #f9f9f9;
			color: #111;
			transition: all 0.3s ease;
		}

		.dark-mode {
			background: #121212;
			color: #f5f5f5;
		}

		.dark-mode .sidebar {
			background: #1b1b1b;
		}

		.dark-mode a {
			color: #f5f5f5;
		}

		.dark-mode .topbar {
			background: #1e1e1e;
			color: #ddd;
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


				<!-- Page Wrapper -->
				<div class="page-wrapper">

					<div class="content container-fluid">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row">
								<div class="col">
									<h3 class="page-title">About</h3>
									<ul class="breadcrumb">
										<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
										<li class="breadcrumb-item active">About</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-header">
										<h2 class="card-title">About Us</h2>
									</div>
									<?php
									$aid = $_GET['id'];
									$sql = "SELECT * FROM about where id = {$aid}";
									$result = mysqli_query($con, $sql);
									while ($row = mysqli_fetch_row($result)) {
									?>
										<form method="post" enctype="multipart/form-data">
											<div class="card-body">
												<div class="row">
													<div class="col-xl-12">
														<h5 class="card-title">About Us </h5>
														<div class="form-group row">
															<label class="col-lg-2 col-form-label">Title</label>
															<div class="col-lg-9">
																<input type="text" class="form-control" name="title" value="<?php echo $row['1']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-2 col-form-label">Content</label>
															<div class="col-lg-9">
																<textarea class="tinymce form-control" name="description" rows="10" cols="30"><?php echo $row['2']; ?></textarea>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-2 col-form-label">Image</label>
															<div class="col-lg-9">
																<input class="form-control" name="image" type="file">
																<img src="uploads/about/<?php echo $row['3']; ?>" height="200px" width="200px"><br><br>
															</div>
														</div>
													</div>
												</div>
												<div class="text-left">
													<input type="submit" class="btn btn-primary" value="Submit" name="update" style="margin-left:200px;">
												</div>
										</form>
								</div>
							<?php } ?>
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

<!-- Mirrored from dreamguys.co.in/demo/ventura/form-vertical.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 25 Aug 2019 04:41:05 GMT -->

</html>