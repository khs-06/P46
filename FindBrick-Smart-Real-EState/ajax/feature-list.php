<?php
// include("../config.php");
include("../session-check.php");

// Ensure no accidental output (no whitespace before <?php or after 

// Set content type to JSON
header('Content-Type: application/json');

// Validate session
if (!isset($_SESSION['uid'])) {
    echo json_encode([
        "cards" => '<div class="col-12 text-center text-danger py-5">Session expired. Please log in.</div>',
        "pagination" => ''
    ]);
    exit;
}

$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = 9;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Find agent_id for this user
$stmt = $conn->prepare("SELECT agent_id FROM property WHERE uid=? LIMIT 1");
$stmt->bind_param("i", $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode([
        "cards" => '<div class="col-12 text-center text-muted py-5">You have no properties listed. Please add properties first.</div>',
        "pagination" => ''
    ]);
    exit;
}
$agent_id = $result->fetch_assoc()['agent_id'];
$stmt->close();

$where = "agent_id=?";
$params = [$agent_id];
$types = "i";

if ($search) {
    $where .= " AND (title LIKE ? OR city LIKE ? OR state LIKE ?)";
    $search_str = "%$search%";
    $params = array_merge($params, [$search_str, $search_str, $search_str]);
    $types .= "sss";
}

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM property WHERE $where ORDER BY created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) $properties[] = $row;

$total_rows_result = $conn->query("SELECT FOUND_ROWS() as total");
$total_rows = $total_rows_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$cards = '';
foreach ($properties as $prop) {
    $img = htmlspecialchars($prop['pimage']);
    $title = htmlspecialchars($prop['title']);
    $price = htmlspecialchars($prop['price']) . ' / ' . htmlspecialchars($prop['price_type']);
    $city = htmlspecialchars($prop['city']);
    $state = htmlspecialchars($prop['state']);
    $pid = (int)$prop['pid'];
    $desc = substr($prop['feature'] ?? '', 0, 60) . (strlen($prop['feature']) > 60 ? '...' : '');
    $cards .= '
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card property-card h-200 shadow-sm">
            <a href="propertydetail.php?pid=' . $pid . '">
                <img src="admin/' . $img . '" class="property-thumb" alt="' . $title . '">
            </a>
            <div class="card-body py-3 px-3">
                <h5 class="mb-1"><a class="text-secondary hover-text-primary" href="propertydetail.php?pid=' . $pid . '">' . $title . '</a></h5>
                <div class="text-primary font-weight-bold mb-2">₹' . $price . '</div>
                <div class="small text-muted mb-1"><i class="fa fa-map-marker"></i> ' . $city . ', ' . $state . '</div>
                <div class="small text-muted">' . $desc . '</div>
            </div>
            <div class="card-footer bg-transparent text-center border-0 pb-3">
                <a href="#" class="btn btn-outline-danger btn-sm px-4 delete-property" data-pid="' . $pid . '">Delete</a>
                <a href="propertyedit.php?pid=' . $pid . '" class="btn btn-outline-primary btn-sm px-4">Update</a>
            </div>
        </div>
    </div>
    ';
}
if (!$cards) $cards = '<div class="col-12 text-center text-muted py-5">No featured properties found.</div>';

// Pagination
$pagination = '';
if ($total_pages > 1) {
    $pagination .= '<li class="page-item' . ($page == 1 ? ' disabled' : '') . '"><a class="page-link feature-page-link" href="#" data-page="' . ($page - 1) . '">Previous</a></li>';
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $page) {
            $pagination .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link feature-page-link" href="#" data-page="' . $i . '">' . $i . '</a></li>';
        }
    }
    $pagination .= '<li class="page-item' . ($page == $total_pages ? ' disabled' : '') . '"><a class="page-link feature-page-link" href="#" data-page="' . ($page + 1) . '">Next</a></li>';
}

echo json_encode([
    "cards" => $cards,
    "pagination" => $pagination
]);
exit;
?>