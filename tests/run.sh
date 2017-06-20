#!/bin/sh

tests_type="."
for i in $@; do
    if [[ "$i" == "--unit" || "$i" == "-u" ]]; then
        tests_type="./Unit"
    elif [[ "$i" == "--functional" || "$i" == "-f" ]]; then
        tests_type="./Functional"
    fi
done

/usr/local/bin/php ./../vendor/phpunit/phpunit/phpunit "$tests_type"