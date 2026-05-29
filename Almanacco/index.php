<?php
declare(strict_types=1);

// Bootstrap dell'API: carica Flight, configurazione, modelli e rotte applicative.
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/flight/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/routes/model.php';
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/squadre.php';
require_once __DIR__ . '/routes/giocatori.php';
require_once __DIR__ . '/routes/contratti.php';
require_once __DIR__ . '/routes/partite.php';
require_once __DIR__ . '/routes/stagioni.php';
require_once __DIR__ . '/routes/campionati.php';
require_once __DIR__ . '/routes/classifica.php';

Flight::route('/*', function () {
    Flight::jsonHalt([
        'status' => 'ko',
        'message' => 'bad request',
    ], 400);
});

Flight::start();