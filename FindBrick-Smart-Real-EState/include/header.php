<header id="mainHeader" class="header fixed-top">
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand font-weight-bold" href="index.php">
        <!-- <img src="images/FB-logo.jpg" alt="FindBrick Logo" class="logo-img"> -->
        FindBrick
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavList" aria-controls="navbarNavList" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavList">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="property.php">Property</a></li>
          <!-- <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Services</a>
            <div class="dropdown-menu services-dropdown" aria-labelledby="servicesDropdown">
              <a class="dropdown-item services-dropdown-item" href="buy.php">Buy</a>
              <a class="dropdown-item services-dropdown-item" href="sell.php">Sell</a>
              <a class="dropdown-item services-dropdown-item" href="rent.php">Rent</a>
            </div>
          </li> -->
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="agent.php">Our Agents</a></li>

          <?php
          $stmt5 = $conn->prepare("SELECT utype FROM user WHERE uid = ?");
          $stmt5->bind_param("i", $_SESSION['uid']);
          $stmt5->execute();
          $userData = $stmt5->get_result()->fetch_assoc();
          // global $urole;
          $urole = $userData['utype'] ?? '';
          $stmt5->close();

          if (isset($_SESSION['uemail'])): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                My Account
              </a>
              <div class="dropdown-menu account-dropdown" aria-labelledby="accountDropdown">
                <a class="dropdown-item account-dropdown-item" href="profile.php">Profile</a>
                <!-- <a class="dropdown-item" href="request.php">Property Request</a> -->
                <?php if ($urole === 'agent' || $urole === 'builder'): ?>
                  <a class="dropdown-item account-dropdown-item" href="your_property.php">Your Property</a>
                <?php endif; ?>
                <!-- <div class="dropdown-divider"></div> -->
                <a class="dropdown-item account-dropdown-item" href="logout.php">Log out</a>
              </div>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Log in</a>
            </li>
          <?php endif; ?>
          <!-- <li class="nav-item"><a class="nav-link" href="#">Log in</a></li> -->
        </ul>
      </div>
    </div>
  </nav>
</header>