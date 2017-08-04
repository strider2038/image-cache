#!/bin/sh

tests_type="."
for i in $@; do
    if [[ "$i" == "--unit" || "$i" == "-u" ]]; then
        tests_type="./Unit"
    elif [[ "$i" == "--functional" || "$i" == "-f" ]]; then
        tests_type="./Functional"
    elif [[ "$i" == "--acceptance" || "$i" == "-a" ]]; then
        tests_type="./Acceptance"
    fi
done

/usr/local/bin/php ./../vendor/phpunit/phpunit/phpunit "$tests_type"