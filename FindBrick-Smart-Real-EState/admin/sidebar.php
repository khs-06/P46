<!-- Sidebar -->
<div class="col-md-2 col-lg-2 p-0">
  <div class="sidebar d-flex flex-column">
    <div class="sidebar-header text-center py-3 border-bottom">
      <h4 class="text-primary font-weight-bold mb-0">Admin Panel</h4>
    </div>

    <div class="sidebar-menu flex-grow-1">
      <ul class="list-unstyled mt-3">

        <li class="menu-title text-muted text-uppercase small pl-3">Main</li>
        <li>
          <a href="dashboard.php" class="active">
            <i class="fa fa-home mr-2"></i> Dashboard
          </a>
        </li>

        <li class="menu-title text-muted text-uppercase small pl-3 mt-3">Properties</li>
        <li>
          <a href="propertyview.php"><i class="fa fa-building mr-2"></i> Properties</a>
        </li>

        <li class="menu-title text-muted text-uppercase small pl-3 mt-3">Location</li>
        <li>
          <a href="#locationSub" data-toggle="collapse"><i class="fa fa-map-marker mr-2"></i> Location <i class="fa fa-chevron-down float-right mt-1"></i></a>
          <ul id="locationSub" class="collapse list-unstyled pl-4">
            <li><a href="stateadd.php">Manage States</a></li>
            <li><a href="cityadd.php">Manage Cities</a></li>
          </ul>
        </li>

        <li class="menu-title text-muted text-uppercase small pl-3 mt-3">Users & Agents</li>
        <li>
          <a href="#userSub" data-toggle="collapse"><i class="fa fa-users mr-2"></i> Users <i class="fa fa-chevron-down float-right mt-1"></i></a>
          <ul id="userSub" class="collapse list-unstyled pl-4">
            <li><a href="adminlist.php">Admins</a></li>
            <li><a href="userlist.php">Clients</a></li>
            <li><a href="useragent.php">Agents</a></li>
            <li><a href="userbuilder.php">Builders</a></li>
          </ul>
        </li>

        <li class="menu-title text-muted text-uppercase small pl-3 mt-3">Leads</li>
        <li>
          <a href="#leadSub" data-toggle="collapse"><i class="fa fa-phone mr-2"></i> Inquiries <i class="fa fa-chevron-down float-right mt-1"></i></a>
          <ul id="leadSub" class="collapse list-unstyled pl-4">
            <li><a href="contactview.php">Contact Requests</a></li>
            <li><a href="feedbackview.php">Client Feedback</a></li>
          </ul>
        </li>

        <li class="menu-title text-muted text-uppercase small pl-3 mt-3">Content</li>
        <li>
          <a href="#contentSub" data-toggle="collapse"><i class="fa fa-file-text mr-2"></i> About Us <i class="fa fa-chevron-down float-right mt-1"></i></a>
          <ul id="contentSub" class="collapse list-unstyled pl-4">
            <li><a href="aboutadd.php">Add Info</a></li>
            <li><a href="aboutview.php">View Info</a></li>
          </ul>
        </li>

        <li class="menu-title text-muted text-uppercase small pl-3 mt-3">Account</li>
        <li>
          <a href="#authSub" data-toggle="collapse"><i class="fa fa-lock mr-2"></i> Authentication <i class="fa fa-chevron-down float-right mt-1"></i></a>
          <ul id="authSub" class="collapse list-unstyled pl-4">
            <!-- <li><a href="index.php">Login</a></li> -->
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </li>

      </ul>
    </div>

    <div class="sidebar-footer text-center py-3 border-top">
      <small class="text-muted">&copy; 2025 FindBrickReal Estate Admin</small>
    </div>
  </div>
</div>

<!-- Styles -->
<style>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 240px;
  background: #fff;
  border-right: 1px solid #dee2e6;
  overflow-y: auto;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.sidebar a {
  display: block;
  color: #333;
  padding: 10px 20px;
  text-decoration: none;
  transition: all 0.3s ease;
  border-radius: 6px;
  margin: 4px 8px;
}

.sidebar a:hover, .sidebar a.active {
  background: #007bff;
  color: #fff;
}

.sidebar .menu-title {
  font-size: 12px;
  letter-spacing: 1px;
}

.sidebar ul ul a {
  font-size: 14px;
  padding-left: 35px;
}

.sidebar-footer {
  background: #f8f9fa;
}

.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-thumb {
  background-color: #bbb;
  border-radius: 10px;
}

.main-content {
  margin-left: 240px;
  padding: 20px;
  background: #f9fafb;
  min-height: 100vh;
}
</style>

<!-- Dependencies (Bootstrap 4 + FontAwesome) -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
