#!/bin/bash

<!-- HABILITA TODO O CACHE DO DRUPAL VIA DRUSH -->
drush config-set verbose -y

drush config-set system.performance js.preprocess 1 -y
drush config-set system.performance css.preprocess 1 -y
drush config-set system.performance cache.page.use_internal 1 -y
drush config-set entity.settings cache 1 -y
drush config-set dynamic_page_cache.settings enabled 1 -y
drush config-set system.performance configuration 1 -y
drush config-set system.performance twig.debug 0 -y
