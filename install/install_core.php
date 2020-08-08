<?php

extract($_POST);

if ( $smtp_port == '' ) $smtp_port = 0;

$website_domain = rtrim($website_domain, '/');

$admin_email = 'admin@' . str_replace(array('http://', 'https://'), array('', '') ,$website_domain);

$website_domain = strpos($website_domain, 'http://') === 0 || strpos($website_domain, 'https://') === 0 ?
    $website_domain : 'http://' . $website_domain;

$scriptUrl = $website_domain.dirname(dirname($_SERVER['PHP_SELF'])) . "/";

$output = "<?php".PHP_EOL.PHP_EOL;

$output .= "//GENERAL SITE SETTINGS".PHP_EOL.PHP_EOL;
$output .= "define('WEBSITE_NAME', \"$website_name\");".PHP_EOL.PHP_EOL;
$output .= "define('WEBSITE_DOMAIN', \"$website_domain\");".PHP_EOL.PHP_EOL;
$output .= PHP_EOL;

$output .= "//DB CONF".PHP_EOL.PHP_EOL;
$output .= "define('DB_HOST', \"$db_host\"); ".PHP_EOL.PHP_EOL;
$output .= "define('DB_TYPE', \"mysql\"); ".PHP_EOL.PHP_EOL;
$output .= "define('DB_USER', \"$db_user\"); ".PHP_EOL.PHP_EOL;
$output .= "define('DB_PASS', \"$db_pass\"); ".PHP_EOL.PHP_EOL;
$output .= "define('DB_NAME', \"$db_name\"); ".PHP_EOL.PHP_EOL;
$output .= PHP_EOL;

$output .= "//SESSION CONFIG".PHP_EOL.PHP_EOL;
$output .= "define('SESSION_SECURE', $session_secure);   ".PHP_EOL.PHP_EOL;
$output .= "define('SESSION_HTTP_ONLY', $session_http_only);".PHP_EOL.PHP_EOL;
$output .= "define('SESSION_REGENERATE_ID', $session_regenerate_id);   ".PHP_EOL.PHP_EOL;
$output .= "define('SESSION_USE_ONLY_COOKIES', $session_use_only_cookies);".PHP_EOL.PHP_EOL;
$output .= PHP_EOL;

$output .= "//LOGIN CONFIG".PHP_EOL.PHP_EOL;
$output .= "define('LOGIN_MAX_LOGIN_ATTEMPTS', $login_max_login_attempts); ".PHP_EOL.PHP_EOL;
$output .= "define('LOGIN_FINGERPRINT', $login_fingerprint); ".PHP_EOL.PHP_EOL;
$output .= "define('SUCCESS_LOGIN_REDIRECT', \"$redirect_after_login\"); ".PHP_EOL.PHP_EOL;
$output .= PHP_EOL;

$output .= "//PASS CONFIG".PHP_EOL.PHP_EOL;
$output .= "define('PASSWORD_ENCRYPTION', \"$encryption\"); //available values: \"sha512\", \"bcrypt\"".PHP_EOL.PHP_EOL;
$output .= "define('PASSWORD_BCRYPT_COST', \"$bcrypt_cost\"); ".PHP_EOL.PHP_EOL;
$output .= "define('PASSWORD_SHA512_ITERATIONS', $sha512_iterations); ".PHP_EOL.PHP_EOL;
$output .= "define('PASSWORD_SALT', \"".  randomString(22)."\"); //22 characters to be appended on first 7 characters that will be generated using PASSWORD_ info above".PHP_EOL.PHP_EOL;
$output .= "define('PASSWORD_RESET_KEY_LIFE', $prk_life); ".PHP_EOL.PHP_EOL;
$output .= PHP_EOL;

$output .= "//REG CONFIG".PHP_EOL.PHP_EOL;
$output .= "define('MAIL_CONFIRMATION_REQUIRED', $mail_confirm_required); ".PHP_EOL.PHP_EOL;
$output .= "define('REGISTER_CONFIRM', \"".$scriptUrl."confirm.php\"); ".PHP_EOL.PHP_EOL;
$output .= "define('REGISTER_PASSWORD_RESET', \"".$scriptUrl."passwordreset.php\"); ".PHP_EOL.PHP_EOL;
$output .= PHP_EOL;

$file = '../include/config.php';
$handle = fopen($file, 'w');
fwrite($handle, $output);
fclose($handle);

include "../include/class/main.php";

$query = "CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(40) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `confirmkey` varchar(40) NOT NULL,
  `confirmed` enum('Y','N') NOT NULL default 'N',
  `p_resetkey` varchar(250) NOT NULL default '',
  `p_resetconfirmed` enum('Y','N') NOT NULL default 'N',
  `p_resettimestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `joindate` date NOT NULL,
  `power` int(4) NOT NULL default 1,
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `banned` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id_attempt` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `attemptnumber` int(11) NOT NULL DEFAULT '1',
  `date` date NOT NULL,
  PRIMARY KEY (`login_attempts`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `userpowers` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `userpowers` (`role_id`, `role`) VALUES
(1, 'user'),
(2, 'admin');

INSERT INTO `users` (`id`, `email`, `username`, `password`, `confirmkey`, 
                        `confirmed`, `p_resetkey`, `p_resetconfirmed`, 
                        `power`, `joindate`) 
VALUES (1,'$admin_email', 'admin','','', 'Y', '', 'N', 2, '".date("Y-m-d")."');";

$db->exec($query);

function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

$register = new Register();

$adminPass = hash("sha512", "admin");

$adminPass = $register->hashPassword($adminPass);

$db->update("users", array( "password" => $adminPass ), "`username` = 'admin'");

echo "successfully installed, delete this folder";
