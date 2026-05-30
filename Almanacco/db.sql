-- Creazione del database
CREATE DATABASE IF NOT EXISTS almanacco_sportivo;
USE almanacco_sportivo;

-- Tabella Utenti 
CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- Tabella Stagioni 
CREATE TABLE stagioni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_stagione VARCHAR(20) NOT NULL,
    anno_inizio INT NOT NULL,
    anno_fine INT NOT NULL,
    eliminata_il DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB;

-- Tabella Squadre
CREATE TABLE squadre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    citta VARCHAR(100) NOT NULL,
    anno_fondazione INT,
    stadio VARCHAR(100) NOT NULL,
    id_squadra_padre INT NULL,
    eliminata_il DATETIME NULL DEFAULT NULL,
    CHECK (anno_fondazione >= 0),
    FOREIGN KEY (id_squadra_padre) REFERENCES squadre(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabella Giocatori
CREATE TABLE giocatori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    data_nascita DATE,
    ruolo VARCHAR(30) NOT NULL,
    nazionalita VARCHAR(50),
    id_squadra INT DEFAULT NULL,
    FOREIGN KEY (id_squadra) REFERENCES squadre(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabella Contratti 
CREATE TABLE contratti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_giocatore INT NOT NULL,
    id_squadra INT NOT NULL,
    data_inizio DATE NOT NULL,
    scadenza DATE,
    numero_maglia INT NOT NULL,
    tipo_contratto VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_giocatore) REFERENCES giocatori(id) ON DELETE CASCADE,
    FOREIGN KEY (id_squadra) REFERENCES squadre(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabella Campionati
CREATE TABLE campionati (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    eliminato_il DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB;

-- Tabella Partite
CREATE TABLE partite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_stagione INT NOT NULL,
    id_campionato INT NOT NULL DEFAULT 1,
    id_squadra_casa INT NOT NULL,
    id_squadra_trasferta INT NOT NULL,
    gol_casa INT DEFAULT 0,
    gol_trasferta INT DEFAULT 0,
    data_partita DATE NOT NULL,
    sede VARCHAR(100) NOT NULL,
    CHECK (gol_casa >= 0),
    CHECK (gol_trasferta >= 0),
    CHECK (id_squadra_casa <> id_squadra_trasferta),
    FOREIGN KEY (id_stagione) REFERENCES stagioni(id) ON DELETE CASCADE,
    FOREIGN KEY (id_campionato) REFERENCES campionati(id) ON DELETE CASCADE,
    FOREIGN KEY (id_squadra_casa) REFERENCES squadre(id) ON DELETE CASCADE,
    FOREIGN KEY (id_squadra_trasferta) REFERENCES squadre(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- INSERIMENTO DATI DI TEST --

INSERT INTO campionati (nome) VALUES 
('Serie A'),
('Coppa Italia'),
('Champions League'),
('Europa League'),
('Supercoppa Italiana');

INSERT INTO utenti (username, password) VALUES 
('admin', '1234');

-- Inserimento Stagioni 
INSERT INTO stagioni (nome_stagione, anno_inizio, anno_fine) VALUES 
('2021/2022', 2021, 2022),
('2022/2023', 2022, 2023),
('2023/2024', 2023, 2024),
('2024/2025', 2024, 2025),
('2025/2026', 2025, 2026);

-- Inserimento Squadre
INSERT INTO squadre (nome, citta, anno_fondazione, stadio, id_squadra_padre) VALUES 
('Inter', 'Milano', 1908, 'San Siro', NULL),
('Juventus', 'Torino', 1897, 'Allianz Stadium', NULL),
('Milan', 'Milano', 1899, 'San Siro', NULL),
('Napoli', 'Napoli', 1926, 'Diego Armando Maradona', NULL),
('Roma', 'Roma', 1927, 'Stadio Olimpico', NULL);

-- Inserimento Giocatori
INSERT INTO giocatori (nome, cognome, data_nascita, ruolo, nazionalita, id_squadra) VALUES 
('Lautaro', 'Martinez', '1997-08-22', 'Attaccante', 'Argentina', 1),
('Dusan', 'Vlahovic', '2000-01-28', 'Attaccante', 'Serbia', 2),
('Rafael', 'Leao', '1999-06-10', 'Attaccante', 'Portogallo', 3),
('Khvicha', 'Kvaratskhelia', '2001-02-12', 'Centrocampista', 'Georgia', 4),
('Nicolo', 'Barella', '1997-02-07', 'Centrocampista', 'Italia', 1),
('Federico', 'Chiesa', '1997-10-25', 'Centrocampista', 'Italia', 2),
('Lorenzo', 'Pellegrini', '1996-06-19', 'Centrocampista', 'Italia', 5),
('Victor', 'Osimhen', '1998-12-29', 'Attaccante', 'Nigeria', 4),
('Theo', 'Hernandez', '1997-10-06', 'Difensore', 'Francia', 3),
('Alessandro', 'Bastoni', '1999-04-13', 'Difensore', 'Italia', 1);

-- Inserimento Contratti 
INSERT INTO contratti (id_giocatore, id_squadra, data_inizio, scadenza, numero_maglia, tipo_contratto) VALUES 
(1, 1, '2023-07-01', '2026-06-30', 10, 'Definitivo'), 
(5, 1, '2023-07-01', '2026-06-30', 23, 'Definitivo'), 
(2, 2, '2023-07-01', '2026-06-30', 9, 'Definitivo'), 
(3, 3, '2023-07-01', '2028-06-30', 10, 'Definitivo'), 
(4, 4, '2023-07-01', '2027-06-30', 77, 'Definitivo'); 

-- Inserimento Partite di esempio
INSERT INTO partite (id_stagione, id_campionato, id_squadra_casa, id_squadra_trasferta, gol_casa, gol_trasferta, data_partita, sede) VALUES 
(1, 1, 1, 2, 1, 0, '2024-02-04', 'San Siro'), 
(1, 1, 3, 4, 1, 0, '2024-02-11', 'San Siro'), 
(2, 2, 2, 5, 2, 1, '2022-10-16', 'Allianz Stadium'),
(3, 3, 5, 1, 0, 2, '2023-11-05', 'Stadio Olimpico'),
(4, 4, 4, 3, 1, 1, '2025-03-02', 'Diego Armando Maradona'),
(5, 5, 1, 5, 3, 2, '2026-05-18', 'San Siro');