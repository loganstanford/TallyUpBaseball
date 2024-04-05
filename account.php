<?php 
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

// Page variables
$pageTitle = "My Account";

// HTTPS Redirector
redirectHTTPS();

?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <?php include 'head.php'; ?>
    <?php include 'styles.php'; ?>
    <style>
        .account-form {
            max-width: 300px;
            margin: auto;
        }

        #account-body {
            align-items: center;
        }
        input:disabled {
            color: #707a85 !important;
        }
        select:disabled {
            color: #707a85 !important;
            border-color: #707a85 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini dark-mode">
    <div class="wrapper">
        <?php include 'nav.php'; ?>
        <?php include 'side-menu.php'; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <?php include 'content-header.php'; ?>
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php //print_r($_SESSION);?>
                    <!-- Body Content -->
                    <?php
                    if (isset($_GET['error'])) {
                        echo '<div class="alert alert-danger alert-dismissible">';
                        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                        if ($_GET['error'] == 'empty') {
                            echo '<h5><i class="icon fas fa-ban"></i> Profile incomplete</h5>';
                            echo "Some fields empty. Please make sure all fields are filled in before saving changes";
                        }
                        if ($_GET['error'] == 'invalidEmail') {
                            echo '<h5><i class="icon fas fa-ban"></i>  Profile invalid</h5>';
                            echo "Changes not saved. Please enter a valid email.";
                        }
                        if ($_GET['error'] == 'stmtfail') {
                            echo '<h5><i class="icon fas fa-ban"></i>  Statement failed</h5>';
                            echo "An error occurred saving changes.";
                        }
                        if ($_GET['error'] == 'invalidEmail') {
                            echo '<h5><i class="icon fas fa-ban"></i>  Profile invalid</h5>';
                            echo "Changes not saved. Please enter a valid email.";
                        }
                        if ($_GET['error'] == 'noUser') {
                            echo '<h5><i class="icon fas fa-ban"></i>  No user</h5>';
                            echo "Didn't find user when trying to update changes.";
                        }
                        if ($_GET['error'] == 'teamupdatefail') {
                            echo '<h5><i class="icon fas fa-ban"></i>  Error!</h5>';
                            echo "An error occurred updating team.";
                        }
                        if ($_GET['error'] == 'acctUpdateFail') {
                            echo '<h5><i class="icon fas fa-ban"></i>  Error!</h5>';
                            echo "An error occurred updating account info.";
                        }
                        echo "</div>";
                    }
                    else if (isset($_GET['success'])) {
                        echo '<div class="alert alert-success alert-dismissible">';
                        echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                        if ($_GET['success'] == 'acctUpdate') {
                            echo '<h5>Success!</h5>';
                            echo "Account information updated successfully";
                        }
                        if ($_GET['success'] == 'acctUpdateNoInfo') {
                            echo '<h5>Success!</h5>';
                            echo "Account information updated successfully but there was an error retreiving updated data.";
                        }
                        if ($_GET['success'] == 'teamUpdate') {
                            echo '<h5>Pending...</h5>';
                            echo "A request has been submit to the league administrator to associate your account with a team.";
                        }
                        echo '</div>';
                    }
                    ?>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <button id="edit-account" class="btn btn-primary">Edit account</button>
                                </div>
                                <div id="account-body" class="card-body">
                                    <form action="includes/account-update.inc.php" class="account-form" method="post">
                                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                                        <div class="form-group">
                                            <label>Full Name</label>
                                            <input type="text" id="input-name" name="name" class="form-control" value="<?php echo $_SESSION['user_name'];?>" disabled="">
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" id="input-email" name="email" class="form-control" value="<?php echo $_SESSION['user_email'];?>" disabled="">
                                        </div>
                                        <div class="form-group">
                                            <label>My team</label>
                                            <select id="team-select" name="team_id" class="form-control" disabled="">
                                                <option value="-1">None</option>
                                            <?php
                                            try {
                                                $teamsSQL = "SELECT d.name as 'division', m.name as 'team_name', d.id as 'team_id'  FROM divisions as d JOIN managers as m ON d.manager_id = m.id";
                                                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                                                // call the stored procedure
                                                $q = $pdo->query($teamsSQL);
                                                $q->setFetchMode(PDO::FETCH_ASSOC);
                                            } catch (PDOException $e) {
                                                print_r($e->getMessage());
                                                die("Error occurred:" . $e->getMessage());
                                            }
                                            $injuryIds = array();
                                            while ($t=$q->fetch()):
                                                if ($_SESSION['team_id'] == $t['team_id']) {
                                                    echo '<option selected value="' . $t['team_id'] . '">' . $t['division'] . ' - ' . $t['team_name'] . '</option>';
                                                }
                                                else {
                                                    echo '<option value="' . $t['team_id'] . '">' . $t['division'] . ' - ' . $t['team_name'] . '</option>';
                                                }
                                            ?>
                                            <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label>Password</label><a href="#" class="ml-2 text-muted">Update password</a>
                                            <input type="password" id="input-password" name="pwd" class="form-control" disabled="">
                                        </div>
                                        <div class="form-group">
                                            <label>Retype password</label>
                                            <input type="password" id="input-password-repeat" name="pwdRepeat" class="form-control" disabled="">
                                        </div> -->
                                        <button type="submit" name="submit" id="button-save" class="btn btn-primary" disabled="">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <footer class="main-footer">
            <?php include 'footer.php';?>
        </footer>
    </div>
    <?php include 'scripts.php';?>
    <script>
        $('#edit-account').click(function() {
            $("#input-name").prop("disabled", false);
            $("#input-email").prop("disabled", false);
            $("#team-select").prop("disabled", false);
            $("#button-save").prop("disabled", false);
        })
    </script>
</body>
</html>