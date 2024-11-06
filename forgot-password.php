<?php require_once "controllerUserData.php"; ?>
<?php include_once 'header.php'; ?>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 my-0 p-0 shadow" style="min-height: 100vh">
                <div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 275px)">
                    <img src="./public/assets/logo.png" alt="logo" style="width: 75%">
                </div>
            
                <form class="p-4 bg-white" action="forgot-password.php" method="POST" autocomplete="">
                    <h2 class="mb-2 font-weight-bold" style="font-size: 24px;">Forgot Password</h2>
                    <p class="m-0" style="font-size: 12px;">Forgot your password? Fill the email below and let us send you an email to reset your pasword.</p>
                    <?php
                        if(count($errors) > 0){
                            ?>
                            <div class="alert alert-danger text-center">
                                <?php 
                                    foreach($errors as $error){
                                        echo $error;
                                    }
                                ?>
                            </div>
                            <?php
                        }
                    ?>
                    <div class="form-group mt-4">
                        <input class="form-control" type="email" name="email" placeholder="Enter email address" required value="<?php echo $email ?>">
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary btn-block py-2 rounded" type="submit" name="check-email" value="Continue">
                    </div>
                    <div class="link login-link text-center" style="font-size: 12px">Already have an account? <a href="login-user.php" class="font-weight-bold">Sign In!</a></div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>