#!/bin/bash
while [ true ]
do
    if [[ $(date -d "now" +'%S') = "00" ]]
    then
        php /var/www/artisan schedule:run --verbose --no-interaction &
        sleep 60
    else
        sleep 1
    fi
done
