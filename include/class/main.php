<?php
include_once 'config.php';
include_once 'session.php';
include_once 'FORMValidator.php';
include_once 'adminPower.php';
include_once 'DB.php';
include_once 'login.class.php';
include_once 'register.class.php';
include_once 'user.class.php';
$db = database::getInstance();

session::startSession();

$login    = new login();
$register = new register();
?>
