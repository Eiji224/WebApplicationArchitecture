<?php

session_set_cookie_params([
	'lifetime' => 86400,
	'path' => '/',
	'secure' => false,
	'httponly' => true,
	'samesite' => 'Lax'
]);
session_start();
session_destroy();

setcookie('PHPSESSID', '', time() - 3600);

header('Location: /index.php');
exit;