#!/bin/bash

composer config --global process-timeout 2000
composer install
drush site:install --db-url=mysql://drupal10:drupal10@database/drupal10 -y
sh /app/scripts/import.sh
