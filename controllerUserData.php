<?php 
header("Access-Control-Allow-Origin: *");
session_start();
require "connection.php";
$email = "";
$errors = array();


function consoleLog($x) {
	echo '<script type="text/javascript">' . 'console.log' . '(' . '"' . $x . '"' . ');</script>';
}

//if user signup button
if(isset($_POST['signup'])){
    //$name = mysqli_real_escape_string($con, $_POST['name']);
	$email = mysqli_real_escape_string($con, $_POST['email']);
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
	if($password !== $cpassword){
		$errors['password'] = "Confirm password not matched!";
	}
	$email_check = "SELECT * FROM usertable WHERE email = '$email'";
	$res = mysqli_query($con, $email_check);
	if(mysqli_num_rows($res) > 0){
		$errors['email'] = "Email that you have entered already exist!";
		header('Location: login-user.php');
	}
	if(count($errors) === 0){
		$encpass = password_hash($password, PASSWORD_BCRYPT);
		$code = rand(999999, 111111);
		$status = "notverified";
		$profile_created = "no";
		$ran_id = rand(time(), 100000000);
		$active = "Active now";
		$insert_data = "INSERT INTO usertable (email, password, code, status, profile_created, unique_id, active)
		values('$email', '$encpass', '$code', '$status', '$profile_created', '$ran_id', '$active')";
		$data_check = mysqli_query($con, $insert_data);
		if($data_check){
			$subject = "Email Verification Code";
			$message = "Your verification code is $code";
			$sender = "From: SoulMate";
			if(mail($email, $subject, $message, $sender)){
				$info = "We've sent a verification code to your email - $email";
				$_SESSION['info'] = $info;
				$_SESSION['email'] = $email;
				$_SESSION['password'] = $password;
				$_SESSION['unique_id'] = $ran_id;
				header('location: user-otp.php');
				exit();
			}else{
				$errors['otp-error'] = "Failed while sending code!";
			}
		}else{
			$errors['db-error'] = "Failed while inserting data into database!";
		}
	}
}
    //if user click verification code submit button
if(isset($_POST['check'])){
	$_SESSION['info'] = "";
	$otp_code = mysqli_real_escape_string($con, $_POST['otp']);
	$check_code = "SELECT * FROM usertable WHERE code = $otp_code";
	$code_res = mysqli_query($con, $check_code);
	if(mysqli_num_rows($code_res) > 0){
		$fetch_data = mysqli_fetch_assoc($code_res);
		$fetch_code = $fetch_data['code'];
		$email = $fetch_data['email'];
		$uid = $fetch_data['uid'];
		$profile_created = $fetch_data['profile_created'];
		$code = 0;
		$status = 'verified';
		$update_otp = "UPDATE usertable SET code = $code, status = '$status' WHERE code = $fetch_code";
		$update_res = mysqli_query($con, $update_otp);
		if($update_res){
			$_SESSION['email'] = $email;
			if($profile_created == 'yes') {
				$query = "SELECT name FROM userprofile WHERE uid = '$uid'";
				$res = mysqli_query($con, $query);
				$fetch = mysqli_fetch_assoc($res);
				$uname = ucfirst(explode(" ", $fetch['name'])[0]);
				$_SESSION['msg_header'] = "Hello";
				$_SESSION['msg'] = "Welcome to SoulMate, " . $uname . "!";
				header('location: home.php');
			} else {
				header('location: profile-input.php');
			}
			exit();
		}else{
			$errors['otp-error'] = "Failed while updating code!";
		}
	}else{
		$errors['otp-error'] = "You've entered incorrect code!";
	}
}

    //if user click login button
