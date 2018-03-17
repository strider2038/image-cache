#!/bin/bash

container_name="image-cache"
container_tag="strider2038/image-cache"

echo "Preparing container for acceptance testing..."
echo "========================================================================="

echo "Preparing folders..."
mkdir -p ./runtime/tests/acceptance/web
mkdir -p ./runtime/tests/acceptance/storage

echo "Starting container..."
docker run \
    --detach \
    --name "$container_name" \
    --stop-signal SIGKILL \
    --env APP_CONFIGURATION_FILENAME=config/testing/acceptance-parameters.yml \
    --volume $PWD/runtime/tests/acceptance/web:/app/web \
    --volume $PWD/runtime/tests/acceptance/storage:/tmp/storage \
    "$container_tag"

docker ps
docker logs "$container_name"

echo "Starting acceptance testing..."
./vendor/bin/phpunit --testsuite acceptance

echo "Removing containers..."

docker stop "$container_name"
docker rm "$container_name"
