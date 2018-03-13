#!/bin/bash

build=0
for i in $@; do
    if [[ "$i" == "--build" || "$i" == "-b" ]]; then
        build=1
    fi
done

container_name="image-cache"
container_tag="strider2038/image-cache:dev"

echo "Cleaning old images $container_name..."
echo "========================================================================="
docker stop "$container_name"
docker rm "$container_name"

if [ ${build} -eq 1 ]; then
    ./build.sh

    echo "Building development image $container_tag..."
    echo "========================================================================="

    docker build \
        --file Dockerfile.dev \
        --tag "$container_tag" \
        .
fi

echo "Starting container $container_name..."
echo "========================================================================="

docker run \
    -p 80:80 -p 9002:9001 \
    --detach \
    --name "$container_name" \
    --stop-signal SIGKILL \
    --volume $PWD:/app \
    "$container_tag"
