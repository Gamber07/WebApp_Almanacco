<?php
declare(strict_types=1);

// Rotte di autenticazione: login, rinnovo sessione e registrazione utente.
Flight::route('POST /auth', function () {
    global $APPKEY;
    $username = trim((string) (Flight::request()->data->username ?? ''));
    $password = (string) (Flight::request()->data->password ?? '');

    $user = get_user_by_credentials($username, $password);
    if ($user === null) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Credenziali errate!',
        ], 401);
    }

    $payload = [
        'iss' => 'localhost',
        'iat' => time(),
        'exp' => time() + 3600,
        'data' => [
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'ruolo' => ($user['username'] === 'admin') ? 'admin' : ($user['ruolo'] ?? 'utente'),
            ],
        ],
    ];

    $jwt = \Firebase\JWT\JWT::encode($payload, $APPKEY, 'HS256');

    Flight::json([
        'status' => 'ok',
        'token' => $jwt,
        'user' => $payload['data']['user'],
    ]);
});

Flight::route('GET /me', function () {
    global $APPKEY;
    $payload = get_JWT();
    $payloadArray = json_decode(json_encode($payload), true);
    $payloadArray['iat'] = time();
    $payloadArray['exp'] = time() + 3600;

    $jwt = \Firebase\JWT\JWT::encode($payloadArray, $APPKEY, 'HS256');

    Flight::json([
        'status' => 'ok',
        'message' => 'session renewed',
        'data' => $payloadArray['data'],
        'token' => $jwt,
        'expires' => $payloadArray['exp'],
    ]);
});

Flight::route('POST /registrazione', function () {
    global $APPKEY, $pdo;
    $username = trim((string) (Flight::request()->data->username ?? ''));
    $password = (string) (Flight::request()->data->password ?? '');
    $password_confirm = (string) (Flight::request()->data->password_confirm ?? '');

    if ($username === '' || strlen($username) < 3) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Username deve essere almeno 3 caratteri.',
        ], 422);
    }

    if ($password === '' || strlen($password) < 6) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Password deve essere almeno 6 caratteri.',
        ], 422);
    }

    if ($password !== $password_confirm) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Le password non coincidono.',
        ], 422);
    }

    $stm = $pdo->prepare('SELECT id FROM utenti WHERE username = :username');
    $stm->execute([':username' => $username]);
    if ($stm->fetch()) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Username già registrato.',
        ], 409);
    }

    // Table `utenti` may not have `ruolo` column (legacy schema). Insert without ruolo.
    $sql = 'INSERT INTO utenti (username, password) VALUES (:username, :password)';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ok = $pdo->prepare($sql)->execute([
        ':username' => $username,
        ':password' => $hash,
    ]);

    if (!$ok) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Errore durante la registrazione.',
        ], 500);
    }

    Flight::response()->status(201);
    Flight::json([
        'status' => 'ok',
        'message' => 'Registrazione completata. Puoi ora accedere.',
    ]);
});