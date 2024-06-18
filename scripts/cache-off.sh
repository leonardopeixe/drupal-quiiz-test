#!/bin/bash

# DESABILITA TODO O CACHE DO DRUPAL VIA DRUSH
drush config-set system.logging error_level verbose -y

drush config-set system.performance js.preprocess 0 -y
drush config-set system.performance css.preprocess 0 -y
drush config-set system.performance cache.page.use_internal 0 -y
drush config-set entity.settings cache 0 -y
drush config-set dynamic_page_cache.settings enabled 0 -y
drush config-set system.performance configuration 0 -y
drush config-set system.performance twig.debug 1 -y
