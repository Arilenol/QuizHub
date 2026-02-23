#!/bin/bash
DB_DIR="/usr/src/quizhub/database"
DB_FILE="$DB_DIR/database.db"
DEFAULT_DB="/usr/src/quizhub/config/default_database.db"
SESSION_DIR="/usr/src/quizhub/sessions"

echo "Initialisation du conteneur..."

mkdir -p "$SESSION_DIR"
chmod 777 "$SESSION_DIR"


mkdir -p "$DB_DIR"

if [ ! -f "$DB_FILE" ]; then
    echo "Aucune base de données trouvée. Copie de default_database.db..."
    cp "$DEFAULT_DB" "$DB_FILE"
else
    echo "Base de données existante trouvée. On la conserve."
fi

chmod 777 "$DB_DIR"
if [ -f "$DB_FILE" ]; then
    chmod 666 "$DB_FILE"
fi

echo "Démarrage de l'application..."
exec "$@"
set -e
cd public
exec php -S 0.0.0.0:"$1"