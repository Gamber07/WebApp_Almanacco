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
    $allCampionati = get_campionati(true);

    $idStagione = (int) (Flight::request()->query->id_stagione ?? ($stagioni[0]['id'] ?? 0));
    $stagioneSelezionata = null;
    foreach ($stagioni as $stagione) {
        if ((int) $stagione['id'] === $idStagione) {
            $stagioneSelezionata = $stagione;
            break;
        }
    }

    if ($stagioneSelezionata === null) {
        $stagioneSelezionata = $stagioni[0] ?? null;
        $idStagione = (int) ($stagioneSelezionata['id'] ?? 0);
    }

    $annoInizio = (int) ($stagioneSelezionata['anno_inizio'] ?? 0);

    $campionati = array_values(array_filter($allCampionati, static function (array $campionato) use ($annoInizio): bool {
        $eliminatoIl = $campionato['eliminato_il'] ?? null;
        if ($eliminatoIl === null) {
            return true;
        }

        return (int) date('Y', strtotime((string) $eliminatoIl)) > $annoInizio;
    }));

    $idCampionato = (int) (Flight::request()->query->id_campionato ?? ($campionati[0]['id'] ?? 0));

    if ($idCampionato > 0) {
        $campionatoSelezionato = null;
        foreach ($campionati as $campionato) {
            if ((int) $campionato['id'] === $idCampionato) {
                $campionatoSelezionato = $campionato;
                break;
            }
        }

        if ($campionatoSelezionato === null) {
            $campionatoStorico = get_campionato($idCampionato);
            if ($campionatoStorico !== null) {
                $campionatoEliminatoIl = $campionatoStorico['eliminato_il'] ?? null;
                $campionatoValidoPerStagione = $campionatoEliminatoIl === null
                    || (int) date('Y', strtotime((string) $campionatoEliminatoIl)) > $annoInizio;

                if ($campionatoValidoPerStagione) {
                    $campionati[] = $campionatoStorico;
                }
            }
        }
    }

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