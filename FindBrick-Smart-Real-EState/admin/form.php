<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Basic Input Form - FB Admin Panel</title>
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
			<main class="col-md-5 ms-sm-auto col-lg-10 px-md-2">
				<!-- Page Wrapper -->
				<div class="page-wrapper">
					<div class="content container-fluid">

						<!-- Page Header -->
						<div class="page-header">
							<div class="row">
								<div class="col">
									<h3 class="page-title">Basic Inputs</h3>
									<ul class="breadcrumb">
										<li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
										<li class="breadcrumb-item active">Basic Inputs</li>
									</ul>
								</div>
							</div>
						</div>
						<!-- /Page Header -->

						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Basic Inputs</h4>
									</div>
									<div class="card-body">
										<form action="#">
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Text Input</label>
												<div class="col-md-10">
													<input type="text" class="form-control">
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Password</label>
												<div class="col-md-10">
													<input type="password" class="form-control">
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Disabled Input</label>
												<div class="col-md-10">
													<input type="text" class="form-control" disabled="disabled">
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Readonly Input</label>
												<div class="col-md-10">
													<input type="text" class="form-control" value="readonly" readonly="readonly">
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Placeholder</label>
												<div class="col-md-10">
													<input type="text" class="form-control" placeholder="Placeholder">
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">File Input</label>
												<div class="col-md-10">
													<input class="form-control" type="file">
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Default Select</label>
												<div class="col-md-10">
													<select class="form-control">
														<option>-- Select --</option>
														<option>Option 1</option>
														<option>Option 2</option>
														<option>Option 3</option>
														<option>Option 4</option>
														<option>Option 5</option>
													</select>
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Radio</label>
												<div class="col-md-10">
													<div class="form-check">
														<input type="radio" name="radio" class="form-check-input" id="radio1">
														<label for="radio1" class="form-check-label">Option 1</label>
													</div>
													<div class="form-check">
														<input type="radio" name="radio" class="form-check-input" id="radio2">
														<label for="radio2" class="form-check-label">Option 2</label>
													</div>
													<div class="form-check">
														<input type="radio" name="radio" class="form-check-input" id="radio3">
														<label for="radio3" class="form-check-label">Option 3</label>
													</div>
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Checkbox</label>
												<div class="col-md-10">
													<div class="form-check">
														<input type="checkbox" name="checkbox" class="form-check-input" id="check1">
														<label for="check1" class="form-check-label">Option 1</label>
													</div>
													<div class="form-check">
														<input type="checkbox" name="checkbox" class="form-check-input" id="check2">
														<label for="check2" class="form-check-label">Option 2</label>
													</div>
													<div class="form-check">
														<input type="checkbox" name="checkbox" class="form-check-input" id="check3">
														<label for="check3" class="form-check-label">Option 3</label>
													</div>
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Textarea</label>
												<div class="col-md-10">
													<textarea rows="5" cols="5" class="form-control" placeholder="Enter text here"></textarea>
												</div>
											</div>
											<div class="form-group row mb-3">
												<label class="col-form-label col-md-2">Input Addons</label>
												<div class="col-md-10">
													<div class="input-group">
														<span class="input-group-text">$</span>
														<input class="form-control" type="text">
														<button class="btn btn-primary" type="button">Button</button>
													</div>
												</div>
											</div>
										</form>
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