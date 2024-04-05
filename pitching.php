<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Florida Rotisserie Fantasy Baseball</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
    .dataTable>thead>tr>th[class*="sort"]:after {
        content: '' !important;
    }

    .dataTable>thead>tr>th[class*="sort"]:before {
        content: '' !important;
    }

    table.dataTable thead>tr>th.sorting_asc,
    table.dataTable thead>tr>th.sorting_desc,
    table.dataTable thead>tr>th.sorting,
    table.dataTable thead>tr>td.sorting_asc,
    table.dataTable thead>tr>td.sorting_desc,
    table.dataTable thead>tr>td.sorting {
        padding: 2px;
    }

    table.dataTable tr>td {
        padding: 2px;
    }

    .position {
        font-size: .8em;
    }

    .position thead,
    tr,
    td {
        padding: 2px;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index.php" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
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

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <img src="logo.png" alt="league-logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Fantasy Baseball</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">Welcome, Jeff/Logan</a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->
                <!-- 
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
        -->

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item menu-open">
                            <a href="#" class="nav-link active">
                                
                                <p>
                                    My Team
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="hitters.php" class="nav-link">
                                        <p> Hitters</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pitching.php" class="nav-link active">
                                        <p>Pitching</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class='nav-icon fas fa-user'></i>
                                <p>Players</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>All Hitters</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Hitters</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <ul class="navbar-nav">
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                style="color:#171a1a;">
                                                <?php
                                                    echo (isset($_GET['days'])) ? "Last " . $_GET['days'] : 'Year to date';
                                                ?>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="hitters.php?days=1">Yesterday</a>
                                                <a class="dropdown-item" href="hitters.php?days=7">Last 7</a>
                                                <a class="dropdown-item" href="hitters.php?days=15">Last 15</a>
                                                <a class="dropdown-item" href="hitters.php?days=30">Last 30</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="hitters.php">Year to date</a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-all" class="table hitters table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Player Name</th>
                                                <th>Position</th>
                                                <th>Team</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>1B</th>
                                                <th>2B</th>
                                                <th>3B</th>
                                                <th>RBI</th>
                                                <th>SB</th>
                                                <th>BB</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>AVG</th>
                                                <th>OBP</th>
                                                <th>SLG</th>
                                                <th>OPS</th>
                                                <th>BABIP</th>
                                                <th>Total Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
        require_once 'dbconfig.php';
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            // execute the stored procedure
            $daysBack = isset($_GET['days']) ? $_GET['days'] : 999;
            $sql = "CALL getTeamStats(5, $daysBack)";
            // call the stored procedure
            $q = $pdo->query($sql);
            $q->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error occurred:" . $e->getMessage());
        }
        ?>

                                            <?php while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['Pos']; ?></td>
                                                <td><?php echo $r['Team']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['1B']; ?></td>
                                                <td><?php echo $r['2B']; ?></td>
                                                <td><?php echo $r['3B']; ?></td>
                                                <td><?php echo $r['RBI']; ?></td>
                                                <td><?php echo $r['SB']; ?></td>
                                                <td><?php echo $r['BB']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['AVG']; ?></td>
                                                <td><?php echo $r['OBP']; ?></td>
                                                <td><?php echo $r['SLG']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['BABIP']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-4">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top C</h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-c" class="table hitters-pos table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getTeamPosStats('C', 5, $daysBack)";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($sql);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            
                                            while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top 1B</h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-1b" class="table hitters-pos table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getTeamPosStats('1B', 5, $daysBack)";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($sql);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            
                                            while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top 2B</h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-1b" class="table hitters-pos table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getTeamPosStats('2B', 5, $daysBack)";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($sql);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            
                                            while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top SS</h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-1b" class="table hitters-pos table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getTeamPosStats('SS', 5, $daysBack)";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($sql);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            
                                            while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top 3B</h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-1b" class="table hitters-pos table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getTeamPosStats('3B', 5, $daysBack)";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($sql);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            
                                            while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="card">
                                <!-- .card-header -->
                                <div class="card-header">
                                    <h4>Top OF</h4>
                                </div>
                                <!-- /.card-header -->
                                <!-- .card-body -->
                                <div class="card-body">
                                    <table id="hitters-1b" class="table hitters-pos table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>AB</th>
                                                <th>R</th>
                                                <th>H</th>
                                                <th>HR</th>
                                                <th>TB</th>
                                                <th>OPS</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            try {
                                                $sql = "CALL getTeamPosStats('OF', 5, $daysBack)";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($sql);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            
                                            while ($r = $q->fetch()): ?>
                                            <tr>
                                                <td><?php echo $r['First Name'] . " " . $r['Last Name']; ?></td>
                                                <td><?php echo $r['AB']; ?></td>
                                                <td><?php echo $r['R']; ?></td>
                                                <td><?php echo $r['H']; ?></td>
                                                <td><?php echo $r['HR']; ?></td>
                                                <td><?php echo $r['TB']; ?></td>
                                                <td><?php echo $r['OPS']; ?></td>
                                                <td><?php echo $r['Total points']; ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                <?php 
                echo ">>--------/-->"
                ?>
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2023 Logan Stanford</a>.</strong> All rights
            reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

    <!-- Page specific script -->
    <script>
    $(function() {
        $('.hitters').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "order": [19, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": false
        });
        $('.hitters-pos').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [7, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": false
        });
    });
    </script>
</body>

</html>