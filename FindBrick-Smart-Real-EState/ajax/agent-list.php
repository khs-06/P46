<?php
include("../config.php");

$page = isset($_GET['page']) ? max((int)$_GET['page'],1) : 1;
$limit = 9;
$offset = ($page-1)*$limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = "1";
$param = [];
if ($search) {
    $where .= " AND (name LIKE ? OR city LIKE ? OR specialty LIKE ?)";
    $search_str = "%$search%";
    $param = [$search_str, $search_str, $search_str];
}

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM agent WHERE $where ORDER BY id DESC LIMIT $offset,$limit";
$stmt = $con->prepare($where === "1" ? str_replace('WHERE 1', '', $sql) : $sql);

if ($where !== "1") {
    $stmt->bind_param("sss", ...$param);
}
$stmt->execute();
$result = $stmt->get_result();
$agents = [];
while($row = $result->fetch_assoc()) $agents[] = $row;

// Total rows for pagination
$total_rows_result = $con->query("SELECT FOUND_ROWS() as total");
$total_rows = $total_rows_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows/$limit);

$showAllIfNoResult = false;
if(!$agents && $search) {
    // If no agents found for search, show all agents
    $showAllIfNoResult = true;
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM agent ORDER BY id DESC LIMIT $offset,$limit";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $agents = [];
    while($row = $result->fetch_assoc()) $agents[] = $row;
    $total_rows_result = $con->query("SELECT FOUND_ROWS() as total");
    $total_rows = $total_rows_result->fetch_assoc()['total'];
    $total_pages = ceil($total_rows/$limit);
}

$cards = '';
if ($showAllIfNoResult) {
    $cards .= '<div class="col-12 text-center text-warning py-3">No agents found for your search. Showing all agents instead.</div>';
}
foreach($agents as $row){
    $img = htmlspecialchars($row['image'] ?? 'images/team/default.jpg');
    $name = htmlspecialchars($row['name']);
    $specialty = htmlspecialchars($row['specialty'] ?? '');
    $city = htmlspecialchars($row['city'] ?? '');
    $agent_id = (int)$row['id'];
    $desc = htmlspecialchars($row['bio'] ?? '');
    $cards .= '
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card agent-card shadow-sm h-100">
            <a href="agentdetail.php?id='.$agent_id.'">
                <img src="'.$img.'" class="agent-avatar" alt="'.$name.'">
            </a>
            <div class="card-body text-center p-3">
                <h5 class="mb-1"><a class="text-secondary hover-text-primary" href="agentdetail.php?id='.$agent_id.'">'.$name.'</a></h5>
                <div class="small text-muted mb-1">'.($specialty ? $specialty : 'Real Estate Agent').'</div>
                <div class="small text-primary mb-2">'.($city ? $city : '').'</div>
                <div class="agent-social mb-2">
                    '.(isset($row['facebook']) && $row['facebook'] ? '<a href="'.htmlspecialchars($row['facebook']).'" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>' : '').'
                    '.(isset($row['twitter']) && $row['twitter'] ? '<a href="'.htmlspecialchars($row['twitter']).'" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>' : '').'
                    '.(isset($row['linkedin']) && $row['linkedin'] ? '<a href="'.htmlspecialchars($row['linkedin']).'" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>' : '').'
                </div>
                <div class="small text-muted">'.substr($desc,0,80).(strlen($desc)>80?'...':'').'</div>
            </div>
            <div class="card-footer bg-transparent text-center border-0 pb-3">
                <a href="agentdetail.php?id='.$agent_id.'" class="btn btn-outline-primary btn-sm px-4">View Profile</a>
            </div>
        </div>
    </div>
    ';
}

if(!$cards) $cards = '<div class="col-12 text-center text-muted py-5">No agents found.</div>';

// Pagination
$pagination = '';
if($total_pages > 1){
    $pagination .= '<li class="page-item'.($page==1?' disabled':'').'"><a class="page-link agent-page-link" href="#" data-page="'.($page-1).'">Previous</a></li>';
    for($i=1; $i<=$total_pages; $i++){
        if($i == $page){
            $pagination .= '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link agent-page-link" href="#" data-page="'.$i.'">'.$i.'</a></li>';
        }
    }
    $pagination .= '<li class="page-item'.($page==$total_pages?' disabled':'').'"><a class="page-link agent-page-link" href="#" data-page="'.($page+1).'">Next</a></li>';
}

echo json_encode([
    "cards" => $cards,
    "pagination" => $pagination
]);