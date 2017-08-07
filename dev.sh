#!/bin/bash

build_prod=0
for i in $@; do
    if [[ "$i" == "--build-prod" || "$i" == "-p" ]]; then
        build_prod=1
    fi
done

echo "Cleaning old images..."
echo "========================================================================="
docker stop $(docker ps -a -q)
docker rm $(docker ps -a -q)

if [ $build_prod -eq 1 ]; then
    ./build.sh
fi

echo "Building development image..."
echo "========================================================================="

docker build \
    --file Dockerfile.dev \
    --tag strider2038:imgcache-service-dev \
    .

echo "Starting container..."
echo "========================================================================="

docker run \
    -p 23:22 -p 80:80 -p 9002:9001 \
    --detach \
    --name imgcache \
    --stop-signal SIGKILL \
    --volume $PWD:/imgcache \
    strider2038:imgcache-service-dev