if(isset($_POST['login'])){
	$email = mysqli_real_escape_string($con, $_POST['email']);
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$check_email = "SELECT * FROM usertable WHERE email = '$email'";
	$res = mysqli_query($con, $check_email);
	if(mysqli_num_rows($res) > 0){
		$fetch = mysqli_fetch_assoc($res);
		$fetch_pass = $fetch['password'];
		$uid = $fetch['uid'];
		$unique_id = $fetch['unique_id'];

		$status = "Active now";
		$sql2 = mysqli_query($con, "UPDATE usertable SET active = '$status' WHERE uid = '$uid'");
		if($sql2){
			$_SESSION['unique_id'] = $unique_id;
			echo "success";
		}

		if(password_verify($password, $fetch_pass)){
			$_SESSION['email'] = $email;
			$status = $fetch['status']; 
			$profile_created = $fetch['profile_created'];
			if($status == 'verified'){
				$_SESSION['email'] = $email;
				$_SESSION['password'] = $password;
				if($profile_created == 'yes') {
					$query = "SELECT name FROM userprofile WHERE uid = '$uid'";
					$res = mysqli_query($con, $query);
					$fetch = mysqli_fetch_assoc($res);
					$uname = ucfirst(explode(" ", $fetch['name'])[0]);
					$_SESSION['msg_header'] = "Hello";
					$_SESSION['msg'] = "Welcome back, " . $uname . "!";


					header('location: home.php');
				} else {
					header('location: profile-input.php');
				}
			}else{
				$info = "It's look like you haven't still verified your email - $email";
				$_SESSION['info'] = $info;
				header('location: user-otp.php');
			}
		}else{
			$errors['email'] = "Incorrect email or password!";
		}
	}else{
		$errors['email'] = "It looks like you're not yet a member! Click on the bottom link to signup.";
	}
}

    //if user click continue button in forgot password form
if(isset($_POST['check-email'])){
	$email = mysqli_real_escape_string($con, $_POST['email']);
	$check_email = "SELECT * FROM usertable WHERE email='$email'";
	$run_sql = mysqli_query($con, $check_email);
	if(mysqli_num_rows($run_sql) > 0){
		$code = rand(999999, 111111);
		$insert_code = "UPDATE usertable SET code = $code WHERE email = '$email'";
		$run_query =  mysqli_query($con, $insert_code);
		if($run_query){
			$subject = "Password Reset Code";
			$message = "Your password reset code is $code";
			$sender = "From: SoulMate";
			if(mail($email, $subject, $message, $sender)){
				$info = "We've sent a passwrod reset otp to your email - $email";
				$_SESSION['info'] = $info;
				$_SESSION['email'] = $email;
				header('location: reset-code.php');
				exit();
			}else{
				$errors['otp-error'] = "Failed while sending code!";
			}
		}else{
			$errors['db-error'] = "Something went wrong!";
		}
	}else{
		$errors['email'] = "This email address does not exist!";
	}
}

    //if user click check reset otp button
if(isset($_POST['check-reset-otp'])){
	$_SESSION['info'] = "";
	$otp_code = mysqli_real_escape_string($con, $_POST['otp']);
	$check_code = "SELECT * FROM usertable WHERE code = $otp_code";
	$code_res = mysqli_query($con, $check_code);
	if(mysqli_num_rows($code_res) > 0){
		$fetch_data = mysqli_fetch_assoc($code_res);
		$email = $fetch_data['email'];
		$_SESSION['email'] = $email;
		$info = "Please create a new password that you don't use on any other site.";
		$_SESSION['info'] = $info;
		header('location: new-password.php');
		exit();
	}else{
		$errors['otp-error'] = "You've entered incorrect code!";
	}
}

    //if user click change password button
