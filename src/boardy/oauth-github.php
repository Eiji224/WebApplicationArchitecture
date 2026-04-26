<?php

session_set_cookie_params([
	'lifetime' => 86400,
	'path' => '/',
	'secure' => true,
	'httponly' => true,
	'samesite' => 'Lax'
]);
session_start();

require_once __DIR__ . '/services/auth.php';

$clientId = getClientId();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = http_build_query([
	'client_id' => $clientId,
	'redirect_uri' => 'https://boardy.eiji.ai-info.ru/oauth-callback.php',
	'scope' => 'read:user',
	'state' => $state,
]);

header("Location: https://github.com/login/oauth/authorize?" . $params);