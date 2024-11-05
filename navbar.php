<?php require_once "functions.php"; ?>
<?php 
$uid 	= pageForLogin();

// fetch notifs
extract(notifs($uid));

$_SESSION['uid1'] = $uid;
// fetch profile pic url
extract(profile_picture($uid));

// fetch matches
$matches = matches($uid);
?>

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


	<!-- NAVBAR -->
	<nav class="navbar navbar-light navbar-expand" >
		<a class="navbar-brand ml-md-5" href="index.php">
			<img src="./public/assets/logo.png" alt="logo" height="60">
		</a>   
		<div class="navbar-nav ml-auto mr-md-5 d-flex align-items-center">
			<div class="nav-item h5 mb-0 position-relative" style="font-size: 32px; cursor: pointer;" tabindex="50"  data-toggle="popover-matches" data-trigger="focus" data-placement="bottom" title="Your Matches">
				<i class="fas fa-fw fa-heart text-secondary"></i>
				<?php if (!empty($matches)): ?>
					<span class="position-absolute bg-danger text-white rounded" style="top: 0px;right: 0px;font-size: 12px;padding: 2px;">
						<?= count($matches) ?>
					</span>
				<?php endif ?>
			</div>
			<div class="nav-item h5 mb-0 ml-2 position-relative" style="font-size: 32px; cursor: pointer;" tabindex="50"  data-toggle="popover-notifs" data-trigger="focus" data-placement="bottom" title="Notifications">
				<i class="fas fa-fw fa-bell text-secondary"></i>
				<?php if (!empty($notifs_content_unseen)): ?>
					<span class="position-absolute bg-danger text-white rounded" style="top: 0px;right: 0px;font-size: 12px;padding: 2px;">
						<?= count($notifs_content_unseen) ?>
					</span>
				<?php endif ?>
			</div>
			<div class="nav-item ml-2" style="position: relative;"><img src="<?php echo $profile_photo; ?>" alt="profile pic" class="avatar" height="40" width="40" style="border-radius: 50%; cursor: pointer; object-fit: cover;" tabindex="50" data-toggle="popover-profile-icon" data-trigger="focus" data-placement="bottom" title="Hello, <?php echo $name; ?>!"></div>
		</div>
	</nav>


	<!-- POPOVERS -->
	<ul id="popover-content-profile-icon" class="list-group" style="display: none;">
		<span class="list-group-item btn btn-outline-success rounded"><a href="users.php">Message</a></span>
		<span class="list-group-item btn btn-outline-success rounded"><a href="edit-profile.php">Edit profile</a></span>
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