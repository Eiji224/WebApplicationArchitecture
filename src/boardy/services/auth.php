<?php

const jwtSecret = 'student-secret-key';
const clientId = 'Ov23liWXx6Ve10vwHivU';
const clientSecret = 'client-secret';

function getClientId(): string
{
	return clientId;
}

function getClientSecret(): string
{
	return clientSecret;
}

function generateJwt(int $userId, string $userName): string
{
	$header = rtrim(strtr(base64_encode(json_encode(
		['alg' => 'HS256', 'typ' => 'JWT']
	)), '+/', '-_'), '=');

	$payload = rtrim(strtr(base64_encode(json_encode([
		'user_id' => $userId,
		'name' => $userName,
		'exp' => time() + 3600
	])), '+/', '-_'), '=');

	$signature = rtrim(strtr(base64_encode(
		hash_hmac('sha256', "$header.$payload", jwtSecret, true)
	), '+/', '-_'), '=');

	return "$header.$payload.$signature";
}