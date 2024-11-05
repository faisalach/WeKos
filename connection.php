<?php 
if (strpos($_SERVER["DOCUMENT_ROOT"], "xampp") !== false) {
	$con = mysqli_connect('localhost', 'root', '', 'SoulMate');
}else{
	$con = mysqli_connect('localhost', 'dokp3944_wekos', 'xOI]BA]sIOnh', 'dokp3944_wekos');
}
?>