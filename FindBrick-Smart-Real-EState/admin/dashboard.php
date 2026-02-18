<?php
session_start();
require("config.php");

if (!isset($_SESSION['auser'])) {
  header("location:index.php");
  exit();
}

// =========================
//  FETCH DASHBOARD DATA
// =========================

// Total Properties
$totalQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM property");
$total = 0;
if ($totalQuery) {
  $total = mysqli_fetch_assoc($totalQuery)['total'] ?? 0;
}

// Available Properties (normalize status using LOWER)
$availableQuery = mysqli_query($con, "SELECT COUNT(*) AS available FROM property WHERE LOWER(status)='available'");
$available = 0;
if ($availableQuery) {
  $available = mysqli_fetch_assoc($availableQuery)['available'] ?? 0;
}

// Sold / Rented Properties
$soldQuery = mysqli_query($con, "SELECT COUNT(*) AS sold FROM property WHERE LOWER(status) IN ('sold','rented')");
$sold = 0;
if ($soldQuery) {
  $sold = mysqli_fetch_assoc($soldQuery)['sold'] ?? 0;
}

// Recent Properties
$recentQuery = mysqli_query($con, "SELECT * FROM property ORDER BY pid DESC LIMIT 5");

// =========================
//  Monthly Sales (Safe Version)
// =========================

// Check if sold_date column exists before using it
$checkColumn = mysqli_query($con, "SHOW COLUMNS FROM property LIKE 'sold_date'");
if ($checkColumn && mysqli_num_rows($checkColumn) > 0) {
  // If sold_date exists, use it
  $salesQuery = mysqli_query($con, "
     SELECT MONTHNAME(sold_date) AS month, COUNT(*) AS total 
     FROM property 
     WHERE LOWER(status) IN ('sold','rented') AND sold_date IS NOT NULL
     GROUP BY MONTH(sold_date)
     ORDER BY MONTH(sold_date)
  ");
  $months = [];
  $totals = [];
  if ($salesQuery) {
    while ($row = mysqli_fetch_assoc($salesQuery)) {
      $months[] = $row['month'];
      $totals[] = (int)$row['total'];
    }
  }
  // if query returned nothing, keep fallback later
}

if (!isset($months) || empty($months)) {
  // fallback sample data
  $months = ['Jan', 'Feb', 'Mar', 'Apr'];
  $totals = [2, 3, 4, 1];
}

// =========================
//  Top Agents (Dynamic)
// =========================

// We'll try to fetch top 2 agents by number of properties.
// If the agents table doesn't exist or query fails, we'll fallback to a safe static list.

$topAgents = [];

$checkAgentsTable = mysqli_query($con, "SHOW TABLES LIKE 'agent'");
if ($checkAgentsTable && mysqli_num_rows($checkAgentsTable) > 0) {
  // Agents table exists — build query
  // We join agents with property and count properties per agent.
  // Use COALESCE for avatar to ensure we always have a path to show.
  $topAgentsQuery = "
    SELECT a.id, a.name, a.email, a.phone,
           COALESCE(NULLIF(a.image, ''), 'assets/img/profiles/avatar-01.png') AS avatar,
           COUNT(p.pid) AS properties_count
    FROM agent a
    JOIN property p ON p.agent_id = a.id
    GROUP BY a.id, a.name, a.email, a.phone, a.image
    ORDER BY properties_count DESC, a.name ASC
    LIMIT 2
  ";
  $res = mysqli_query($con, $topAgentsQuery);
  if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
      $topAgents[] = $row;
    }
  }
}

