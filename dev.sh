#!/bin/bash

build_prod=0
for i in $@; do
    if [[ "$i" == "--build-prod" || "$i" == "-p" ]]; then
        build_prod=1
    fi
done

container_name="imgcache"
container_tag="strider2038:imgcache-service-dev"

echo "Cleaning old images $container_name..."
echo "========================================================================="
docker stop "$container_name"
docker rm "$container_name"

if [ ${build_prod} -eq 1 ]; then
    ./build.sh
fi

echo "Building development image $container_tag..."
echo "========================================================================="

docker build \
    --file Dockerfile.dev \
    --tag "$container_tag" \
    .

echo "Starting container $container_name..."
echo "========================================================================="

docker run \
    -p 80:80 -p 9002:9001 \
    --detach \
    --name "$container_name" \
    --stop-signal SIGKILL \
    --volume $PWD:/imgcache \
    "$container_tag"
