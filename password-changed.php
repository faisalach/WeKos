<?php require_once "controllerUserData.php"; ?>
<?php
if($_SESSION['info'] == false){
    header('Location: login-user.php');  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style-form.css">
    <style>
        #gif {
            z-index: 0;
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            opacity: .5;
            z-index: -2;
        }

        .vr {
            border: .5px solid rgba(0, 0, 0, .1);
            height: 100px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
            justify-content: space-evenly;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 form login-form">
            <?php 
            if(isset($_SESSION['info'])){
                ?>
                <div class="alert alert-success text-center">
                <?php echo $_SESSION['info']; ?>
                </div>
                <?php
            }
            ?>
                <form action="login-user.php" method="POST">
                    <div class="form-group">
                        <input class="btn btn-primary btn-block py-2 rounded" type="submit" name="login-now" value="Login Now">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>