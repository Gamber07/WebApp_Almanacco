AVVIO FRONTEND
cd Almanacco/client
VITE_API_BASE=/api npm run dev -- --host 127.0.0.1 --port 5173

AVVIO BACKEND
php -S 127.0.0.1:8001


GESTIONE MYSQL
# Accedi come root per configurare tutto la prima volta
mysql -u root -p

# Dentro MySQL esegui:
CREATE DATABASE almanacco_sportivo;
CREATE USER 'almanacco'@'localhost' IDENTIFIED BY 'almanacco123';
GRANT ALL PRIVILEGES ON almanacco_sportivo.* TO 'almanacco'@'localhost';
FLUSH PRIVILEGES;
EXIT;

ACCESSO AL DB
# Usa questo comando per entrare direttamente nel database con l'utente corretto
mysql -h localhost -u almanacco -p'almanacco123' almanacco_sportivo

