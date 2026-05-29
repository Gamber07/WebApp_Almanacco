<?php
declare(strict_types=1);

// Rotte CRUD per le stagioni sportive del campionato.
Flight::group('/stagioni', function () {
    Flight::route('GET /', function () {
        Flight::json([
            'status' => 'ok',
            'data' => [
                'stagioni' => get_stagioni(),
            ],
        ]);
    });

    Flight::route('GET /@id', function (string $id) {
        $stagione = get_stagione((int) $id);

        if ($stagione === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Stagione not found',
            ], 404);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'stagione' => $stagione,
            ],
        ]);
    });

    Flight::route('POST /', function () {
        require_auth();

        $data = Flight::request()->data->getData();
        $nome_stagione = trim((string) ($data['nome_stagione'] ?? ''));
        $anno_inizio = (int) ($data['anno_inizio'] ?? 0);
        $anno_fine = (int) ($data['anno_fine'] ?? 0);

        if ($nome_stagione === '' || $anno_inizio <= 0 || $anno_fine <= 0 || $anno_fine < $anno_inizio) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati stagione non validi.',
            ], 422);
        }

        global $pdo;
        $sql = 'INSERT INTO stagioni (nome_stagione, anno_inizio, anno_fine)
                VALUES (:nome_stagione, :anno_inizio, :anno_fine)';
        $ok = $pdo->prepare($sql)->execute([
            ':nome_stagione' => $nome_stagione,
            ':anno_inizio' => $anno_inizio,
            ':anno_fine' => $anno_fine,
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
                'stagione' => get_stagione($id),
            ],
        ]);
    });

    Flight::route('PUT /@id', function (string $id) {
        require_auth();

        $stagione = get_stagione((int) $id);
        if ($stagione === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Stagione not found',
            ], 404);
        }

        $data = Flight::request()->data->getData();
        $nome_stagione = trim((string) ($data['nome_stagione'] ?? ''));
        $anno_inizio = (int) ($data['anno_inizio'] ?? 0);
        $anno_fine = (int) ($data['anno_fine'] ?? 0);

        if ($nome_stagione === '' || $anno_inizio <= 0 || $anno_fine <= 0 || $anno_fine < $anno_inizio) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati stagione non validi.',
            ], 422);
        }

        $ok = update_stagione([
            ':id' => (int) $id,
            ':nome_stagione' => $nome_stagione,
            ':anno_inizio' => $anno_inizio,
            ':anno_fine' => $anno_fine,
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
                'stagione' => get_stagione((int) $id),
            ],
        ]);
    });

    Flight::route('DELETE /@id', function (string $id) {
        require_auth();

        if (!delete_stagione((int) $id)) {
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
