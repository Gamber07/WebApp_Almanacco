<?php
declare(strict_types=1);

// Layer modello: query e operazioni CRUD usate dalle rotte Flight.
function get_user_by_credentials(string $username, string $password): ?array
{
    global $pdo;

    // Fetch user by username and verify password with password_verify
    $stm = $pdo->prepare('SELECT * FROM utenti WHERE username = :username');
    $stm->execute([':username' => $username]);

    $user = $stm->fetch();
    if ($user === false) {
        return null;
    }

    // If password is stored hashed, verify; otherwise fall back to plain compare
    $stored = $user['password'] ?? '';

    if (password_verify($password, $stored) || $stored === $password) {
        return $user;
    }

    return null;
}

function get_squadre(string $q = ''): array
{
    global $pdo;

    $sql = 'SELECT * FROM squadre';
    if ($q !== '') {
        $sql .= ' WHERE nome LIKE :q_nome OR citta LIKE :q_citta';
    }
    $sql .= ' ORDER BY id DESC';

    $stm = $pdo->prepare($sql);
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stm->bindValue(':q_nome', $like);
        $stm->bindValue(':q_citta', $like);
    }
    $stm->execute();

    return $stm->fetchAll();
}

function get_squadra(int $id): ?array
{
    global $pdo;

    $stm = $pdo->prepare('SELECT * FROM squadre WHERE id = :id');
    $stm->execute([':id' => $id]);
    $squadra = $stm->fetch();

    return $squadra === false ? null : $squadra;
}

function insert_squadra(array $data)
{
    global $pdo;

    $sql = 'INSERT INTO squadre (nome, citta, anno_fondazione, stadio, id_squadra_padre)
            VALUES (:nome, :citta, :anno_fondazione, :stadio, :id_squadra_padre)';
    $ok = $pdo->prepare($sql)->execute($data);

    return $ok ? $pdo->lastInsertId() : false;
}

function update_squadra(array $data): bool
{
    global $pdo;

    $sql = 'UPDATE squadre SET nome = :nome, citta = :citta, stadio = :stadio, anno_fondazione = :anno_fondazione WHERE id = :id';
    return $pdo->prepare($sql)->execute($data);
}

function delete_squadra(int $id): bool
{
    global $pdo;

    $stm = $pdo->prepare('DELETE FROM squadre WHERE id = :id');
    return $stm->execute([':id' => $id]);
}

function get_giocatori(string $q = ''): array
{
    global $pdo;

    $sql = 'SELECT g.*, s.nome as nome_squadra
            FROM giocatori g
            LEFT JOIN squadre s ON g.id_squadra = s.id';

    if ($q !== '') {
        $sql .= ' WHERE g.nome LIKE :q_nome OR g.cognome LIKE :q_cognome';
    }
    $sql .= ' ORDER BY g.id DESC';

    $stm = $pdo->prepare($sql);
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stm->bindValue(':q_nome', $like);
        $stm->bindValue(':q_cognome', $like);
    }
    $stm->execute();

    return $stm->fetchAll();
}

function get_giocatore(int $id): ?array
{
    global $pdo;

    $stm = $pdo->prepare('SELECT g.*, s.nome as nome_squadra FROM giocatori g LEFT JOIN squadre s ON g.id_squadra = s.id WHERE g.id = :id');
    $stm->execute([':id' => $id]);
    $giocatore = $stm->fetch();

    return $giocatore === false ? null : $giocatore;
}

function insert_giocatore(array $data)
{
    global $pdo;

    $sql = 'INSERT INTO giocatori (nome, cognome, data_nascita, ruolo, nazionalita)
            VALUES (:nome, :cognome, :data_nascita, :ruolo, :nazionalita)';
    $ok = $pdo->prepare($sql)->execute($data);

    return $ok ? $pdo->lastInsertId() : false;
}

