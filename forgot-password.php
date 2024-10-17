<?php require_once "controllerUserData.php"; ?>
<?php include_once 'header.php'; ?>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 form">
                <div class="text-center border-bottom py-2">
                    <img src="./public/assets/SoulMate (3).png" alt="logo" height="100">
                </div>
                <form action="forgot-password.php" method="POST" autocomplete="">
                    <h2 class="text-center">Forgot Password</h2>
                    <p class="text-center">Enter your email address</p>
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
                    <div class="form-group mt-5">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" placeholder="Enter email address" required value="<?php echo $email ?>">
                    </div>
                    <div class="form-group">
                        <input class="form-control button" type="submit" name="check-email" value="Continue">
                    </div>
                    <div class="form-group">
                        <a class="form-control button text-center" href="login-user.php" >Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>