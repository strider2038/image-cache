#!/bin/bash

container_name="image-cache"
container_tag="strider2038/image-cache"

echo "Preparing container for acceptance testing..."
echo "========================================================================="

echo "Starting container..."
docker run \
    --publish 80:80 \
    --detach \
    --name "$container_name" \
    --stop-signal SIGKILL \
    --env APP_CONFIGURATION_FILENAME=config/testing/acceptance-parameters.yml \
    "$container_tag"

docker ps
docker logs "$container_name"

echo "Waiting for container services to start..."
sleep 5
docker exec -it "$container_name" sh -c "supervisorctl status"
curl -v localhost
