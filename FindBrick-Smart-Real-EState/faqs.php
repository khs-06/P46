<?php
include("session-check.php");
// ini_set('session.cache_limiter', 'public');
// session_cache_limiter(false);
// session_start();
// include("config.php");
// if (!isset($_SESSION['uemail'])) {
//     header("location:login.php");
//     exit;
// }

$error = '';
$msg = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Profile - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">

</head>

<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br><br>
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <img src="images/fb-logo.png" alt="FAQ" style="max-width:120px" class="mb-3">
                        <h2 class="display-4 font-weight-bold elegant-font mb-2">Frequently Asked Questions</h2>
                        <p class="lead text-muted">Find answers to common questions about buying, selling, and renting properties on FindBrick.</p>
                    </div>
                    <div class="accordion shadow-lg rounded" id="faqAccordion">
                        <div class="card border-0 mb-3">
                            <div class="card-header bg-white" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed font-weight-bold text-dark" type="button" data-toggle="collapse" data-target="#collapseOne">
                                        How do I search for properties on FindBrick?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Use the search bar on our homepage to find properties by location, type, price, features, and more. Filter results to match your preferences.
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 mb-3">
                            <div class="card-header bg-white" id="headingTwo">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed font-weight-bold text-dark" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                        Can I contact property owners or agents directly?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Yes! Every listing page has a contact form. Submit your query, and the owner or agent will get back to you promptly.
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 mb-3">
                            <div class="card-header bg-white" id="headingThree">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed font-weight-bold text-dark" type="button" data-toggle="collapse" data-target="#collapseThree">
                                        Is it free to list my property on FindBrick?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Yes, listing is absolutely free! You can upgrade to premium features for enhanced visibility and exclusive tools.
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 mb-3">
                            <div class="card-header bg-white" id="headingFour">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed font-weight-bold text-dark" type="button" data-toggle="collapse" data-target="#collapseFour">
                                        How do I get support if I face any issues?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseFour" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Visit our <a href="support.php" class="text-primary">Support page</a> or email us at <b>support@findbrick.com</b>. We respond within 24 hours.
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 mb-3">
                            <div class="card-header bg-white" id="headingFive">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed font-weight-bold text-dark" type="button" data-toggle="collapse" data-target="#collapseFive">
                                        Are my details and data safe on FindBrick?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseFive" class="collapse" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Absolutely! Your privacy is important. Read our <a href="privacy.php" class="text-primary">Privacy Policy</a> for details on how we protect your data.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-5">
                        <a href="support.php" class="btn btn-search px-4 py-2 shadow">Need more help? Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
        <?php include("include/footer.php"); ?>
        <!-- <a href="#" class="bg-secondary text-white hover-text-secondary" id="scroll"><i class="fas fa-angle-up"></i></a> -->
    </div>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
    <!-- <script>
        if (typeof window.history.pushState == 'function') {
            window.history.pushState({}, "Hide", '<?php echo $_SERVER['PHP_SELF']; ?>');
        }
    </script> -->
</body>

</html>