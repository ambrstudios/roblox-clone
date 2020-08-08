<?php
include '../include/class/main.php';
session::destroySession();
header('Location: ../login.php');
?>
