<?php
use Firebase\JWT\JWT;

function generateJWT($payload)
{
    return JWT::encode($payload, getenv('JWT_SECRET_KEY'), getenv('JWT_ALGORITHM'));
}

function validateJWT($jwt)
{
    try {
        $decoded = JWT::decode($jwt, getenv('JWT_SECRET_KEY'), [getenv('JWT_ALGORITHM')]);
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}