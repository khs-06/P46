<?php
include("session-check.php");

// Rewritten property listing with filters + pagination (9 per page)

$filter_error = "";
$properties = [];
$citiesQ = $ptypesQ = $stypesQ = null;

try {
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception("Database connection (\$conn) is not available.");
    }

    // --- FILTER PARSING ---
    $where = [];
    $params = [];        // values to bind
    $param_types = [];   // matching type chars for bind_param

    // Helper to add filter condition and param info
    $addFilter = function ($condition, $value, $typeChar = 's') use (&$where, &$params, &$param_types) {
        $where[] = $condition;
        $params[] = $value;
        $param_types[] = $typeChar;
    };

    // price_min
    if (isset($_GET['price_min']) && $_GET['price_min'] !== '') {
        if (!is_numeric($_GET['price_min'])) throw new Exception("Minimum price must be a number.");
        $addFilter("property.price >= ?", (int)$_GET['price_min'], 'i');
    }
    // price_max
    if (isset($_GET['price_max']) && $_GET['price_max'] !== '') {
        if (!is_numeric($_GET['price_max'])) throw new Exception("Maximum price must be a number.");
        $addFilter("property.price <= ?", (int)$_GET['price_max'], 'i');
    }
    // ptype
    if (isset($_GET['ptype']) && $_GET['ptype'] !== '') {
        $addFilter("property.ptype = ?", $_GET['ptype'], 's');
    }
    // city
    if (isset($_GET['city']) && $_GET['city'] !== '') {
        $addFilter("property.city = ?", $_GET['city'], 's');
    }
    // bed
    if (isset($_GET['bed']) && $_GET['bed'] !== '') {
        if (!is_numeric($_GET['bed'])) throw new Exception("Bedroom must be a number.");
        $addFilter("property.bed = ?", (int)$_GET['bed'], 'i');
    }
    // bath
    if (isset($_GET['bath']) && $_GET['bath'] !== '') {
        if (!is_numeric($_GET['bath'])) throw new Exception("Bathroom must be a number.");
        $addFilter("property.bath = ?", (int)$_GET['bath'], 'i');
    }
    // stype
    if (isset($_GET['stype']) && $_GET['stype'] !== '') {
        $addFilter("property.stype = ?", $_GET['stype'], 's');
    }

    $filter_sql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    // --- PAGINATION SETUP ---
    $perPage = 9;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $perPage;

    // Helper to bind params safely (mysqli requires references)
    function refValues(array $arr) {
        // For PHP 5.3+ call_user_func_array requires references
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    // --- TOTAL COUNT (for pagination) ---
    $count_sql = "SELECT COUNT(*) AS total FROM property INNER JOIN user ON property.uid=user.uid $filter_sql";
    $count_stmt = $conn->prepare($count_sql);
    if ($count_stmt === false) throw new Exception("Count statement preparation failed: " . $conn->error);

    if (count($params)) {
        $types = implode('', $param_types);
        // bind parameters
        $bindArr = array_merge([$types], $params);
        if (!call_user_func_array([$count_stmt, 'bind_param'], refValues($bindArr))) {
            throw new Exception("Failed to bind count parameters: " . $count_stmt->error);
        }
    }

    if (!$count_stmt->execute()) throw new Exception("Count query execution failed: " . $count_stmt->error);
    $countRes = $count_stmt->get_result();
    if ($countRes === false) throw new Exception("Failed to get count result.");
    $totalRow = $countRes->fetch_assoc();
    $totalProperties = isset($totalRow['total']) ? (int)$totalRow['total'] : 0;
    $totalPages = $totalProperties ? (int)ceil($totalProperties / $perPage) : 1;
    if ($page > $totalPages) {
        $page = $totalPages;
        $offset = ($page - 1) * $perPage;
    }
    $count_stmt->close();

    // --- PROPERTY QUERY WITH LIMIT/OFFSET ---
    $query_base = "SELECT property.*, user.uname, user.utype, user.uimage FROM property INNER JOIN user ON property.uid=user.uid $filter_sql ORDER BY property.pid DESC LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query_base);
    if ($stmt === false) throw new Exception("Statement preparation failed: " . $conn->error);

    // Prepare params for this statement (base params + limit + offset)
    $stmt_params = $params; // values
    $stmt_types = $param_types; // types
    // Append limit and offset as integers
    $stmt_params[] = $perPage;
    $stmt_params[] = $offset;
    $stmt_types[] = 'i';
    $stmt_types[] = 'i';

    if (count($stmt_params)) {
        $types = implode('', $stmt_types);
        $bindArr = array_merge([$types], $stmt_params);
        if (!call_user_func_array([$stmt, 'bind_param'], refValues($bindArr))) {
            throw new Exception("Failed to bind parameters: " . $stmt->error);
        }
    }

    if (!$stmt->execute()) throw new Exception("Query execution failed: " . $stmt->error);
    $result = $stmt->get_result();
    if ($result === false) throw new Exception("Could not fetch property results.");
    $properties = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // --- CITY/PTYPE/STYPE LIST FOR FILTERS ---
    $citiesQ = $conn->query("SELECT DISTINCT city FROM property ORDER BY city ASC");
    if ($citiesQ === false) throw new Exception("City list fetch failed: " . $conn->error);
    $ptypesQ = $conn->query("SELECT DISTINCT ptype FROM property ORDER BY ptype ASC");
    if ($ptypesQ === false) throw new Exception("Type list fetch failed: " . $conn->error);
    $stypesQ = $conn->query("SELECT DISTINCT stype FROM property ORDER BY stype ASC");
    if ($stypesQ === false) throw new Exception("Sale/Rent list fetch failed: " . $conn->error);

} catch (Exception $e) {
    $filter_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Property Grid - FindBrick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="package/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/fb-logo.png">
    <style>
        .filter-box { background:#fff; border-radius:8px; box-shadow:0 4px 24px rgba(0,0,0,.06); padding:24px 18px 10px 18px; margin-bottom:32px; }
        .filter-box label { font-weight:600; color:#084298; }
        .filter-box select, .filter-box input { border-radius:5px; border:1px solid #ced4da; }
        .property-card { background:#fff; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.09); transition:box-shadow .2s; margin-bottom:32px; }
        .property-card:hover { box-shadow:0 8px 32px rgba(0,0,0,.09); transform:translateY(-4px) scale(1.01); transition:transform .3s, box-shadow .3s; }
        .property-thumb img { border-radius:10px 10px 0 0; width:100%; min-height:200px !important; height:200px !important; object-fit:cover; }
        .property-info { padding:18px; }
        .property-info h5 { font-weight:600; margin-bottom:8px; }
        .property-info .price { font-size:1.3rem; color:#007bff; font-weight:700; }
        .property-meta { font-size:.97rem; color:#666; }
        .property-meta span { margin-right:12px; }
        .property-location { font-size:1.02rem; color:#495057; margin-bottom:8px; }
        .page-banner { padding:80px 0; background-size:cover; background-position:center; }
        @media (max-width:991px) { .property-thumb img { min-height:150px; } }
    </style>
</head>

<body>
    <?php include("include/header.php"); ?>
    <br><br><br><br>
    <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg'); min-height:250px;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="page-name float-left text-white text-uppercase mt-1 mb-0"><b>Property Grid</b></h2>
                </div>
                <div class="col-md-6">
                    <nav aria-label="breadcrumb" class="float-left float-md-right">
                        <ol class="breadcrumb bg-transparent m-0 p-0">
                            <li class="breadcrumb-item text-white"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">Property Grid</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="full-row">
        <div class="container">
            <?php if ($filter_error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($filter_error); ?></div>
            <?php endif; ?>

            <form id="propertyFilterForm" class="filter-box" method="get" action="property.php">
                <div class="row align-items-end">
                    <div class="col-md-2 col-6 mb-2">
                        <label for="ptype">Type</label>
                        <select class="form-control" id="ptype" name="ptype">
                            <option value="">All</option>
                            <?php if ($ptypesQ): while ($pt = $ptypesQ->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($pt['ptype']); ?>" <?php if (isset($_GET['ptype']) && $_GET['ptype'] == $pt['ptype']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars(ucfirst($pt['ptype'])); ?>
                                </option>
                            <?php } endif; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-6 mb-2">
                        <label for="stype">For</label>
                        <select class="form-control" id="stype" name="stype">
                            <option value="">All</option>
                            <?php if ($stypesQ): while ($st = $stypesQ->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($st['stype']); ?>" <?php if (isset($_GET['stype']) && $_GET['stype'] == $st['stype']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars(ucfirst($st['stype'])); ?>
                                </option>
                            <?php } endif; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-6 mb-2">
                        <label for="city">City</label>
                        <select class="form-control" id="city" name="city">
                            <option value="">All</option>
                            <?php if ($citiesQ): while ($ct = $citiesQ->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($ct['city']); ?>" <?php if (isset($_GET['city']) && $_GET['city'] == $ct['city']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars(ucfirst($ct['city'])); ?>
                                </option>
                            <?php } endif; ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-6 mb-2">
                        <label for="bed">Bedroom</label>
                        <select class="form-control" id="bed" name="bed">
                            <option value="">Any</option>
                            <?php for ($i = 1; $i <= 10; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if (isset($_GET['bed']) && (int)$_GET['bed'] === $i) echo 'selected'; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2 col-6 mb-2">
                        <label for="bath">Bathroom</label>
                        <select class="form-control" id="bath" name="bath">
                            <option value="">Any</option>
                            <?php for ($i = 1; $i <= 10; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if (isset($_GET['bath']) && (int)$_GET['bath'] === $i) echo 'selected'; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-6 mb-1 d-flex">
                        <div class="col-6">
                            <label for="price_min">Min Price</label>
                            <input type="number" class="form-control" id="price_min" name="price_min" placeholder="Min" value="<?php echo isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : ''; ?>">
                        </div>
                        <div class="col-6">
                            <label for="price_max">Max Price</label>
                            <input type="number" class="form-control" id="price_max" name="price_max" placeholder="Max" value="<?php echo isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : ''; ?>">
                        </div>
                    </div>

                    <div class="col-md-12 col-12 text-right">
                        <button class="btn btn-primary mt-3" type="submit"><i class="fa fa-filter"></i> Apply Filter</button>
                        <a href="property.php" class="btn btn-secondary mt-3">Reset</a>
                    </div>
                </div>
            </form>

            <div class="row">
                <!-- PROPERTY GRID -->
                <?php if (empty($filter_error) && count($properties)): ?>
                    <?php foreach ($properties as $row): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="property-card h-100 d-flex flex-column overflow-hidden">
                                <div class="property-thumb">
                                    <a href="propertydetail.php?pid=<?php echo urlencode($row['pid']); ?>">
                                        <img class="hover-zoomer" src="admin/<?php echo htmlspecialchars($row['pimage']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                    </a>
                                    <span class="position-relative text-white" style="top: -50px; left: 40px;">| <?php echo htmlspecialchars($row['asize']); ?></span>
                                    <div class="col-3 pt-0" style="font-size: large;">
                                        <span class="badge badge-<?php echo ($row['stype'] == 'sale' ? 'success' : 'info'); ?> m-2">
                                            For <?php echo htmlspecialchars(ucfirst($row['stype'])); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="property-info flex-grow-1 pt-1">
                                    <h5><a href="propertydetail.php?pid=<?php echo urlencode($row['pid']); ?>" class="text-secondary"><?php echo htmlspecialchars($row['title']); ?></a></h5>
                                    <div class="property-location mb-1">
                                        <i class="fa fa-map-marker text-primary"></i>
                                        <?php echo htmlspecialchars($row['loc']); ?>, <?php echo htmlspecialchars($row['city']); ?>
                                    </div>
                                    <div class="property-meta mb-2">
                                        <span><i class="fa fa-bed text-info"></i> <?php echo (int)$row['bed']; ?> Bed</span>
                                        <span><i class="fa fa-bath text-info"></i> <?php echo (int)$row['bath']; ?> Bath</span>
                                        <span><i class="fa fa-building text-info"></i> <?php echo htmlspecialchars(ucfirst($row['ptype'])); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="small text-muted">By: <?php echo htmlspecialchars($row['uname']); ?></span>
                                        <a href="propertydetail.php?pid=<?php echo urlencode($row['pid']); ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (!$filter_error): ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">No properties found for your filter!</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <?php
            // Build base query string preserving filters except page
            $queryParams = $_GET;
            if (isset($queryParams['page'])) unset($queryParams['page']);
            $baseQuery = http_build_query($queryParams);
            $baseUrl = 'property.php' . ($baseQuery ? '?' . $baseQuery . '&' : '?');
            ?>
            <?php if (empty($filter_error) && $totalProperties > 0 && $totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="<?php echo $page <= 1 ? '#' : htmlspecialchars($baseUrl . 'page=' . ($page - 1)); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        // show a sliding window of pages
                        $start = max(1, $page - 3);
                        $end = min($totalPages, $page + 3);
                        if ($start > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($baseUrl . 'page=1') . '">1</a></li>';
                            if ($start > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        for ($p = $start; $p <= $end; $p++) {
                            $active = $p == $page ? ' active' : '';
                            echo '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars($baseUrl . 'page=' . $p) . '">' . $p . '</a></li>';
                        }
                        if ($end < $totalPages) {
                            if ($end < $totalPages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($baseUrl . 'page=' . $totalPages) . '">' . $totalPages . '</a></li>';
                        }
                        ?>

                        <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                            <a class="page-link" href="<?php echo $page >= $totalPages ? '#' : htmlspecialchars($baseUrl . 'page=' . ($page + 1)); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>

    <br><br>
    <?php include("include/footer.php"); ?>

    <script src="package/Jquery/dist/jquery.slim.min.js"></script>
    <script src="package/Jquery/dist/jquery.min.js"></script>
    <script src="package/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/all.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>
?>