<?php require_once "controllerUserData.php"; ?>

<?php 
$email = isset($_SESSION['email']) ? $_SESSION['email'] : false;
$password = isset($_SESSION['password']) ? $_SESSION['password'] : false;
if($email != false && $password != false){
    $sql = "SELECT * FROM usertable WHERE email = '$email'";
    $run_Sql = mysqli_query($con, $sql);
    if($run_Sql){
        $fetch_info = mysqli_fetch_assoc($run_Sql);
        $status = $fetch_info['status'];
        $code = $fetch_info['code'];
        $profile_created = $fetch_info['profile_created'];
        if($status == "verified"){
            if($code != 0){
                header('Location: reset-code.php');
            }
            if($profile_created == "yes") {
              header('Location: home.php');
            }
        }else if($status == "notverified"){
            header('Location: user-otp.php');
        } else {
            header('Location: home.php');
        }
    }
}
?>
<?php include_once 'header.php'; ?>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 my-0 p-0 shadow" style="min-height: 100vh">
                <div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 339px)">
                    <img src="./public/assets/SoulMate (3).png" alt="logo" style="width: 75%">
                </div>
                <form class="p-4 bg-white" action="signup-user.php" method="POST" autocomplete="">
                    <h2 class="mb-4 font-weight-bold" style="font-size: 24px;">Sign Up</h2>
                    <?php
                    if(count($errors) == 1){
                        ?>
                        <div class="alert alert-danger text-center">
                            <?php
                            foreach($errors as $showerror){
                                echo $showerror;
                            }
                            ?>
                        </div>
                        <?php
                    }elseif(count($errors) > 1){
                        ?>
                        <div class="alert alert-danger">
                            <?php
                            foreach($errors as $showerror){
                            ?>
                                <li><?php echo $showerror; ?></li>
                            <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <input class="form-control" id="email" type="email" name="email" placeholder="Email Address" required value="<?php echo $email ?>" onkeyup="validateEmail(this.value);">
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control" type="password" name="cpassword" placeholder="Confirm password" required>
                    </div>
                    <div class="form-group">
                        <input class="btn btn-primary btn-block py-2 rounded" type="submit" name="signup" value="Sign Up">
                    </div>
                    <div class="link login-link text-center" style="font-size: 12px">Already have an account? <a href="login-user.php" class="font-weight-bold">Sign In!</a></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let email = document.getElementById('email');
        function validateEmail(e) {
            console.log(e);
            let regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if(e.match(regex)) {
                email.classList.remove('is-invalid');
                email.classList.add('is-valid');
            } else {
                email.classList.add('is-invalid');
                email.classList.remove('is-valid');
            }
            email.onblur = () => {
                if(email.value == '') {
                    email.classList.add('is-invalid');
                    email.classList.remove('is-valid');
                } else {
                    validateEmail(email.value);
                }
            }
        }
    </script>
</body>
</html>