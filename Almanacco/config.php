<?php
declare(strict_types=1);

$host = 'localhost';
$dbName = 'almanacco_sportivo';
$user = 'almanacco';
$pass = 'almanacco123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}

$db = $pdo;
$APPKEY = (string) (getenv('ALMANACCO_JWT_KEY') ?: 'almanacco_jwt_key_2026_min_32_bytes_secret_value');

if (strlen($APPKEY) < 32) {
    throw new RuntimeException('ALMANACCO_JWT_KEY must be at least 32 characters long for HS256.');
}

foreach (glob(__DIR__ . '/Firebase/JWT/*.php') as $file) {
    require_once $file;
}

function get_JWT(): object
{
    global $APPKEY;

    $jwt = (string) Flight::request()->getHeader('Authorization');
    $jwt = str_replace('Bearer ', '', $jwt);

    if ($jwt === '') {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Token required',
        ], 401);
    }

    try {
        return \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($APPKEY, 'HS256'));
    } catch (\Firebase\JWT\ExpiredException $e) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'token expired, please relogin',
        ], 401);
    } catch (\Firebase\JWT\SignatureInvalidException $e) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'invalid token, please relogin',
        ], 401);
    } catch (\Firebase\JWT\BeforeValidException $e) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'not authorized',
        ], 401);
    } catch (\UnexpectedValueException $e) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'missing token, please relogin',
        ], 401);
    } catch (Exception $e) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'something went wrong',
        ], 401);
    }
}

function require_auth(): object
{
    return get_JWT();
}