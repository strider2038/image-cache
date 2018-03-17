#!/bin/bash

container_name="image-cache"
container_tag="strider2038/image-cache"

echo "Preparing container for acceptance testing..."
echo "========================================================================="

echo "Starting container..."
docker run \
    --publish 127.0.0.1:1234:80 \
    --detach \
    --name "$container_name" \
    --stop-signal SIGKILL \
    --env APP_CONFIGURATION_FILENAME=config/testing/acceptance-parameters.yml \
    "$container_tag"

docker ps
docker logs "$container_name"
docker exec -it "$container_name" sh -c "supervisorctl status"
sleep 5

curl -v localhost:1234
curl -v 0.0.0.0:1234
nc -zv localhost 1234
nc -zv 0.0.0.0 1234
