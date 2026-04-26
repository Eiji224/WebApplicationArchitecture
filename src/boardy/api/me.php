<?php

include_once __DIR__ . '/../services/auth.php';

session_set_cookie_params([
	'lifetime' => 86400,
	'path' => '/',
	'secure' => true,
	'httponly' => true,
	'samesite' => 'Lax'
]);
session_start();

if (empty($_SESSION['user_id'])) {
	http_response_code(401);
	echo json_encode(['error' => 'Not authenticated']);
	exit;
}

$jwt = generateJwt($_SESSION['user_id'], $_SESSION['user_name']);

header('Content-Type: application/json');
echo json_encode(['token' => $jwt]);