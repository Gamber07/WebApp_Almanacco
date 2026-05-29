<?php
declare(strict_types=1);

// Rotte CRUD per le squadre e le informazioni anagrafiche del club.
Flight::group('/squadre', function () {
    Flight::route('GET /', function () {
        $q = trim((string) (Flight::request()->query->q ?? ''));

        Flight::json([
            'status' => 'ok',
            'data' => [
                'squadre' => get_squadre($q),
            ],
        ]);
    });

    Flight::route('GET /@id', function (string $id) {
        $squadra = get_squadra((int) $id);

        if ($squadra === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Squadra not found',
            ], 404);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'squadra' => $squadra,
            ],
        ]);
    });

    Flight::route('POST /', function () {
        require_auth();

        $data = Flight::request()->data->getData();
        $annoCorrente = (int) date('Y');
        $idSquadraPadre = $data['id_squadra_padre'] ?? null;
        $idSquadraPadre = $idSquadraPadre === '' || $idSquadraPadre === null ? null : (int) $idSquadraPadre;

        $nome = trim((string) ($data['nome'] ?? ''));
        $citta = trim((string) ($data['citta'] ?? ''));
        $stadio = trim((string) ($data['stadio'] ?? ''));
        $annoFondazione = (int) ($data['anno_fondazione'] ?? 0);

        if ($nome === '' || $citta === '' || $stadio === '' || $annoFondazione < 0 || $annoFondazione > $annoCorrente) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati squadra non validi.',
            ], 422);
        }

        global $pdo;
        $sql = 'INSERT INTO squadre (nome, citta, anno_fondazione, stadio, id_squadra_padre)
                VALUES (:nome, :citta, :anno_fondazione, :stadio, :id_squadra_padre)';
        $stm = $pdo->prepare($sql);
        $ok = $stm->execute([
            ':nome' => $nome,
            ':citta' => $citta,
            ':anno_fondazione' => $annoFondazione,
            ':stadio' => $stadio,
            ':id_squadra_padre' => $idSquadraPadre,
        ]);

        if (!$ok) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore inserimento.',
            ], 500);
        }

        $id = (int) $pdo->lastInsertId();

        Flight::response()->status(201);
        Flight::json([
            'status' => 'ok',
            'data' => [
                'squadra' => get_squadra($id),
            ],
        ]);
    });

    Flight::route('PUT /@id', function (string $id) {
        require_auth();

        $squadra = get_squadra((int) $id);
        if ($squadra === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Squadra not found',
            ], 404);
        }

        $data = Flight::request()->data->getData();
        $annoCorrente = (int) date('Y');
        $nome = trim((string) ($data['nome'] ?? ''));
        $citta = trim((string) ($data['citta'] ?? ''));
        $stadio = trim((string) ($data['stadio'] ?? ''));
        $annoFondazione = (int) ($data['anno_fondazione'] ?? 0);

        if ($nome === '' || $citta === '' || $stadio === '' || $annoFondazione < 0 || $annoFondazione > $annoCorrente) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati squadra non validi.',
            ], 422);
        }

        $ok = update_squadra([
            ':id' => (int) $id,
            ':nome' => $nome,
            ':citta' => $citta,
            ':stadio' => $stadio,
            ':anno_fondazione' => $annoFondazione,
        ]);

        if (!$ok) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore durante la modifica.',
            ], 500);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'squadra' => get_squadra((int) $id),
            ],
        ]);
    });

    Flight::route('DELETE /@id', function (string $id) {
        require_auth();

        if (!delete_squadra((int) $id)) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore eliminazione (forse ha giocatori collegati?).',
            ], 500);
        }

        Flight::json([
            'status' => 'ok',
        ]);
    });
});