function delete_giocatore(int $id): bool
{
    global $pdo;

    $stm = $pdo->prepare('DELETE FROM giocatori WHERE id = :id');
    return $stm->execute([':id' => $id]);
}

function update_giocatore(array $data): bool
{
    global $pdo;

    $sql = 'UPDATE giocatori
            SET nome = :nome,
                cognome = :cognome,
                data_nascita = :data_nascita,
                ruolo = :ruolo,
                nazionalita = :nazionalita,
                id_squadra = :id_squadra
            WHERE id = :id';

    return $pdo->prepare($sql)->execute($data);
}

function get_contratti(): array
{
    global $pdo;

    $sql = 'SELECT c.*, g.cognome as atleta, s.nome as squadra
            FROM contratti c
            JOIN giocatori g ON c.id_giocatore = g.id
            JOIN squadre s ON c.id_squadra = s.id
            ORDER BY c.scadenza DESC';
    return $pdo->query($sql)->fetchAll();
}

function get_contratto(int $id): ?array
{
    global $pdo;

    $stm = $pdo->prepare('SELECT c.*, g.cognome as atleta, s.nome as squadra
        FROM contratti c
        JOIN giocatori g ON c.id_giocatore = g.id
        JOIN squadre s ON c.id_squadra = s.id
        WHERE c.id = :id');
    $stm->execute([':id' => $id]);
    $contratto = $stm->fetch();

    return $contratto === false ? null : $contratto;
}

function insert_contratto(array $data)
{
    global $pdo;

    $sql = 'INSERT INTO contratti (id_giocatore, id_squadra, data_inizio, scadenza, numero_maglia, tipo_contratto)
            VALUES (:id_giocatore, :id_squadra, :data_inizio, :scadenza, :numero_maglia, :tipo_contratto)';
    $ok = $pdo->prepare($sql)->execute($data);

    return $ok ? $pdo->lastInsertId() : false;
}

function delete_contratto(int $id): bool
{
    global $pdo;

    $stm = $pdo->prepare('DELETE FROM contratti WHERE id = :id');
    return $stm->execute([':id' => $id]);
}

function update_contratto(array $data): bool
{
    global $pdo;

    $sql = 'UPDATE contratti
            SET id_giocatore = :id_giocatore,
                id_squadra = :id_squadra,
                data_inizio = :data_inizio,
                scadenza = :scadenza,
                numero_maglia = :numero_maglia,
                tipo_contratto = :tipo_contratto
            WHERE id = :id';

    return $pdo->prepare($sql)->execute($data);
}

function get_partite(string $q = ''): array
{
    global $pdo;

    $sql = 'SELECT p.*, s1.nome as casa, s2.nome as trasferta, st.nome_stagione
            FROM partite p
            JOIN squadre s1 ON p.id_squadra_casa = s1.id
            JOIN squadre s2 ON p.id_squadra_trasferta = s2.id
            JOIN stagioni st ON p.id_stagione = st.id';
    
    if ($q !== '') {
        $sql .= ' WHERE s1.nome LIKE :q_casa OR s2.nome LIKE :q_trasferta OR st.nome_stagione LIKE :q_stagione';
    }
    
    $sql .= ' ORDER BY p.data_partita DESC';
    
    $stm = $pdo->prepare($sql);
    if ($q !== '') {
        $like = '%' . $q . '%';
        $stm->bindValue(':q_casa', $like);
        $stm->bindValue(':q_trasferta', $like);
        $stm->bindValue(':q_stagione', $like);
    }
    $stm->execute();
    return $stm->fetchAll();
}

function get_partita(int $id): ?array
{
    global $pdo;

    $stm = $pdo->prepare('SELECT p.*, s1.nome as casa, s2.nome as trasferta, st.nome_stagione
        FROM partite p
        JOIN squadre s1 ON p.id_squadra_casa = s1.id
        JOIN squadre s2 ON p.id_squadra_trasferta = s2.id
        JOIN stagioni st ON p.id_stagione = st.id
        WHERE p.id = :id');
    $stm->execute([':id' => $id]);
    $partita = $stm->fetch();

    return $partita === false ? null : $partita;
}

function insert_partita(array $data)
{
    global $pdo;

    $sql = 'INSERT INTO partite (id_stagione, id_campionato, id_squadra_casa, id_squadra_trasferta, gol_casa, gol_trasferta, data_partita, sede)
            VALUES (:id_stagione, :id_campionato, :id_casa, :id_trasferta, :gol_casa, :gol_trasferta, :data, :sede)';
    $ok = $pdo->prepare($sql)->execute($data);

    return $ok ? $pdo->lastInsertId() : false;
}

function update_partita(array $data): bool
{
    global $pdo;

    $sql = 'UPDATE partite
            SET id_stagione = :id_stagione,
                id_campionato = :id_campionato,
                id_squadra_casa = :id_casa,
                id_squadra_trasferta = :id_trasferta,
                gol_casa = :gol_casa,
                gol_trasferta = :gol_trasferta,
                data_partita = :data,
                sede = :sede
            WHERE id = :id';

    return $pdo->prepare($sql)->execute($data);
}

function get_classifica(int $id_stagione, int $id_campionato): array
{
    global $pdo;

    $sql = 'SELECT s.nome,
            COALESCE(SUM(
                CASE
                    WHEN p.id_squadra_casa = s.id AND p.gol_casa > p.gol_trasferta THEN 3
                    WHEN p.id_squadra_trasferta = s.id AND p.gol_trasferta > p.gol_casa THEN 3
                    WHEN (p.id_squadra_casa = s.id OR p.id_squadra_trasferta = s.id) AND p.gol_casa = p.gol_trasferta THEN 1
                    ELSE 0
                END
            ), 0) as punti
            FROM squadre s
            LEFT JOIN partite p ON (s.id = p.id_squadra_casa OR s.id = p.id_squadra_trasferta)
                AND p.id_stagione = :id_stagione
                AND p.id_campionato = :id_campionato
            GROUP BY s.id
            ORDER BY punti DESC, s.nome ASC';

    $stm = $pdo->prepare($sql);
    $stm->execute([
        ':id_stagione' => $id_stagione,
        ':id_campionato' => $id_campionato,
    ]);

    return $stm->fetchAll();
}

function get_campionati(): array
{
    global $pdo;

    return $pdo->query('SELECT * FROM campionati')->fetchAll();
}

function get_stagioni(): array
{
    global $pdo;

    return $pdo->query('SELECT * FROM stagioni ORDER BY anno_inizio DESC')->fetchAll();
}

function get_stagione(int $id): ?array
{
    global $pdo;

    $stm = $pdo->prepare('SELECT * FROM stagioni WHERE id = :id');
    $stm->execute([':id' => $id]);
    $stagione = $stm->fetch();

    return $stagione === false ? null : $stagione;
}

function insert_stagione(array $data)
{
    global $pdo;

    $sql = 'INSERT INTO stagioni (nome_stagione, anno_inizio, anno_fine)
            VALUES (:nome_stagione, :anno_inizio, :anno_fine)';
    $ok = $pdo->prepare($sql)->execute($data);

    return $ok ? $pdo->lastInsertId() : false;
}

function update_stagione(array $data): bool
{
    global $pdo;

    $sql = 'UPDATE stagioni
            SET nome_stagione = :nome_stagione,
                anno_inizio = :anno_inizio,
                anno_fine = :anno_fine
            WHERE id = :id';

    return $pdo->prepare($sql)->execute($data);
}

function delete_stagione(int $id): bool
{
    global $pdo;

    $stm = $pdo->prepare('DELETE FROM stagioni WHERE id = :id');
    return $stm->execute([':id' => $id]);
}

function delete_partita(int $id): bool
{
    global $pdo;

    $stm = $pdo->prepare('DELETE FROM partite WHERE id = :id');
    return $stm->execute([':id' => $id]);
}