<?php
session_start();
$_SESSION = [];
session_destroy();


header("Location: /sys_Taller_Computo/public/api/login.php");
exit;

?>