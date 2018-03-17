#!/bin/bash

build=0
acceptance_test=0
for i in $@; do
    if [[ "$i" == "--build" || "$i" == "-b" ]]; then
        build=1
    elif [[ "$i" == "--test" || "$i" == "-t" ]]; then
        acceptance_test=1
    fi
done

container_name="image-cache"
container_tag="strider2038/image-cache"
container_dev_name="$container_name-dev"
container_dev_tag="$container_tag:dev"
container_dev_network="$container_name-net"

if [ ${build} -eq 1 ]; then
    ./build.sh

    echo "Building development image $container_dev_tag..."
    echo "========================================================================="

    docker build \
        --file Dockerfile.dev \
        --tag "$container_dev_tag" \
        .
fi

echo "Cleaning old images $container_dev_name..."
echo "========================================================================="
docker stop "$container_dev_name"
docker rm "$container_dev_name"

if [ ${acceptance_test} -eq 0 ]; then

    echo "Starting development container $container_dev_name..."
    echo "========================================================================="

    docker run \
        --publish 80:80 --publish 9002:9001 \
        --detach \
        --name "$container_dev_name" \
        --stop-signal SIGKILL \
        --volume $PWD:/app \
        "$container_dev_tag"

else

    echo "Preparing containers for acceptance testing..."
    echo "========================================================================="

    echo "Resetting network and containers..."
    docker stop "$container_name"
    docker rm "$container_name"
    docker network rm "$container_dev_network"

    echo "Creating shared network..."
    docker network create "$container_dev_network"

    echo "Preparing folders..."
    mkdir -p ./runtime/tests/web
    mkdir -p ./runtime/tests/storage

    echo "Starting containers..."
    docker run \
        --detach \
        --network "$container_dev_network" \
        --network-alias "$container_name" \
        --name "$container_name" \
        --stop-signal SIGKILL \
        --env APP_CONFIGURATION_FILENAME=config/testing/acceptance-parameters.yml \
        --volume $PWD/runtime/tests/web:/app/web \
        --volume $PWD/runtime/tests/storage:/tmp/storage \
        "$container_tag"

    docker run \
        --publish 80:80 \
        --publish 9002:9001 \
        --detach \
        --network "$container_dev_network" \
        --network-alias "$container_dev_name" \
        --name "$container_dev_name" \
        --stop-signal SIGKILL \
        --env ACCEPTANCE_HOST="http://$container_name" \
        --volume $PWD:/app \
        "$container_dev_tag"

fi
