<?php

function pageForLogin(){
	global $con;
	$email = $_SESSION['email'];
	$password = $_SESSION['password'];
	$uid = "";

	if (empty($email) || empty($password)) {
		echo "<script>location.href = 'login-user.php';</script>";
		exit;
	}

	$sql = "SELECT * FROM usertable WHERE email = '$email'";
	$run_Sql = mysqli_query($con, $sql);
	if($run_Sql){
		$fetch_info = mysqli_fetch_assoc($run_Sql);
		$uid = $fetch_info['uid'];
		$status = $fetch_info['status'];
		$code = $fetch_info['code'];
		if($status == "verified"){
			if($code != 0){
				echo "<script>location.href = 'reset-code.php';</script>";
				exit;
			}
		}else{
			echo "<script>location.href = 'user-otp.php';</script>";
			exit;
		}
	}

	return $uid;
}
function matches($uid)
{
	global $con;
	$matches = Array();
	$query = "SELECT uid1, uid2, status, DATE_FORMAT(created_at, '%d %b, %Y') as matched_at FROM `match` WHERE (uid1 = '$uid' OR uid2 = '$uid') AND status = 'match'";
	$res = mysqli_query($con, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$uid == $row['uid1'] ? $uid2 = $row['uid2'] : $uid2 = $row['uid1'];
		// fetch fname, age of other partner
		$query_inner = "SELECT name, age FROM userprofile WHERE uid='$uid2'";
		$res_inner = mysqli_query($con, $query_inner);
		$fetch_inner = mysqli_fetch_assoc($res_inner);
		$name_inner = ucfirst(explode(" ", $fetch_inner['name'])[0]);
		$age = $fetch_inner['age'];
		$date = dateFormatter($row['matched_at']);
		array_push($matches, Array("name" => $name_inner, "age" => $age, "date" => $date, "id" => $uid2));
	}

	return $matches;
}
function dateFormatter($date) {
	$today = date("d M, Y");
	if($today == $date) return "Today";
	$arr = explode(",", $date);
	$year = $arr[1];
	$arr = explode(" ", $arr[0]);
	$day = intval($arr[0]);
	$month = $arr[1];
	$diff = intval(substr($today, 0, 2)) - $day;
	if($diff == 1) return "Yesterday";
	else return $date;
}

function notifs($uid)
{
	global $con;
	$notifs_content_seen = Array();
	$notifs_date_seen = Array();
	$notifs_content_unseen = Array();
	$notifs_date_unseen = Array();
	$sql = "SELECT type, content, DATE_FORMAT(created_at, '%d %b, %Y') as created_at, seen_by_user FROM notification WHERE uid='$uid' ORDER BY nid DESC";
	$run_Sql = mysqli_query($con, $sql);
	while ($row = mysqli_fetch_assoc($run_Sql)) {
		if($row['seen_by_user'] == 'yes') {
			array_push($notifs_content_seen, $row['content']);
			array_push($notifs_date_seen, dateFormatter($row['created_at']));
		} else {
			array_push($notifs_content_unseen, $row['content']);
			array_push($notifs_date_unseen, dateFormatter($row['created_at']));      
		}
	}

	return [
		"notifs_content_seen" => $notifs_content_seen,
		"notifs_date_seen" => $notifs_date_seen,
		"notifs_content_unseen" => $notifs_content_unseen,
		"notifs_date_unseen" => $notifs_date_unseen,
	];
}
function profile_picture($uid)
{
	global $con;
	$query = "SELECT profile_photo, name FROM userprofile WHERE uid='$uid'";
	$res = mysqli_query($con, $query);
	$fetch = mysqli_fetch_assoc($res);
	$profile_photo = !empty($fetch['profile_photo']) ? $fetch['profile_photo'] : "./profile-icon-png-910.png";
	$name = ucfirst(explode(" ", $fetch['name'])[0]);



	return [
		"profile_photo" => $profile_photo,
		"name" => $name,
		"latitude" => "0",
		"longitude" => "0",
	];
}