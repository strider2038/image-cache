#!/bin/bash

container_name="image-cache"
container_tag="strider2038/image-cache"

echo "Preparing container for acceptance testing..."
echo "========================================================================="

echo "Preparing folders..."
mkdir -p ./runtime/tests/acceptance/web
mkdir -p ./runtime/tests/acceptance/storage
chmod 0777 ./runtime/tests/acceptance/web
chmod 0777 ./runtime/tests/acceptance/storage

echo "Starting container..."
docker run \
    --publish 127.0.0.1:1234:80 \
    --detach \
    --name "$container_name" \
    --stop-signal SIGKILL \
    --env APP_CONFIGURATION_FILENAME=config/testing/acceptance-parameters.yml \
    --volume $PWD/runtime/tests/acceptance/web:/app/web \
    --volume $PWD/runtime/tests/acceptance/storage:/tmp/storage \
    "$container_tag"

docker ps
docker logs "$container_name"

curl -v localhost:1234
curl -v 0.0.0.0:1234
nc -zv localhost 1234
nc -zv 0.0.0.0 1234
