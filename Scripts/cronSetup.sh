#bin/sh

# CRON 
CRON_FILE="/etc/cron.d/apagnan_cron"
CRON_PROJECT_DIR="/root/Projet-Semestriel-4IW-1"

sudo rm -f $CRON_FILE

# Ajouter la tâche cron
echo "0 0 * * * root cd $CRON_PROJECT_DIR && docker compose exec php bin/console app:expired-invoice-announcement" | sudo tee -a $CRON_FILE > /dev/null
echo "0 0 * * * root cd $CRON_PROJECT_DIR && docker compose exec php bin/console app:update-invoice-status" | sudo tee -a $CRON_FILE > /dev/null
echo "0 0 */3 * * root cd $CRON_PROJECT_DIR && docker compose exec php bin/console app:invoice-payment-reminder" | sudo tee -a $CRON_FILE > /dev/null

echo "\e[33m CRONS TASKS SUCCESSFULLY RUNNED !\e[0m"

sudo chmod 644 $CRON_FILE