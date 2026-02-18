<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();
ini_set('session.cache_limiter', 'public');
session_cache_limiter(false);
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    include("config.php");
}

ob_end_flush()
?>
<script>
    var isLoggedIn = <?php echo isset($_SESSION['uemail']) ? 'true' : 'false'; ?>;
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FindBrick Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="css/all.min.css">
    <!-- Google Fonts: Poppins & Playfair Display -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        .bg-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: 1;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            /* Optional: dark overlay for text readability */
            z-index: 2;
        }

        .welcome_banner .container-fluid {
            position: relative;
            z-index: 3;
        }
    </style>
</head>

<body>
    <div style="position: relative; z-index: 1; width: 100%;overflow: hidden;" class="main-container ">
        <!-- header start -->
        <?php include("include/header.php"); ?>
        <!-- header end -->
        <div class="welcome_banner position-relative" style="overflow:hidden;">
            <video autoplay loop muted playsinline class="bg-video">
                <source src="images/banner.mp4" type="video/mp4">
                <!-- Fallback image -->
                <img src="images/home.jpg" alt="welcome banner" />
            </video>
            <div class="banner-overlay"></div>
            <div class="container-fluid h-100 position-absolute top-0 start-0">
                <!-- Banner content here -->
                <div class="row h-100">
                    <div class="col-12 d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="welcome-banner-content text-center mb-4 animate__animated animate__fadeInDown">
                            <h1 class="welcome-banner-title font-weight-bold text-uppercase mb-2">
                                FindBrick
                            </h1>
                            <p class="welcome-banner-subtitle h3 mb-0">
                                Your Smart Real Estate Solution
                            </p>
                        </div>
                        <!-- ... search bar, etc ... -->
                        <!-- Search Bar -->
                        <div class="search-container mt-0 mb-5 animate__animated animate__fadeInUp">
                            <div class="row justify-content-center">
                                <div class="col-md-6 position-relative">
                                    <input type="text" class="form-control form-control-lg" id="mainSearchInput" placeholder="Search agent, builder or property..." autocomplete="off">
                                    <ul id="mainSearchDropdown" class="search-dropdown-modern list-group position-absolute" style="z-index:100; display:none; max-height:320px; overflow-y:auto; min-width: 430px;"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Welcome Banner
        <div class="welcome_banner position-relative">
            <section class="welcome-banner-section position-relative">
                <img src="images/home.jpg" alt="welcome banner" class="img-fluid w-100 h-100 banner-bg-img" />
                <div class="banner-overlay"></div>
                <div class="container-fluid h-100 position-absolute top-0 start-0">
                    <div class="row h-100">
                        <div class="col-12 d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="welcome-banner-content text-center mb-4 animate__animated animate__fadeInDown">
                                <h1 class="welcome-banner-title font-weight-bold text-uppercase mb-2">
                                    FindBrick
                                </h1>
                                <p class="welcome-banner-subtitle h3 mb-0">
                                    Your Smart Real Estate Solution
                                </p>
                            </div>


                        </div>
                    </div>
                </div> -->
        <!-- <div class="dark-mode-toggle position-relative top-50px p-3">
                    <input type="checkbox" class="checkbox" id="checkbox">
                    <label for="checkbox" class="checkbox-label">
                        <i class="fas fa-moon"></i>
                        <i class="fas fa-sun"></i>
                        <span class="ball"></span>
                    </label>
                </div> -->
        </section>
    </div>

    <!-- Services -->
    <div class="services-container  text-white p-3">
        <section class="container services-section my-5">
            <h2 class="section-title text-center mb-4 animate__animated animate__fadeInUp">What we Do</h2>
            <div class="row text-center">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="service-card hover-zoomer shadow animate__animated animate__zoomIn">
                        <!-- <a href="buy.php" class="text-decoration-none text-white"> -->
                        <h3>Buy <br>Property</h3>
                        <hr>
                        <p>Find your dream home with our expert agents.</p>
                        <!-- </a> -->
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="service-card hover-zoomer shadow animate__animated animate__zoomIn" style="animation-delay: 0.1s;">
                        <!-- <a href="sell.php" class="text-decoration-none text-white"> -->
                        <h3>Sell <br>Property</h3>
                        <hr>
                        <p>Get the best value for your property with our expert guidance.</p>
                        <!-- </a> -->
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="service-card hover-zoomer shadow animate__animated animate__zoomIn" style="animation-delay: 0.2s;">
                        <!-- <a href="rent.php" class="text-decoration-none text-white"> -->
                        <h3>Rent <br>Property</h3>
                        <hr>
                        <p>Find the perfect rental property with our extensive listings.</p>
                        <!-- </a> -->
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="service-card hover-zoomer shadow animate__animated animate__zoomIn" style="animation-delay: 0.3s;">
                        <!-- <a href="invest.php" class="text-decoration-none text-white"> -->
                        <h3>Invest <br>Property</h3>
                        <hr>
                        <p>Maximize your returns with our expert investment advice.</p>
                        <!-- </a> -->
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Gallery and Carousel -->
    <div class="gallery-container text-white  p-5">
        <h2 class="section-title text-center mb-4">Recently Added Properties</h2>
        <!-- Carousel start -->
        <div id="mainCarousel" class="carousel-container carousel slide animate__animated animate__fadeIn" data-ride="carousel">
            <?php
            $stmt1 = $conn->prepare("SELECT title, city, state, pimage FROM property where created_at < NOW(4)");
            $stmt1->execute();
            $result = $stmt1->get_result();
            $properties = $result->fetch_all(MYSQLI_ASSOC);
            ?>
            <div class="carousel-inner">
                <?php foreach ($properties as $index => $property): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="admin/<?php echo $property['pimage']; ?>" class="d-block w-100 zoom-hover-img" alt="<?php echo $property['title']; ?>">
                        <div class="carousel-caption d-none d-md-block">
                            <h5><?php echo $property['title']; ?></h5>
                            <p><?php echo $property['city']; ?>, <?php echo $property['state']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php $stmt1->close(); ?>
            <a class="carousel-control-prev" href="#mainCarousel" role="button" data-slide="prev">
                <span class="fa-solid fa-chevron-left" style="font-size: 35px;" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#mainCarousel" role="button" data-slide="next">
                <span class="fa-solid fa-chevron-right" style="font-size: 35px;" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <!-- Carousel end -->

        <h2 class="section-title text-center mb-4">Popular Properties</h2>
        <section class="container gallery-section my-5">
            <div class="row text-center">
                <?php
                $stmt4 = $conn->prepare("SELECT title, pimage, pid FROM property order by RAND() LIMIT 8");
                $stmt4->execute();
                $result = $stmt4->get_result();
                $gallery = $result->fetch_all();
                foreach ($gallery as $img) {
                    echo '<div class="col-md-3 mb-4"><a href="propertydetail.php?pid=' . $img[2] . '"><div class="gallery-card bg-url hover-zoomer shadow" style="background-image: url(\'admin/' . $img[1] . '\');"><p class="gallery-text">' . $img[0] . '</p></div></a></div>';
                }
                // $stmt1->close();
                ?>
            </div>
        </section>
    </div>

    <!-- About -->
    <div class="about-container  text-white p-3">
        <section class="container about-section my-5">
            <div class="about-card back-shadow p-4 text-center animate__animated animate__fadeInUp">
                <h2 class="elegant-font about-title">Why Choose Us</h2>
                <div class="about-content">
                    <ul class="list-unstyled d-flex flex-column align-items-center">
                        <li>
                            <i class="fas fa-star"></i>
                            <div class="about-item">
                                <h5 class="about-title">Top Rated</h5>
                                <p>Our properties are highly rated by clients.</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-th-large"></i>
                            <div class="about-item">
                                <h5 class="about-title">Wide Range of Properties</h5>
                                <p>We offer a diverse selection of properties to suit every need.</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <div class="about-item">
                                <h5 class="about-title">Trusted by Thousands</h5>
                                <p>Join our community of satisfied clients.</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-headset"></i>
                            <div class="about-item">
                                <h5 class="about-title">24/7 Customer Support</h5>
                                <p>Our team is here to assist you at any time.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <p>Discover the difference with our dedicated team and personalized services.</p>
            </div>
        </section>
    </div>

    <!-- Testimonials -->
    <div class="terminology-container  text-white p-3">
        <section class="container testimonials-section hover-zoomer my-5 p-3">
            <h2 class="section-title text-center mb-4 animate__animated animate__fadeIn">Testimonials</h2>
            <div class="testimonial-card p-4 animate__animated animate__fadeInLeft">
                <span class="quote-text">“Absolutely the best property service I’ve ever used!”</span>
                <br>
                <span class="quote-author">- Some Quotes</span>
            </div>
        </section>
    </div>

    <!-- footer start -->
    <?php include("include/footer.php"); ?>
    <!-- footer end -->
    </div>
    <!-- Bootstrap JS + dependencies  -->
    <!-- <script src="package/Jquery/dist/jquery.slim.min.js"></script> -->
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Animate.css for extra animation -->

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/> -->
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            let searchTimer = null;

            function showDropdown(html) {
                if (html.trim()) {
                    $("#mainSearchDropdown").html(html).show();
                } else {
                    $("#mainSearchDropdown").hide().empty();
                }
            }

            $("#mainSearchInput").on("input", function() {
                let val = $(this).val().trim();
                if (searchTimer) clearTimeout(searchTimer);

                // Check login status
                if (!isLoggedIn) {
                    showDropdown('<li class="list-group-item text-danger">pls login first</li>');
                    return;
                }

                if (!val) {
                    showDropdown("");
                    return;
                }
                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: "ajax/search-handler.php",
                        type: "POST",
                        data: {
                            search: val
                        },
                        dataType: "html",
                        success: function(html) {
                            showDropdown(html);
                        },
                        error: function() {
                            showDropdown('<li class="list-group-item text-danger">Search failed</li>');
                        }
                    });
                }, 400); // 400 ms debounce
            });

            // Hide dropdown when clicking outside
            $(document).on("click", function(e) {
                if (!$(e.target).closest("#mainSearchInput, #mainSearchDropdown").length) {
                    $("#mainSearchDropdown").hide();
                }
            });
        });
    </script>
</body>

</html>