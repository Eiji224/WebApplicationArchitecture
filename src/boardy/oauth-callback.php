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
require_once __DIR__ . '/db.php';

function getAccessToken(): string
{
	$clientId = getClientId();
	$clientSecret = getClientSecret();

	if (($_GET['state'] ?? '') !== ($_SESSION['oauth_state'] ?? '')) {
		var_dump($_SESSION['oauth_state']);
		var_dump($_GET['state'] ?? '');
		die('Invalid state');
	}

	$ch = curl_init('https://github.com/login/oauth/access_token');
	curl_setopt_array($ch, [
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query([
			'client_id' => $clientId,
			'client_secret' => $clientSecret,
			'code' => $_GET['code'],
		]),
		CURLOPT_HTTPHEADER => ['Accept: application/json'],
		CURLOPT_RETURNTRANSFER => true,
	]);

	$response = json_decode(curl_exec($ch), true);
	curl_close($ch);
	return $response['access_token'];
}

function getProfile(string $accessToken): array
{
	$ch = curl_init('https://api.github.com/user');
	curl_setopt_array($ch, [
		CURLOPT_HTTPHEADER => [
			"Authorization: Bearer $accessToken",
			'User-Agent: Boardy'
		],
		CURLOPT_RETURNTRANSFER => true,
	]);
	$profile = json_decode(curl_exec($ch), true);
	curl_close($ch);

	return $profile;
}

$accessToken = getAccessToken();
$profile = getProfile($accessToken);

$stmt = $pdo->prepare('SELECT id, name FROM users WHERE github_id = ?');
$stmt->execute([$profile['id']]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
	$stmt = $pdo->prepare('INSERT INTO users (name, github_id) VALUES (?, ?)');
	$stmt->execute([$profile['login'], $profile['id']]);
	$user = [
		'id' => $pdo->lastInsertId(),
		'name' => $profile['login'],
	];
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['is_github_login'] = true;

header('Location: /messages.php');
exit;