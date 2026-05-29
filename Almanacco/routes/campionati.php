<?php
declare(strict_types=1);

// Rotte per leggere l'elenco dei campionati disponibili.
Flight::group('/campionati', function () {
    Flight::route('GET /', function () {
        global $pdo;
        $stm = $pdo->query('SELECT * FROM campionati ORDER BY id');
        $campionati = $stm->fetchAll();

        Flight::json([
            'status' => 'ok',
            'data' => [
                'campionati' => $campionati,
            ],
        ]);
    });
});
