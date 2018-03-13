#!/bin/bash

echo "Deploying image to DockerHub..."

docker login -u "$DOCKER_LOGIN" -p "$DOCKER_PASSWORD";
docker push strider2038/image-cache
