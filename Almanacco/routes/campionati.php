<?php
declare(strict_types=1);

// Rotte CRUD per i campionati.
Flight::group('/campionati', function () {
    Flight::route('GET /', function () {
        Flight::json([
            'status' => 'ok',
            'data' => [
                'campionati' => get_campionati(),
            ],
        ]);
    });

    Flight::route('GET /@id', function (string $id) {
        $campionato = get_campionato((int) $id);

        if ($campionato === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Campionato not found',
            ], 404);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'campionato' => $campionato,
            ],
        ]);
    });

    Flight::route('POST /', function () {
        require_auth();

        $data = Flight::request()->data->getData();
        $nome = trim((string) ($data['nome'] ?? ''));

        if ($nome === '') {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati campionato non validi.',
            ], 422);
        }

        $id = insert_campionato([
            ':nome' => $nome,
        ]);

        if ($id === false) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Errore inserimento.',
            ], 500);
        }

        Flight::response()->status(201);
        Flight::json([
            'status' => 'ok',
            'data' => [
                'campionato' => get_campionato((int) $id),
            ],
        ]);
    });

    Flight::route('PUT /@id', function (string $id) {
        require_auth();

        $campionato = get_campionato((int) $id);
        if ($campionato === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Campionato not found',
            ], 404);
        }

        $data = Flight::request()->data->getData();
        $nome = trim((string) ($data['nome'] ?? ''));

        if ($nome === '') {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati campionato non validi.',
            ], 422);
        }

        $ok = update_campionato([
            ':id' => (int) $id,
            ':nome' => $nome,
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
                'campionato' => get_campionato((int) $id),
            ],
        ]);
    });

    Flight::route('DELETE /@id', function (string $id) {
        require_auth();

        if (!delete_campionato((int) $id)) {
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
