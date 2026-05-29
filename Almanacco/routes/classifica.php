<?php
declare(strict_types=1);

// Rotte dedicate alla classifica e ai filtri stagione/campionato.
Flight::route('GET /stagioni', function () {
    Flight::json([
        'status' => 'ok',
        'data' => [
            'stagioni' => get_stagioni(),
        ],
    ]);
});

Flight::route('GET /campionati', function () {
    Flight::json([
        'status' => 'ok',
        'data' => [
            'campionati' => get_campionati(),
        ],
    ]);
});

Flight::route('GET /classifica', function () {
    $stagioni = get_stagioni();
    $campionati = get_campionati();

    $idStagione = (int) (Flight::request()->query->id_stagione ?? ($stagioni[0]['id'] ?? 0));
    $idCampionato = (int) (Flight::request()->query->id_campionato ?? ($campionati[0]['id'] ?? 0));

    if ($idStagione <= 0 || $idCampionato <= 0) {
        Flight::jsonHalt([
            'status' => 'ko',
            'message' => 'Nessuna stagione o campionato disponibile.',
        ], 404);
    }

    Flight::json([
        'status' => 'ok',
        'data' => [
            'filters' => [
                'id_stagione' => $idStagione,
                'id_campionato' => $idCampionato,
            ],
            'stagioni' => $stagioni,
            'campionati' => $campionati,
            'classifica' => get_classifica($idStagione, $idCampionato),
        ],
    ]);
});