<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
    header("location:index.php");
}

// Add state
$error = "";
$msg = "";

if (isset($_POST['insert'])) {
    $state = $_POST['state'];
    if (!empty($state)) {
        $sql = "INSERT INTO state (sname) VALUES ('$state')";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $msg = "State Added Successfully";
        } else {
            $error = "Something went wrong. Try again.";
        }
    } else {
        $error = "Please enter state name";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>State Add - FB Admin Panel</title>
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
                <!-- Page Content -->
                <div class="content">
                    <h3 class="mb-4">Manage States</h3>

                    <!-- Add State Form -->
                    <div class="card mb-4">
                        <div class="card-header">Add State</div>
                        <div class="card-body">
                            <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                            <?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
                            <form method="post">
                                <div class="form-group">
                                    <label for="state">State Name</label>
                                    <input type="text" name="state" class="form-control" id="state" placeholder="Enter state name">
                                </div>
                                <button type="submit" name="insert" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>

                    <!-- State List -->
                    <div class="card">
                        <div class="card-header">State List</div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>State Name</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($con, "SELECT * FROM state ORDER BY sid DESC");
                                    while ($row = mysqli_fetch_array($query)) {
                                        echo "<tr>
                        <td>" . $row['sid'] . "</td>
                        <td>" . $row['sname'] . "</td>
                        <td><a href='stateedit.php?id=" . $row['sid'] . "' class='btn btn-warning btn-sm'>Edit</a></td>
                        <td><a href='statedelete.php?id=" . $row['sid'] . "' class='btn btn-danger btn-sm'>Delete</a></td>
                    </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

</body>

</html>