// Fallback: if no dynamic agents found, provide safe static placeholders
if (empty($topAgents)) {
  $topAgents[] = [
    'id' => 0,
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => 'N/A',
    'avatar' => 'assets/img/profiles/avatar-01.png',
    'properties_count' => 0
  ];
  $topAgents[] = [
    'id' => 0,
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'phone' => 'N/A',
    'avatar' => 'assets/img/profiles/avatar-01.png',
    'properties_count' => 0
  ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>FindBrick Admin Dashboard</title>
  <link rel="shortcut icon" type="image/x-icon" href="../images/fb-logo.png">

  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="assets/js/chart.js"></script>
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

    body {
      background: #f9f9f9;
      color: #111;
      transition: all 0.3s ease;
    }

    .dark-mode {
      background: #121212;
      color: #f5f5f5;
    }

    .dark-mode .sidebar {
      background: #1b1b1b;
    }

    .dark-mode a {
      color: #f5f5f5;
    }

    .dark-mode .topbar {
      background: #1e1e1e;
      color: #ddd;
    }

    .agent-avatar {
      width: 36px;
      height: 36px;
      object-fit: cover;
      border-radius: 50%;
      margin-right: 12px;
    }

    .agent-list-item {
      display: flex;
      align-items: center;
      gap: 12px;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">

      <!-- Sidebar -->
      <?php include("sidebar.php"); ?>

      <!-- Main content -->
      <main class="col-md-10 col-lg-10 px-md-4">

        <div class="topbar mt-2 mb-4 rounded">
          <h3 class="mb-0">Dashboard</h3>
          <div class="d-flex align-items-center gap-3">
            <a href="profile.php">
              <img src="assets/img/profiles/avatar-01.png" class="rounded-circle" width="40" height="40" alt="Profile">
            </a>
            <i class="bi bi-bell fs-4 text-secondary"></i>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-4">
            <div class="card card-custom p-4 text-center">
              <i class="bi bi-building text-primary fs-2"></i>
              <h5>Total Properties</h5>
              <h2 class="fw-bold"><?php echo (int)$total; ?></h2>
              <p class="text-muted">All property listings</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-custom p-4 text-center">
              <i class="bi bi-check-circle text-success fs-2"></i>
              <h5>Available</h5>
              <h2 class="fw-bold"><?php echo (int)$available; ?></h2>
              <p class="text-muted">Currently for sale/rent</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-custom p-4 text-center">
              <i class="bi bi-x-circle text-danger fs-2"></i>
              <h5>Sold</h5>
              <h2 class="fw-bold"><?php echo (int)$sold; ?></h2>
              <p class="text-muted">Already sold</p>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="card card-custom p-3">
              <h5 class="mb-3">Property Status</h5>
              <canvas id="propertyChart"></canvas>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-custom p-3">
              <h5 class="mb-3">Monthly Sales</h5>
              <canvas id="salesChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Progress Bars & Top Agents -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="card card-custom p-3">
              <h5>Property Status Overview</h5>
              <div class="mt-3">
                <span>Available</span>
                <div class="progress mb-2" style="height:10px;">
                  <div class="progress-bar bg-primary" style="width:<?php echo $total > 0 ? round(($available / $total) * 100) : 0; ?>%"></div>
                </div>
                <span>Sold</span>
                <div class="progress" style="height:10px;">
                  <div class="progress-bar bg-danger" style="width:<?php echo $total > 0 ? round(($sold / $total) * 100) : 0; ?>%"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dynamic Top Agents Column -->
          <div class="col-md-6">
            <div class="card card-custom p-3">
              <h5>Top Agents</h5>
              <ul class="list-group list-group-flush mt-3">
                <?php foreach ($topAgents as $agent) : ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="agent-list-item">
                      <img src="<?php echo htmlspecialchars($agent['avatar']); ?>" alt="Avatar" class="agent-avatar">
                      <div>
                        <div class="fw-semibold"><?php echo htmlspecialchars($agent['name']); ?></div>
                        <div class="text-muted small"><?php echo htmlspecialchars($agent['email']); ?> &middot; <?php echo htmlspecialchars($agent['phone']); ?></div>
                      </div>
                    </div>
                    <span class="badge bg-primary rounded-pill"><?php echo (int)$agent['properties_count']; ?> Properties</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Recent Properties Table -->
        <div class="card card-custom p-3 mb-5">
          <h5>Recent Properties</h5>
          <div class="table-responsive mt-3">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Property Name</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recentQuery) {
                  while ($row = mysqli_fetch_assoc($recentQuery)) { ?>
                    <tr>
                      <td><?php echo (int)$row['pid']; ?></td>
                      <td><?php echo htmlspecialchars($row['title'] ?? 'N/A'); ?></td>
                      <td><?php echo htmlspecialchars($row['ptype'] ?? 'N/A'); ?></td>
                      <td>
                        <?php if (isset($row['status']) && strtolower($row['status']) == "available") { ?>
                          <span class="badge bg-success">Available</span>
                        <?php } else { ?>
                          <span class="badge bg-danger">Sold</span>
                        <?php } ?>
                      </td>
                      <td><?php echo isset($row['price']) ? htmlspecialchars($row['price']) : '0'; ?> <?php echo htmlspecialchars($row['price_type'] ?? ''); ?></td>
                      <td><a href="edit-property.php?id=<?php echo (int)$row['pid']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script>
    // Pie chart for property status
    new Chart(document.getElementById('propertyChart'), {
      type: 'pie',
      data: {
        labels: ['Available', 'Sold'],
        datasets: [{
          data: [<?php echo (int)$available; ?>, <?php echo (int)$sold; ?>],
          backgroundColor: ['#0d6efd', '#dc3545']
        }]
      }
    });

    // Bar chart for monthly sales
    const months = <?php echo json_encode($months); ?>;
    const totals = <?php echo json_encode($totals); ?>;
    new Chart(document.getElementById('salesChart'), {
      type: 'bar',
      data: {
        labels: months,
        datasets: [{
          label: 'Sold Properties',
          data: totals,
          backgroundColor: '#198754'
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>

</html>