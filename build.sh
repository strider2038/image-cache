#!/bin/bash

docker_run=0
for i in $@; do
   if [[ "$i" == "--run" || "$i" == "-r" ]]; then
       docker_run=1
   fi
done

echo "Starting to build image..."
echo "========================================================================="

docker build --pull --tag strider2038:imgcache-service .

echo "========================================================================="
echo "Image created"


if [ $docker_run -eq 1 ]; then
    echo "Running image..."
    docker stop $(docker ps -a -q)
    docker rm $(docker ps -a -q)

    docker run \
        -p 80:80 \
        --detach \
        --name imgcache \
        --stop-signal SIGKILL \
        strider2038:imgcache-service
fi
