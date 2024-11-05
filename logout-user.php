<?php
session_start();
session_unset();
session_destroy();
echo "<script>location.href = 'login-user.php';</script>";
exit;
?>
