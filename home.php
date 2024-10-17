<?php require_once "controllerUserData.php"; ?>

<?php 
$email = $_SESSION['email'];
$password = $_SESSION['password'];
$uid = "";

if (empty($email) || empty($password)) {
	header('Location: login-user.php');
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
			header('Location: reset-code.php');
		}
	}else{
		header('Location: user-otp.php');
	}
}
// fetch notifs
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

$_SESSION['uid1'] = $uid;
// fetch profile pic url
$query = "SELECT profile_photo, latitude, longitude, name FROM userprofile WHERE uid='$uid'";
$res = mysqli_query($con, $query);
$fetch = mysqli_fetch_assoc($res);
$profile_photo = $fetch['profile_photo'];
$name = ucfirst(explode(" ", $fetch['name'])[0]);
$latitude = $fetch['latitude'];
$longitude = $fetch['longitude'];

// fetch matches
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

?>

<?php include_once 'header.php'; ?>

<body>
	<link rel="stylesheet" href="./css/style-recommendation.css">
	<!-- TOAST -->
	<?php if(isset($_SESSION['msg']) && $_SESSION['msg'] != "") { ?>
		<div style="position: relative;">
			<div style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); z-index: 2;" >
				<div class="toast" style="min-width: 250px;">
					<div class="toast-header" style="background: #f5f5f5;">
						<strong class="mr-auto" style="font-size: 18px;"><?php echo $_SESSION['msg_header']; unset($_SESSION['msg_header']); ?></strong>
						<button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
							<span >&times;</span>
						</button>
					</div>
					<div class="toast-body">
						<?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
					</div>
				</div> 
			</div>
		</div>
	<?php } ?>  


	<!-- HIDDEN INPUTS  -->
	<input type="text" class="uid1-name" hidden value="<?php echo $name; ?>">
	<input type="text" class="uid" hidden value="<?php echo $_SESSION['uid1']; ?>">
	<input type="text" class="latitude" hidden value="<?php echo $latitude; ?>">
	<input type="text" class="longitude" hidden value="<?php echo $longitude; ?>">


	<!-- REDIRECT TO MATCH FORM -->
	<form action="match.php" method="POST" id="match-redirect-form" hidden>
		<input type="text" id="match-redirect-uid1" name="match-redirect-uid1" value="<?php echo $uid; ?>">
		<input type="text" id="match-redirect-uid2" name="match-redirect-uid2">
	</form>


	<!-- NAVBAR -->
	<nav class="navbar navbar-light navbar-expand" >
		<a class="navbar-brand ml-md-5" href="home.php">
			<img src="./public/assets/SoulMate (3).png" alt="logo" height="60">
		</a>   
		<div class="navbar-nav ml-auto mr-md-5 d-flex align-items-center">
			<div class="nav-item h5 mb-0 pr-3" style="font-size: 32px; cursor: pointer;" >
				<a href="users.php" style="color: #babec1">
					<i class="fas fa-fw fa-envelope"></i>
				</a>
			</div>
			<div class="nav-item h5 mb-0 position-relative" style="font-size: 32px; cursor: pointer;" tabindex="50"  data-toggle="popover-matches" data-trigger="focus" data-placement="bottom" title="Your Matches">
				<i class="fas fa-fw fa-heart"></i>
				<?php if (!empty($matches)): ?>
					<span class="position-absolute bg-danger text-white rounded" style="top: 0px;right: 0px;font-size: 12px;padding: 2px;">
						<?= count($matches) ?>
					</span>
				<?php endif ?>
			</div>
			<div class="nav-item ml-4" style="position: relative;"><img src="<?php echo $profile_photo; ?>" alt="profile pic" class="avatar" height="60" width="60" style="border-radius: 50%; cursor: pointer; object-fit: cover;" tabindex="50" data-toggle="popover-profile-icon" data-trigger="focus" data-placement="bottom" title="Hello, <?php echo $name; ?>!">

				<?php if(count($notifs_content_unseen) == 0) {?>
					<span class="badge badge-light" style="position: absolute !important; right:2px; cursor: pointer;" tabindex="50" data-toggle="popover-notifs" data-trigger="focus" data-placement="bottom" title="Notifications"><span id="span-num">0</span></span>    
				<?php } else { ?>
					<span class="badge badge-danger" style="position: absolute !important; right:2px; cursor: pointer;" tabindex="50" data-toggle="popover-notifs" data-trigger="focus" data-placement="bottom" title="Notifications"><span id="span-num"><?php echo count($notifs_content_unseen) ?></span></span>   
				<?php } ?>  
			</div>
		</div>
	</nav>


	<!-- CARDS -->
	<div class="tinder" id="tinder-container">
		<div class="tinder--status">
			<i class="fas fa-times"></i>
			<i class="fas fa-check"></i>
		</div>
		<div class="tinder--cards"></div>
	</div>

	<div class="cards-over d-flex align-items-center justify-content-center" id="cards-over-container">
		<div class="jumbotron">
			<h1 class="display-4">Sorry!</h1>
			<p class="lead">You've seen all potential matches in your search</p>
			<hr class="my-4">
			<p>Try again with a different combination to see if there are more</p>
			<p class="lead">
				<a class="btn btn-primary btn-lg" href="#" role="button">Search <i class="fas fa-search"></i></a>
			</p>
		</div>
	</div>


	<!-- POPOVERS -->
	<ul id="popover-content-profile-icon" class="list-group" style="display: none;">
		<span class="list-group-item btn btn-outline-success rounded"><a href="edit-profile.php">Edit profile</a></span>
		<div class="dropdown-divider"></div>
		<span class="list-group-item btn btn-outline-danger rounded"><a href="php/logout.php?logout_id=<?php echo $_SESSION['unique_id']; ?>" class="">Logout</a></span>
	</ul>

	<ul id="popover-content-matches" class="list-group" style="display: none;">
		<?php if(count($matches) == 0) { ?>
			<li class="list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 rounded disabled"><b>Sorry ☹</b><p class="font-weight-light">No matches yet.....</p></li>
		<?php } else { ?>  
			<?php for($i = 0; $i < count($matches); $i++) { ?>
				<li class="match-li list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 <?php if($i == 0) echo 'rounded-top'; elseif($i == count($matches) - 1) echo 'rounded-bottom'; elseif($i == 0 && $i == count($matches) - 1) echo "rounded"; ?>" style="cursor: pointer;"><b class="match-li-b"><?php echo $matches[$i]['name']; ?>, <?php echo $matches[$i]['age']; ?></b><p class="match-li-p font-weight-light">Matched <?php echo $matches[$i]['date']; ?></p><span class="d-none"><?php echo $matches[$i]['id']; ?></span></li>
			<?php } ?>  
		<?php } ?>  
	</ul>

	<ul id="popover-content-notifs" class="list-group" style="display: none;">
		<?php if(count($notifs_content_unseen) != 0) { ?>
			<?php for($i = 0; $i < count($notifs_content_unseen); $i++) { ?>  
				<li class="list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 <?php if($i == 1) echo "rounded-top" ?>"><p class="notif-text mb-0"><?php echo $notifs_content_unseen[$i] ?></p><p class="font-weight-light notif-date"><?php echo $notifs_date_unseen[$i] ?></p></li>
			<?php } ?>
			<div class="dropdown-divider"></div>
		<?php } ?>
		<?php if(count($notifs_content_seen) != 0) {  ?>
			<?php for($i = 0; $i < count($notifs_content_seen); $i++) { ?>  
				<li class="list-group-item btn btn-light text-left d-flex flex-column pl-2 pb-0 mb-2 notif-old <?php if($i == count($notifs_content_seen) - 1) echo "rounded-bottom" ?>"><p class="notif-text mb-0"><?php echo $notifs_content_seen[$i] ?></b><p class="font-weight-light notif-date"><?php echo $notifs_date_seen[$i] ?></p></li>
			<?php } ?>
		<?php } ?>  
	</ul>


	<!-- SCRIPTS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script>
		// to activate profile icon popover
		$(function() {
			$('[data-toggle="popover-profile-icon"]').popover({
				html: true,
				content: function() {
					return $('#popover-content-profile-icon').html();
				}
			});
		});

		// to activate matches popover
		$(function() {
			$('[data-toggle="popover-matches"]').popover({
				html: true,
				content: function() {
					return $('#popover-content-matches').html();
				}
			});
		});

		// to activate notifs popover
		$(function() {
			$('[data-toggle="popover-notifs"]').popover({
				html: true,
				content: function() {
					return $('#popover-content-notifs').html();
				}
			});
		});

		// activate toasts
		$(document).ready(function(){
			$(".toast").toast({
				autohide: false
			});
		});

		$(document).ready(function(){
			$('.toast').toast('show');
		});
	</script>
	<script src="./js/home.js"></script>
	<script src="https://hammerjs.github.io/dist/hammer.js"></script>
	<script src="./js/cards.js"></script>

</body>
</html>