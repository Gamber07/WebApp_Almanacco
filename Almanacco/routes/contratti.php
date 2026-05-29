<?php
declare(strict_types=1);

// Rotte CRUD per i contratti e gli ingaggi dei giocatori.
Flight::group('/contratti', function () {
    Flight::route('GET /', function () {
        Flight::json([
            'status' => 'ok',
            'data' => [
                'contratti' => get_contratti(),
            ],
        ]);
    });

    Flight::route('GET /@id', function (string $id) {
        $contratto = get_contratto((int) $id);

        if ($contratto === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Contratto not found',
            ], 404);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'contratto' => $contratto,
            ],
        ]);
    });

    Flight::route('POST /', function () {
        require_auth();

        $data = Flight::request()->data->getData();
        $idGiocatore = (int) ($data['id_giocatore'] ?? 0);
        $idSquadra = (int) ($data['id_squadra'] ?? 0);
        $dataInizio = (string) ($data['data_inizio'] ?? '');
        $scadenza = (string) ($data['scadenza'] ?? '');
        $numeroMaglia = (int) ($data['numero_maglia'] ?? 0);
        $tipoContratto = trim((string) ($data['tipo_contratto'] ?? ''));

        if ($idGiocatore <= 0 || $idSquadra <= 0 || $dataInizio === '' || $scadenza === '' || $numeroMaglia <= 0 || $tipoContratto === '') {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati contratto non validi.',
            ], 422);
        }

        global $pdo;
        $sql = 'INSERT INTO contratti (id_giocatore, id_squadra, data_inizio, scadenza, numero_maglia, tipo_contratto)
                VALUES (:id_giocatore, :id_squadra, :data_inizio, :scadenza, :numero_maglia, :tipo_contratto)';
        $ok = $pdo->prepare($sql)->execute([
            ':id_giocatore' => $idGiocatore,
            ':id_squadra' => $idSquadra,
            ':data_inizio' => $dataInizio,
            ':scadenza' => $scadenza,
            ':numero_maglia' => $numeroMaglia,
            ':tipo_contratto' => $tipoContratto,
        ]);

        if (!$ok) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore durante la registrazione del contratto.',
            ], 500);
        }

        $id = (int) $pdo->lastInsertId();
        Flight::response()->status(201);
        Flight::json([
            'status' => 'ok',
            'data' => [
                'contratto' => get_contratto($id),
            ],
        ]);
    });

    Flight::route('PUT /@id', function (string $id) {
        require_auth();

        $contratto = get_contratto((int) $id);
        if ($contratto === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Contratto not found',
            ], 404);
        }

        $data = Flight::request()->data->getData();
        $idGiocatore = (int) ($data['id_giocatore'] ?? 0);
        $idSquadra = (int) ($data['id_squadra'] ?? 0);
        $dataInizio = (string) ($data['data_inizio'] ?? '');
        $scadenza = (string) ($data['scadenza'] ?? '');
        $numeroMaglia = (int) ($data['numero_maglia'] ?? 0);
        $tipoContratto = trim((string) ($data['tipo_contratto'] ?? ''));

        if ($idGiocatore <= 0 || $idSquadra <= 0 || $dataInizio === '' || $scadenza === '' || $numeroMaglia <= 0 || $tipoContratto === '') {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati contratto non validi.',
            ], 422);
        }

        $ok = update_contratto([
            ':id' => (int) $id,
            ':id_giocatore' => $idGiocatore,
            ':id_squadra' => $idSquadra,
            ':data_inizio' => $dataInizio,
            ':scadenza' => $scadenza,
            ':numero_maglia' => $numeroMaglia,
            ':tipo_contratto' => $tipoContratto,
        ]);

        if (!$ok) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore durante la modifica del contratto.',
            ], 500);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'contratto' => get_contratto((int) $id),
            ],
        ]);
    });

    Flight::route('DELETE /@id', function (string $id) {
        require_auth();

        if (!delete_contratto((int) $id)) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore durante l\'eliminazione.',
            ], 500);
        }

        Flight::json([
            'status' => 'ok',
        ]);
    });
});