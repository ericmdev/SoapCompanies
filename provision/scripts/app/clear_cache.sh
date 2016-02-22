#!/bin/bash

# Clear production cache.
php $WEBAPP/bin/console cache:clear --env=prod
chmod 777 -R $WEBAPP/var/cache/