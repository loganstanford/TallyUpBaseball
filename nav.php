<nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
                <?php 
                if (isset($_SESSION["user_email"])) {
                    echo '<li class="nav-item d-sm-inline-block"><a href="account.php" class="nav-link">Account</a></li>';
                    echo '<li class="nav-item d-sm-inline-block"><a href="includes/logout.inc.php" class="nav-link">Logout</a></li>';
                }
                else {
                    echo '<li class="nav-item d-sm-inline-block"><a href="login.php" class="nav-link">Login</a></li>';
                    echo '<li class="nav-item d-sm-inline-block"><a href="register.php" class="nav-link">Sign up</a></li>';
                }
                ?>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <?php
                if (isset($_SESSION['admin'])){
                    if ($_SESSION['admin'] == 1) {
                        $n_count = 0;
                        $approvals = getApprovals($conn);
                        if (count($approvals) > 0) {
                            $n_count += count($approvals);
                            //print_r($approvals[0][3]);
                            $last_time = $approvals[0][3];
                        }
                        ?>
                
                        <?php
                        echo '<li class="nav-item dropdown">';
                        echo '<a class="nav-link" data-toggle="dropdown" href="#">';
                        echo '<i class="far fa-bell"></i>';
                        echo '<span class="badge bg-info navbar-badge">' . $n_count . '</span>';
                        echo '</a>';
                        echo '<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">';
                        echo '<a href="#" class="dropdown-item">';
                        echo '<i class="fas fa-users mr-2"></i> ' . $n_count . ' team approvals';
                        echo '<span id="notification-time" class="float-right text-muted text-sm"></span>';
                        echo '</a>';
                        echo '</div>';
                        echo '</li>';
                    }
                }
                
                ?>
                
                <!-- Navbar Search -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>

                <!-- Messages Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" href="https://mb.boardhost.com/bullpen/">
                        <i class="far fa-comments"></i>
                    </a>
                </li>
                <!-- Notifications Dropdown Menu -->
            </ul>
        </nav>
        <!-- /.navbar -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script>$('#notification-time')[0].innerHTML = moment("<?php echo $last_time; ?>", "YYYY-MM-DD HH:mm:ss").fromNow();</script>