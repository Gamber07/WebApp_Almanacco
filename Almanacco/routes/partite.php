<?php
declare(strict_types=1);

// Rotte CRUD per le partite e i controlli di coerenza sui dati inseriti.
Flight::group('/partite', function () {
    Flight::route('GET /', function () {
        $q = trim((string) (Flight::request()->query->q ?? ''));
        Flight::json([
            'status' => 'ok',
            'data' => [
                'partite' => get_partite($q),
            ],
        ]);
    });

    Flight::route('GET /@id', function (string $id) {
        $partita = get_partita((int) $id);

        if ($partita === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Partita not found',
            ], 404);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'partita' => $partita,
            ],
        ]);
    });

    Flight::route('POST /', function () {
        require_auth();

        $data = Flight::request()->data->getData();
        $idStagione = (int) ($data['id_stagione'] ?? 0);
        $idCampionato = (int) ($data['id_campionato'] ?? 0);
        $idCasa = (int) ($data['id_casa'] ?? 0);
        $idTrasferta = (int) ($data['id_trasferta'] ?? 0);
        $golCasa = (int) ($data['gol_casa'] ?? 0);
        $golTrasferta = (int) ($data['gol_trasferta'] ?? 0);
        $dataPartita = (string) ($data['data'] ?? '');
        $sede = trim((string) ($data['sede'] ?? ''));

        if ($idStagione <= 0 || $idCampionato <= 0 || $idCasa <= 0 || $idTrasferta <= 0 || $dataPartita === '' || $sede === '') {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati partita non validi.',
            ], 422);
        }

        if ($idCasa === $idTrasferta) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'La squadra di casa e trasferta devono essere diverse.',
            ], 422);
        }

        if ($golCasa < 0 || $golTrasferta < 0) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'I gol non possono essere negativi.',
            ], 422);
        }

        global $pdo;
        $sql = 'INSERT INTO partite (id_stagione, id_campionato, id_squadra_casa, id_squadra_trasferta, gol_casa, gol_trasferta, data_partita, sede)
                VALUES (:id_stagione, :id_campionato, :id_casa, :id_trasferta, :gol_casa, :gol_trasferta, :data, :sede)';
        $ok = $pdo->prepare($sql)->execute([
            ':id_stagione' => $idStagione,
            ':id_campionato' => $idCampionato,
            ':id_casa' => $idCasa,
            ':id_trasferta' => $idTrasferta,
            ':gol_casa' => $golCasa,
            ':gol_trasferta' => $golTrasferta,
            ':data' => $dataPartita,
            ':sede' => $sede,
        ]);

        if (!$ok) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore nella registrazione.',
            ], 500);
        }

        $id = (int) $pdo->lastInsertId();
        Flight::response()->status(201);
        Flight::json([
            'status' => 'ok',
            'data' => [
                'partita' => get_partita($id),
            ],
        ]);
    });

    Flight::route('PUT /@id', function (string $id) {
        require_auth();

        $partita = get_partita((int) $id);
        if ($partita === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Partita not found',
            ], 404);
        }

        $data = Flight::request()->data->getData();
        $idStagione = (int) ($data['id_stagione'] ?? 0);
        $idCampionato = (int) ($data['id_campionato'] ?? 0);
        $idCasa = (int) ($data['id_casa'] ?? 0);
        $idTrasferta = (int) ($data['id_trasferta'] ?? 0);
        $golCasa = (int) ($data['gol_casa'] ?? 0);
        $golTrasferta = (int) ($data['gol_trasferta'] ?? 0);
        $dataPartita = (string) ($data['data'] ?? '');
        $sede = trim((string) ($data['sede'] ?? ''));

        if ($idStagione <= 0 || $idCampionato <= 0 || $idCasa <= 0 || $idTrasferta <= 0 || $dataPartita === '' || $sede === '') {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati partita non validi.',
            ], 422);
        }

        if ($idCasa === $idTrasferta) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'La squadra di casa e trasferta devono essere diverse.',
            ], 422);
        }

        if ($golCasa < 0 || $golTrasferta < 0) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'I gol non possono essere negativi.',
            ], 422);
        }

        $ok = update_partita([
            ':id' => (int) $id,
            ':id_stagione' => $idStagione,
            ':id_campionato' => $idCampionato,
            ':id_casa' => $idCasa,
            ':id_trasferta' => $idTrasferta,
            ':gol_casa' => $golCasa,
            ':gol_trasferta' => $golTrasferta,
            ':data' => $dataPartita,
            ':sede' => $sede,
        ]);

        if (!$ok) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore durante la modifica della partita.',
            ], 500);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'partita' => get_partita((int) $id),
            ],
        ]);
    });

    Flight::route('DELETE /@id', function (string $id) {
        require_auth();

        if (!delete_partita((int) $id)) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore eliminazione.',
            ], 500);
        }

        Flight::json([
            'status' => 'ok',
        ]);
    });
});