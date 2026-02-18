<?php
// feature-property.php
// session_start();
// include("config.php");
include("session-check.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Featured Properties - FindBrick</title>
    <link rel="shortcut icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">

    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        .property-thumb {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
        }
        .property-card {
            border-radius: 18px;
            box-shadow: 0 6px 24px rgba(44, 62, 80, .08);
            transition: box-shadow .2s, transform .2s;
            background: #fff;
        }
        .property-card:hover {
            box-shadow: 0 12px 32px rgba(44, 62, 80, .18);
            transform: translateY(-6px) scale(1.025);
        }
    </style>
</head>
<body>
    <div id="page-wrapper">
        <?php include("include/header.php"); ?>
        <br><br><br><br>
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height: 250px;">
            <div class="container">
                <div class="row align-items-center py-5">
                    <div class="col-md-6">
                        <h2 class="page-name text-white text-uppercase mb-0"><b>My Properties</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active">My Properties</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="full-row py-5">
            <?php 
            if (isset($_GET['msg'])) {
                echo $_GET['msg'];
            }
            ?>
            <div class="container">
                <div class="row mb-4">
                    <div class="col-lg-8 mx-auto">
                        <form id="feature-search-form" class="search-bar position-relative">
                            <i class="fa fa-search"></i>
                            <input type="text" class="form-control shadow-sm" id="feature-search" placeholder="Search your properties..." autocomplete="off">
                        </form>
                    </div>
                </div>
                <div class="row" id="feature-list">
                    <!-- AJAX loaded featured properties here -->
                </div>
                <div class="row">
                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-center mt-4" id="feature-pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <?php include("include/footer.php"); ?>
    </div>
    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/popper/dist/popper.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            let searchTimeout = null;

            function loadFeatured(page = 1, q = '') {
                $.ajax({
                    url: "ajax/feature-list.php",
                    type: "GET",
                    data: {
                        page: page,
                        search: q
                    },
                    beforeSend: function() {
                        $("#feature-list").html('<div class="col-12 text-center py-5"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                        $("#feature-pagination").empty();
                    },
                    success: function(data) {
                        let result = data;
                        // If not already parsed, parse JSON
                        if (typeof data === "string") {
                            try {
                                result = JSON.parse(data);
                            } catch (e) {
                                $("#feature-list").html('<div class="col-12 text-center text-danger">Invalid response format.</div>');
                                return;
                            }
                        }
                        $("#feature-list").html(result.cards);
                        $("#feature-pagination").html(result.pagination);
                        bindDeleteProperty(); // Re-attach delete handler
                    },
                    error: function(xhr) {
                        $("#feature-list").html('<div class="col-12 text-center text-danger">Failed to load featured properties.</div>');
                    }
                });
            }

            // Delete property handler
            function bindDeleteProperty() {
                $('.delete-property').off('click').on('click', function(e) {
                    e.preventDefault();
                    const pid = $(this).data('pid');
                    if (confirm("Are you sure you want to delete this property?")) {
                        window.location.href = 'propertydelete.php?pid=' + pid;
                    }
                });
            }

            // Initial load
            loadFeatured();

            // Debounced search
            $("#feature-search").on('input', function() {
                let q = $(this).val();
                if (searchTimeout) clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    loadFeatured(1, q);
                }, 600); // 600ms debounce for better UX
            });

            // Pagination link handler (delegated)
            $(document).on('click', '.feature-page-link', function(e) {
                e.preventDefault();
                let page = $(this).data('page');
                let q = $("#feature-search").val();
                loadFeatured(page, q);
            });
        });
    </script>
</body>
</html>