if(isset($_POST['change-password'])){
	$_SESSION['info'] = "";
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
	if($password !== $cpassword){
		$errors['password'] = "Confirm password not matched!";
	}else{
		$code = 0;
            $email = $_SESSION['email']; //getting this email using session
            $encpass = password_hash($password, PASSWORD_BCRYPT);
            $update_pass = "UPDATE usertable SET code = $code, password = '$encpass' WHERE email = '$email'";
            $run_query = mysqli_query($con, $update_pass);
            if($run_query){
            	$info = "Your password is changed. Now you can login with your new password.";
            	$_SESSION['info'] = $info;
            	header('Location: password-changed.php');
            }else{
            	$errors['db-error'] = "Failed to change your password!";
            }
        }
    }
    
   //if login now button click
    if(isset($_POST['login-now'])){
    	header('Location: login-user.php');
    }

    //if profile form submitted
    if(isset($_POST['profile-submit'])){
    	$email = $_SESSION['email'];
    	$uid = '';
    	$count = 0;

        //fetch the user of current session's uid
    	$query = "SELECT uid FROM usertable WHERE email = '$email'";
    	$res = mysqli_query($con, $query);
    	if(mysqli_num_rows($res) > 0) {
    		$fetch = mysqli_fetch_assoc($res);
    		$uid = $fetch['uid'];
    	}

        //userprofile table
    	$name = $_POST['fname'] . " " . $_POST['lname'];
    	$name = mysqli_real_escape_string($con, $name);
    	$age = mysqli_real_escape_string($con, $_POST['age']);
    	$gender = mysqli_real_escape_string($con, $_POST['gender']);
    	$_POST['height'] == "" ? $height = NULL : $height = mysqli_real_escape_string($con, $_POST['height']);  
    	$_POST['weight'] == "" ? $weight = NULL : $weight = mysqli_real_escape_string($con, $_POST['weight']);     
    	$lat = mysqli_real_escape_string($con, $_POST['lat']);
    	$long = mysqli_real_escape_string($con, $_POST['long']);    
    	$profile_photo = ""; 
    	$bio = mysqli_real_escape_string($con, $_POST['bio']);
    	$target_dir = "public/user-profiles/";
    	$target_file = $target_dir . $_FILES["profile_photo"]["name"];
    	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        //validate file type, must be only image
    	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg")
    		$errors['imageFileType'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    	else {
    		move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/SoulMate/public/user-profiles/user-" . $uid . "-" . $_FILES["profile_photo"]["name"]);
    		$profile_photo = "./public/user-profiles/user-" . $uid . "-" . $_FILES["profile_photo"]["name"];
    	}
    	$query = "INSERT INTO userprofile VALUES ('$uid', '$name', '$age', '$gender', '$height', '$weight', '$lat', '$long', '$profile_photo', '$bio')";
    	$result = mysqli_query($con, $query);
    	if($result){
    		consoleLog("userprofile inserted");
    		$count++;
    	} else {
    		consoleLog(mysqli_error($con));
    		$errors['db-error'] = "Something went wrong with userprofile!";
    	}

        //social table
    	$_POST['ig'] == "" ? $ig = NULL : $ig = mysqli_real_escape_string($con, $_POST['ig']);
    	$_POST['tiktok'] == "" ? $sc = NULL : $sc = mysqli_real_escape_string($con, $_POST['tiktok']);
    	$_POST['twit'] == "" ? $twit = NULL : $twit = mysqli_real_escape_string($con, $_POST['twit']);
    	$_POST['fb'] == "" ? $fb = NULL : $fb = mysqli_real_escape_string($con, $_POST['fb']);
    	$query = "INSERT INTO social VALUES ('$uid', '$ig', '$sc', '$twit', '$fb')";
    	$result = mysqli_query($con, $query);
    	if($result){
    		consoleLog("social inserted");
    		$count++;
    	} else {
    		consoleLog(mysqli_error($con));
    		$errors['db-error'] = "Something went wrong with social!";
    	}

        //career table
    	$status = mysqli_real_escape_string($con, $_POST['status']);
    	$job = mysqli_real_escape_string($con, $_POST['job']);
    	$_POST['college'] == "" ? $college = NULL : $college = mysqli_real_escape_string($con, $_POST['college']);
    	$entre = mysqli_real_escape_string($con, $_POST['entre']);        
    	$_POST['owns_biz'] == "" ? $owns_biz = NULL : $owns_biz = mysqli_real_escape_string($con, $_POST['owns_biz']);
    	$query = "INSERT INTO career VALUES ('$uid', '$status', '$job', '$college', '$entre', '$owns_biz')";
    	$result = mysqli_query($con, $query);
    	if($result){
    		consoleLog("career inserted");
    		$count++;
    	} else {
    		consoleLog(mysqli_error($con));
    		$errors['db-error'] = "Something went wrong with career!";
    	}

        //hobbies table
    	isset($_POST['streaming_movies_and_shows']) ? $streaming_movies_and_shows = mysqli_real_escape_string($con, $_POST['streaming_movies_and_shows']) : $streaming_movies_and_shows = 0;
    	isset($_POST['anime']) ? $anime = mysqli_real_escape_string($con, $_POST['anime']) : $anime = 0;
    	isset($_POST['stand_up_comedy']) ? $stand_up_comedy = mysqli_real_escape_string($con, $_POST['stand_up_comedy']) : $stand_up_comedy = 0;
    	isset($_POST['reading']) ? $reading = mysqli_real_escape_string($con, $_POST['reading']) : $reading = 0;
    	isset($_POST['writing']) ? $writing = mysqli_real_escape_string($con, $_POST['writing']) : $writing = 0;
    	isset($_POST['meditation']) ? $meditation = mysqli_real_escape_string($con, $_POST['meditation']) : $meditation = 0;
    	isset($_POST['music']) ? $music = mysqli_real_escape_string($con, $_POST['music']) : $music = 0;
    	isset($_POST['eating']) ? $eating = mysqli_real_escape_string($con, $_POST['eating']) : $eating = 0;
    	isset($_POST['dancing']) ? $dancing = mysqli_real_escape_string($con, $_POST['dancing']) : $dancing = 0;
    	isset($_POST['singing']) ? $singing = mysqli_real_escape_string($con, $_POST['singing']) : $singing = 0;
    	isset($_POST['baking']) ? $baking = mysqli_real_escape_string($con, $_POST['baking']) : $baking = 0;
    	isset($_POST['cooking']) ? $cooking = mysqli_real_escape_string($con, $_POST['cooking']) : $cooking = 0;
    	isset($_POST['gardening']) ? $gardening = mysqli_real_escape_string($con, $_POST['gardening']) : $gardening = 0;
    	isset($_POST['arts_and_crafts']) ? $arts_and_crafts = mysqli_real_escape_string($con, $_POST['arts_and_crafts']) : $arts_and_crafts = 0;
    	isset($_POST['painting']) ? $painting = mysqli_real_escape_string($con, $_POST['painting']) : $painting = 0;
    	isset($_POST['sketching']) ? $sketching = mysqli_real_escape_string($con, $_POST['sketching']) : $sketching = 0;
    	isset($_POST['fishing']) ? $fishing = mysqli_real_escape_string($con, $_POST['fishing']) : $fishing = 0;
    	isset($_POST['running']) ? $running = mysqli_real_escape_string($con, $_POST['running']) : $running = 0;
    	isset($_POST['walking']) ? $walking = mysqli_real_escape_string($con, $_POST['walking']) : $walking = 0;
    	isset($_POST['swimming']) ? $swimming = mysqli_real_escape_string($con, $_POST['swimming']) : $swimming = 0;
    	isset($_POST['working_out']) ? $working_out = mysqli_real_escape_string($con, $_POST['working_out']) : $working_out = 0;
    	isset($_POST['yoga']) ? $yoga = mysqli_real_escape_string($con, $_POST['yoga']) : $yoga = 0;
    	isset($_POST['bicycling']) ? $bicycling = mysqli_real_escape_string($con, $_POST['bicycling']) : $bicycling = 0;
    	isset($_POST['driving']) ? $driving = mysqli_real_escape_string($con, $_POST['driving']) : $driving = 0;
    	isset($_POST['riding']) ? $riding = mysqli_real_escape_string($con, $_POST['riding']) : $riding = 0;
    	isset($_POST['sports']) ? $sports = mysqli_real_escape_string($con, $_POST['sports']) : $sports = 0;
    	isset($_POST['video_games']) ? $video_games = mysqli_real_escape_string($con, $_POST['video_games']) : $video_games = 0;
    	isset($_POST['travelling']) ? $travelling = mysqli_real_escape_string($con, $_POST['travelling']) : $travelling = 0;
    	isset($_POST['hiking']) ? $hiking = mysqli_real_escape_string($con, $_POST['hiking']) : $hiking = 0;
    	isset($_POST['collecting']) ? $collecting = mysqli_real_escape_string($con, $_POST['collecting']) : $collecting = 0;
    	isset($_POST['volunteer_work']) ? $volunteer_work = mysqli_real_escape_string($con, $_POST['volunteer_work']) : $volunteer_work = 0;
    	isset($_POST['working']) ? $working = mysqli_real_escape_string($con, $_POST['working']) : $working = 0;
    	isset($_POST['audiobooks_and_podcasts']) ? $audiobooks_and_podcasts = mysqli_real_escape_string($con, $_POST['audiobooks_and_podcasts']) : $audiobooks_and_podcasts = 0;
    	isset($_POST['youtube']) ? $youtube = mysqli_real_escape_string($con, $_POST['youtube']) : $youtube = 0;
    	isset($_POST['social_media']) ? $social_media = mysqli_real_escape_string($con, $_POST['social_media']) : $social_media = 0;
    	isset($_POST['housework']) ? $housework = mysqli_real_escape_string($con, $_POST['housework']) : $housework = 0;
    	isset($_POST['shopping']) ? $shopping = mysqli_real_escape_string($con, $_POST['shopping']) : $shopping = 0;
    	isset($_POST['coding']) ? $coding = mysqli_real_escape_string($con, $_POST['coding']) : $coding = 0;
    	isset($_POST['hacking']) ? $hacking = mysqli_real_escape_string($con, $_POST['hacking']) : $hacking = 0;
    	isset($_POST['photoshop']) ? $photoshop = mysqli_real_escape_string($con, $_POST['photoshop']) : $photoshop = 0;
    	isset($_POST['video_editing']) ? $video_editing = mysqli_real_escape_string($con, $_POST['video_editing']) : $video_editing = 0;
    	isset($_POST['filmmaking']) ? $filmmaking = mysqli_real_escape_string($con, $_POST['filmmaking']) : $filmmaking = 0;
    	isset($_POST['science']) ? $science = mysqli_real_escape_string($con, $_POST['science']) : $science = 0;
    	isset($_POST['astronomy']) ? $astronomy = mysqli_real_escape_string($con, $_POST['astronomy']) : $astronomy = 0;
    	isset($_POST['astrology']) ? $astrology = mysqli_real_escape_string($con, $_POST['astrology']) : $astrology = 0;
    	$query = "INSERT INTO hobbies VALUES ('$uid', '$streaming_movies_and_shows', '$anime', '$stand_up_comedy', '$reading', '$writing', '$meditation', '$music', '$eating','$dancing','$singing','$baking','$cooking','$gardening','$arts_and_crafts','$painting','$sketching','$fishing','$running','$walking', '$swimming', '$working_out','$yoga','$bicycling','$driving','$riding','$sports','$video_games','$travelling','$hiking','$collecting','$volunteer_work','$working','$audiobooks_and_podcasts','$youtube','$social_media','$housework','$shopping','$coding','$hacking','$photoshop','$video_editing','$filmmaking','$science','$astrology','$astronomy')";
    	$result = mysqli_query($con, $query);
    	if($result){
    		consoleLog("hobbies inserted");
    		$count++;
    	} else {
    		consoleLog(mysqli_error($con));
    		$errors['db-error'] = "Something went wrong with hobbies!";
    	}

    	if($count == 4) {
    		$query = "UPDATE usertable SET profile_created='yes' WHERE uid=$uid";
    		$result = mysqli_query($con, $query);
    		if($result) {
    			consoleLog("Profile successfully created");
    			$query = "INSERT INTO notification(uid, type, content) VALUES ($uid, 'default', 'Welcome to SoulMate!'), ($uid, 'default', 'Browse through recommendations to find your perfect match!')";
    			$res = mysqli_query($con, $query);
    			if($res){
    				consoleLog("Notifs added successfully");
    			} else {
    				consoleLog("Notifs error");
    				consoleLog(mysqli_error($con));
    			}
    			$_SESSION['msg_header'] = "Welcome to SoulMate";
    			$_SESSION['msg'] = "Your profile has been created successfully";
    			header('location: home.php');
    		} else {
    			consoleLog("Error creating profile");
    			consoleLog(mysqli_error($con));
    		}
    	}
    }

    //profile edit - update profile
    if(isset($_POST['profile-edit-submit'])) {
    	$err_count = 0;
    	$email = $_SESSION['email'];
    	$uid = "";
    	$query = "SELECT uid from usertable where email = '$email'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		$fetch = mysqli_fetch_assoc($run_query);
    		$uid = $fetch['uid'];
    	}
        //update userprofile    	
    	$name 			= mysqli_real_escape_string($con, $_POST['name']);
    	$birth_date		= mysqli_real_escape_string($con, $_POST['tgl_lahir']);
    	$gender 		= mysqli_real_escape_string($con, $_POST['gender']);
    	$height 		= mysqli_real_escape_string($con, $_POST['height']);
    	$weight 		= mysqli_real_escape_string($con, $_POST['weight']);
    	$province_id	= mysqli_real_escape_string($con, $_POST['asal_provinsi']);
    	$regencies_id	= mysqli_real_escape_string($con, $_POST['asal_kota']);
    	$address 		= mysqli_real_escape_string($con, $_POST['address']);
    	$bio 			= mysqli_real_escape_string($con, $_POST['bio']);
    	$fakultas_id	= mysqli_real_escape_string($con, $_POST['fakultas']);
    	$jurusan_id		= mysqli_real_escape_string($con, $_POST['jurusan']);
    	$tahun_masuk	= mysqli_real_escape_string($con, $_POST['tahun_masuk']);
    	$organisasi		= mysqli_real_escape_string($con, $_POST['organisasi']);
    	
    	$profile_photo 	= ""; 
    	$target_dir 	= "public/user-profiles/";
    	$target_file 	= $target_dir . $_FILES["profile_photo"]["name"];
    	$imageFileType 	= strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        //validate file type, must be only image
    	if(!file_exists($_FILES['profile_photo']['tmp_name']) || !is_uploaded_file($_FILES['profile_photo']['tmp_name'])) {
    		$query = "UPDATE userprofile SET 
    		name='$name',
    		birth_date='$birth_date',
    		gender='$gender',
    		height='$height',
    		weight='$weight',
    		province_id='$province_id',
    		regencies_id='$regencies_id',
    		address='$address',
    		bio='$bio',
    		fakultas_id='$fakultas_id',
    		jurusan_id='$jurusan_id',
    		tahun_masuk='$tahun_masuk',
    		organisasi='$organisasi'
    		WHERE uid = '$uid'";
    	} else {
    		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"){
    			$errors['imageFileType'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    		}else {
    			move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/SoulMate/public/user-profiles/user-" . $uid . "-" . $_FILES["profile_photo"]["name"]);
    			$profile_photo = "./public/user-profiles/user-" . $uid . "-" . $_FILES["profile_photo"]["name"];
                //get old photo url
    			$query 	= "SELECT profile_photo FROM userprofile WHERE uid='$uid'";
    			$res 	= mysqli_query($con, $query);
    			$fetch 	= mysqli_fetch_assoc($run_query);
    			$old 	= $fetch['profile_photo'];
    			$url 	= $_SERVER["DOCUMENT_ROOT"]."/SoulMate" . substr($old, 1);
                // if($old != $profile_photo) unlink(realpath($url));

    			$query = "UPDATE userprofile SET 
    			profile_photo='$profile_photo',
    			name='$name',
    			birth_date='$birth_date',
    			gender='$gender',
    			height='$height',
    			weight='$weight',
    			province_id='$province_id',
    			regencies_id='$regencies_id',
    			address='$address',
    			bio='$bio',
    			fakultas_id='$fakultas_id',
    			jurusan_id='$jurusan_id',
    			tahun_masuk='$tahun_masuk',
    			organisasi='$organisasi'
    			WHERE uid = '$uid'";
    		}
    	}

    	$result = mysqli_query($con, $query);
    	if($result){
    		consoleLog("userprofile updated");
    	} else {
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}

        //update social
    	$ig 	= mysqli_real_escape_string($con, $_POST['ig']);
    	$tiktok	= mysqli_real_escape_string($con, $_POST['tiktok']);
    	$twit 	= mysqli_real_escape_string($con, $_POST['twit']);
    	$fb 	= mysqli_real_escape_string($con, $_POST['fb']);
    	$query 	= "UPDATE social SET 
    	ig='$ig',
    	tiktok='$tiktok',
    	twit='$twit',
    	fb='$fb' 
    	WHERE uid = '$uid'";
    	$result = mysqli_query($con, $query);
    	if($result){
    		consoleLog("social updated");
    	} else {
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}

    	if($err_count == 0) {
    		$_SESSION['msg_header'] = "Profile Update";
    		$_SESSION['msg'] = "Your profile has been updated successfully";
    		header('location: edit-profile.php');
    	}
    }

    //delete profile
    if(isset($_POST['delete-profile'])) { 
    	$err_count = 0;
    	$email = $_SESSION['email'];
    	$uid = "";
    	$unique_id = "";
    	$query = "SELECT uid, unique_id from usertable where email = '$email'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		$fetch = mysqli_fetch_assoc($run_query);
    		$uid = $fetch['uid'];
    		$unique_id = $fetch['unique_id'];
    	}
        //1 delete from usertable
    	$query = "DELETE FROM usertable WHERE uid = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from USERTABLE");
    	} else {
    		consoleLog("USERTABLE delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //2 delete from userprofile
    	$query = "DELETE FROM userprofile WHERE uid = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from USERPROFILE");
    	} else {
    		consoleLog("USERPROFILE delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //3 delete from social
    	$query = "DELETE FROM social WHERE uid = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from SOCIAL");
    	} else {
    		consoleLog("SOCIAL delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //4 delete from career
    	$query = "DELETE FROM career WHERE uid = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from CAREER");
    	} else {
    		consoleLog("CAREER delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //5 delete from hobbies
    	$query = "DELETE FROM hobbies WHERE uid = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from HOBBIES");
    	} else {
    		consoleLog("HOBBIES delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //6 delete from notifications
    	$query = "DELETE FROM notification WHERE uid = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from NOTIFICATION");
    	} else {
    		consoleLog("NOTIFICATION delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //6 delete from match
    	$query = "DELETE FROM `match` WHERE uid1 = '$uid' OR uid2 = '$uid'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from MATCH");
    	} else {
    		consoleLog("MATCH delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}
        //7 delete from messages
    	$query = "DELETE FROM `messages` WHERE incoming_msg_id = '$unique_id' OR outgoing_msg_id = '$unique_id'";
    	$run_query = mysqli_query($con, $query);
    	if($run_query) {
    		consoleLog("Deleted from MESSAGES");
    	} else {
    		consoleLog("MESSAGES delete error");
    		consoleLog(mysqli_error($con));
    		$err_count++;
    	}

    	if($err_count == 0) {
    		consoleLog("PROFILE DELETED");
    		session_start();
    		session_unset();
    		session_destroy();
    		header('location: signup-user.php');
    	}
    }

    //when user sees notifications set as seen
    if(isset($_POST['seen_by_user'])) {
    	$uid = $_POST['uid'];
    	$query = "UPDATE notification SET seen_by_user='yes' where uid='$uid'";
    	$res = mysqli_query($con, $query);
    	if($res) {
    		echo "Updated seen by user";
    	} else {
    		echo mysqli_error($con);
    	}        
    }

    //fetch all cards
    if(isset($_POST['get_cards'])) {
    	$uid 		= $_POST['uid1'];
    	$cards 		= Array();
    	$coordsList = "";
    	$noCards 	= true;
        //get gender of current user
    	$query 		= "SELECT gender FROM userprofile WHERE uid='$uid'";
    	$res 		= mysqli_query($con, $query);
    	$gender 	= mysqli_fetch_assoc($res)['gender'];
        //fetch all cards of opposite gender

    	$query 		= "SELECT t1.*,t2.name as nama_jurusan FROM userprofile t1 
    	LEFT JOIN jurusan t2 ON t2.id = t1.jurusan_id
    	WHERE t1.gender = '$gender' AND t1.uid != '$uid'";
    	$res 		= mysqli_query($con, $query);
    	while ($row = mysqli_fetch_assoc($res)) {
    		$showCard = true;
    		$uid2 	= $row['uid'];
    		$query 	= "SELECT * FROM `match` WHERE uid1 in ('$uid', '$uid2') AND uid2 in ('$uid', '$uid2')";
    		$user_res = mysqli_query($con, $query);

            //don't show user if already matched or if blocked
    		if(mysqli_num_rows($user_res) == 1) {
    			$fetch = mysqli_fetch_assoc($user_res);
    			if($fetch['status'] == 'match' || $fetch['status'] == 'blocked') $showCard = false;
    			if($fetch['first_liked_by'] == $uid) $showCard = false;
    		}

    		if($showCard) {
    			$noCards		= false;
    			$id 			= $row['uid'];
    			$name 			= ucwords($row['name']);
    			$tanggal_lahir 	= new DateTime($row["birth_date"]);
    			$sekarang 		= new DateTime("today");
    			$age 			= $sekarang->diff($tanggal_lahir)->y;
    			$gender			= $row['gender'];
    			$profile_photo	= $row['profile_photo'];
    			$jurusan		= $row['nama_jurusan'];
    			$bio 			= nl2br($row['bio']);
    			$card = Array(
    				"uid" => $id,
    				"name" => $name,
    				"age" => $age,
    				"gender" => $gender,
    				"profile_photo" => $profile_photo,
    				"jurusan" => $jurusan,
    				"bio" => $bio
    			);
    			array_push($cards, $card);
    		}
    	}
    	if(count($cards) == 0) $noCards = true;
    	array_push($cards, Array("noCards" => $noCards));
    	echo json_encode($cards);
    }

    //check if match, save in match table
    if(isset($_POST['data-uid'])) {
    	$uid1 = $_POST['uid1'];
    	$uid1_name = $_POST['uid1-name'];
    	$uid2_name = $_POST['uid2-name'];
    	$uid2 = $_POST['data-uid'];
        $choice = $_POST['choice']; //whether pending or block
        $jsonData = Array();
        $query = "SELECT * FROM `match` WHERE uid1 in ('$uid1', '$uid2') AND uid2 in ('$uid1', '$uid2')";
        $res = mysqli_query($con, $query);
        //not there in match => save choice in match
        if(mysqli_num_rows($res) == 0) {
        	$query = "INSERT INTO `match`(uid1, uid2, status, first_liked_by) VALUES ('$uid1', '$uid2', '$choice', '$uid1')";
        	$resp = mysqli_query($con, $query);
        	if($resp) $jsonData['instantMatch'] = false;
        	else echo "error creating new";
        }
        //status is pending => update to match 
        if(mysqli_num_rows($res) == 1) {
        	if(mysqli_fetch_assoc($res)['status'] == 'pending') {
        		$query = "UPDATE `match` SET status = 'match' WHERE uid1 in ('$uid1', '$uid2') AND uid2 in ('$uid1', '$uid2')";
        		$resp = mysqli_query($con, $query);
        		if($resp) $jsonData['instantMatch'] = true;
        		else echo "error updating old";

                //update notification for both
        		$content = "You have matched with " . $uid1_name . "!";
        		$query1 = "INSERT INTO notification(uid, type, content) VALUES ($uid2, 'match', '$content')";
        		$res1 = mysqli_query($con, $query1);
        		$content = "You have matched with " . $uid2_name . "!";
        		$query2 = "INSERT INTO notification(uid, type, content) VALUES ($uid1, 'match', '$content')";
        		$res2 = mysqli_query($con, $query2);

                //update toast info
        		$_SESSION['msg_header'] = "Match";
        		$_SESSION['msg'] = "Congratulations! You have matched with ";
        	}
        }
        // (IF BLOCKED/MATCH THEN CARD WON'T EVEN BE SHOWN)
        echo json_encode($jsonData);
    }

    //if curr user blocks a matched user
    if(isset($_POST['block-matched-user'])) {
    	$uid1 = $_POST['curr_uid'];
    	$uid2 = $_POST['uid_to_be_blocked'];

    	$query = "UPDATE `match` SET status='blocked', created_at=CURRENT_TIMESTAMP WHERE uid1 in ('$uid1', '$uid2') AND uid2 in ('$uid1', '$uid2')";
    	$res = mysqli_query($con, $query);
    	if($res) echo "Blocked user";
    	else echo "error";

    	header('location: home.php');
    }

    if(isset($_POST['show-profile'])) {
    	$uid 	= $_POST["uid"];
    	$query = "SELECT userprofile.*, 
    	social.*,
    	fakultas.name as nama_fakultas,
    	jurusan.name as nama_jurusan,
    	provinces.name as nama_provinsi,
    	regencies.name as nama_kota
    	FROM userprofile 
    	LEFT JOIN fakultas ON fakultas.id=userprofile.fakultas_id
    	LEFT JOIN jurusan ON jurusan.id=userprofile.jurusan_id
    	LEFT JOIN provinces ON provinces.id=userprofile.province_id
    	LEFT JOIN regencies ON regencies.id=userprofile.regencies_id
    	LEFT JOIN social ON social.uid=userprofile.uid
    	WHERE userprofile.uid = $uid";
    	$res = mysqli_query($con, $query);

    	echo json_encode(mysqli_fetch_assoc($res));
    }

    ?>
