<?php
header('Content-Type: text/html; charset=UTF-8');
include("../config.php"); // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = trim($_POST['search']);

    $agents = [];
    $properties = [];

    // Search agents/builders
    $stmtA = $conn->prepare("SELECT id, name, utype FROM agent WHERE name LIKE ? OR utype LIKE ? LIMIT 10");
    $like = "%$search%";
    $stmtA->bind_param("ss", $like, $like);
    $stmtA->execute();
    $resA = $stmtA->get_result();
    while ($row = $resA->fetch_assoc()) $agents[] = $row;
    $stmtA->close();

    // Search properties
    $stmtP = $conn->prepare("SELECT pid, title, city, state FROM property WHERE title LIKE ? OR city LIKE ? OR state LIKE ? LIMIT 10");
    $stmtP->bind_param("sss", $like, $like, $like);
    $stmtP->execute();
    $resP = $stmtP->get_result();
    while ($row = $resP->fetch_assoc()) $properties[] = $row;
    $stmtP->close();

    // Modern CSS (add to your main CSS file for production)
    echo '<style>
    .search-dropdown-modern { background: #fff; box-shadow: 0 8px 24px rgba(44,62,80,0.12); border-radius: 12px; overflow-y:auto; max-height:250px; min-width: 300px;}
    .search-dropdown-modern li { transition: background 0.15s; display: flex; align-items: center; gap: 14px; padding: 10px 18px; border-bottom: 1px solid #f3f3f3; cursor: pointer; }
    .search-dropdown-modern li:last-child { border-bottom: none; }
    .search-dropdown-modern li:hover { background: #f4f6ff; }
    .search-result-icon { font-size: 1.2em; color: #0d6efd; min-width: 32px; text-align: center; }
    .search-result-title { font-weight: 500; color: #222; }
    .search-result-meta { font-size: 0.95em; color: #888; }
    .search-agent-badge, .search-property-badge { font-size: 0.85em; background: #e9ecef; color: #555; border-radius: 6px; padding: 2px 7px; margin-left: 7px; }
    </style>';

    echo "<ul class='search-dropdown-modern list-group'>";

    // Agents/Builders
    if (count($agents)) {
        foreach ($agents as $a) {
            $name = htmlspecialchars($a['name']);
            $type = htmlspecialchars($a['utype']);
            echo "<li onclick=\"window.location='agentdetail.php?id={$a['id']}'\">
                <span class='search-result-icon'><i class='fa fa-user-tie'></i></span>
                <div>
                    <span class='search-result-title'>$name</span>
                    <span class='search-agent-badge'>$type</span>
                </div>
            </li>";
        }
    }
    // Properties
    if (count($properties)) {
        foreach ($properties as $p) {
            $title = htmlspecialchars($p['title']);
            $city = htmlspecialchars($p['city']);
            $state = htmlspecialchars($p['state']);
            echo "<li onclick=\"window.location='propertydetail.php?pid={$p['pid']}'\">
                <div>
                    <span class='search-result-title'>$title</span>
                    <span class='search-property-badge'>Property</span><br>
                    <span class='search-result-meta'><i class='fa fa-map-marker-alt'></i> $city, $state</span>
                </div>
            </li>";
        }
    }
    // No results
    if (!count($agents) && !count($properties)) {
        echo "<li class='text-muted'><span class='search-result-icon'><i class='fa fa-search'></i></span> No results found</li>";
    }

    echo "</ul>";
    exit;
}
echo "<ul class='search-dropdown-modern list-group'><li class='text-danger'><span class='search-result-icon'><i class='fa fa-exclamation-circle'></i></span> Invalid request</li></ul>";
exit;
?>