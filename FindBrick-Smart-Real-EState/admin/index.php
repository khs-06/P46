<?php 
session_start();
include("config.php");

$error = "";

if(isset($_POST['login']))
{
    $email = $_REQUEST['user'];  // login input is now email
    $pass  = $_REQUEST['pass'];
    
    if(!empty($email) && !empty($pass))
    {
        // Query using only email
        $query = "SELECT aemail, apass FROM admin WHERE aemail='$email' AND apass='$pass'";
        $result = mysqli_query($con, $query) or die(mysqli_error($con));

        $num_row = mysqli_num_rows($result);
        $row = mysqli_fetch_array($result);

        if($num_row == 1)
        {
            $_SESSION['auser'] = $row['aemail']; // store email in session
            header("Location: dashboard.php");
            exit();
        }
        else
        {
            $error = '* Invalid Email or Password';
        }
    }
    else
    {
        $error = "* Please Fill all the Fields!";
    }
}   
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Login - FB Admin Panel</title>

    <!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <!-- Main Wrapper -->
    <div class="page-wrappers login-body">
        <div class="login-wrapper">
            <div class="container">
                <div class="loginbox">
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Login</h1>
                            <p class="account-subtitle">Access to our dashboard</p>
                            <p style="color:red;"><?php echo $error; ?></p>
                            <!-- Form -->
                            <form method="post">
                                <div class="form-group">
                                    <input class="form-control" name="user" type="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="password" name="pass" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block" name="login" type="submit">Login</button>
                                </div>
                            </form>
                            
                            <!-- <div class="login-or">
                                <span class="or-line"></span>
                                <span class="span-or">or</span>
                            </div> -->
                            
                            <!-- <div class="text-center dont-have">Don't have an account? <a href="register.php">Register</a></div> -->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
