<?php
declare(strict_types=1);

// Rotte CRUD per i giocatori, incluse le validazioni sul ruolo e sulla squadra.
Flight::group('/giocatori', function () {
    Flight::route('GET /', function () {
        $q = trim((string) (Flight::request()->query->q ?? ''));

        Flight::json([
            'status' => 'ok',
            'data' => [
                'giocatori' => get_giocatori($q),
            ],
        ]);
    });

    Flight::route('GET /@id', function (string $id) {
        $giocatore = get_giocatore((int) $id);

        if ($giocatore === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Giocatore not found',
            ], 404);
        }

        Flight::json([
            'status' => 'ok',
            'data' => [
                'giocatore' => $giocatore,
            ],
        ]);
    });

    Flight::route('POST /', function () {
        require_auth();

        $data = Flight::request()->data->getData();
        $nome = trim((string) ($data['nome'] ?? ''));
        $cognome = trim((string) ($data['cognome'] ?? ''));
        $dataNascita = (string) ($data['data_nascita'] ?? '');
        $ruolo = trim((string) ($data['ruolo'] ?? ''));
        $nazionalita = trim((string) ($data['nazionalita'] ?? ''));
        $idSquadra = isset($data['id_squadra']) ? (int) $data['id_squadra'] : null;

        $ruoliAmmessi = ['Portiere', 'Difensore', 'Centrocampista', 'Attaccante'];
        if ($nome === '' || $cognome === '' || $dataNascita === '' || !in_array($ruolo, $ruoliAmmessi, true)) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati giocatore non validi.',
            ], 422);
        }

        global $pdo;
        $sql = 'INSERT INTO giocatori (nome, cognome, data_nascita, ruolo, nazionalita, id_squadra)
                VALUES (:nome, :cognome, :data_nascita, :ruolo, :nazionalita, :id_squadra)';
        $ok = $pdo->prepare($sql)->execute([
            ':nome' => $nome,
            ':cognome' => $cognome,
            ':data_nascita' => $dataNascita,
            ':ruolo' => $ruolo,
            ':nazionalita' => $nazionalita,
            ':id_squadra' => $idSquadra,
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
                'giocatore' => get_giocatore($id),
            ],
        ]);
    });

    Flight::route('PUT /@id', function (string $id) {
        require_auth();

        $giocatore = get_giocatore((int) $id);
        if ($giocatore === null) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Giocatore not found',
            ], 404);
        }

        $data = Flight::request()->data->getData();
        $nome = trim((string) ($data['nome'] ?? ''));
        $cognome = trim((string) ($data['cognome'] ?? ''));
        $dataNascita = (string) ($data['data_nascita'] ?? '');
        $ruolo = trim((string) ($data['ruolo'] ?? ''));
        $nazionalita = trim((string) ($data['nazionalita'] ?? ''));
        $idSquadra = isset($data['id_squadra']) ? (int) $data['id_squadra'] : null;

        $ruoliAmmessi = ['Portiere', 'Difensore', 'Centrocampista', 'Attaccante'];
        if ($nome === '' || $cognome === '' || $dataNascita === '' || !in_array($ruolo, $ruoliAmmessi, true)) {
            Flight::jsonHalt([
                'status' => 'ko',
                'message' => 'Dati giocatore non validi.',
            ], 422);
        }

        $ok = update_giocatore([
            ':id' => (int) $id,
            ':nome' => $nome,
            ':cognome' => $cognome,
            ':data_nascita' => $dataNascita,
            ':ruolo' => $ruolo,
            ':nazionalita' => $nazionalita,
            ':id_squadra' => $idSquadra,
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
                'giocatore' => get_giocatore((int) $id),
            ],
        ]);
    });

    Flight::route('DELETE /@id', function (string $id) {
        require_auth();

        if (!delete_giocatore((int) $id)) {
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