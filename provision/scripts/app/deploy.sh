#!/bin/bash

# Create MySQL database.
RESULT=`mysqlshow --user=root --password=password soap_companies_db| grep -v Wildcard | grep -o soap_companies_db`
if [ "$RESULT" != "soap_companies_db" ]; then
	php $WEBAPP/bin/console doctrine:database:create
fi

# Generate ORM.
php $WEBAPP/bin/console doctrine:generate:entities AppBundle
php $WEBAPP/bin/console doctrine:schema:update --force

# Clear production cache.
. clear_cache.sh

# Generate initial data.
curl -O localhost/api/companies
for i in {1..5}
do
	curl -O localhost/api/quotes/GOOG
	curl -O localhost/api/quotes/MSFT
    curl -O localhost/api/quotes/IBM
done
