#!/bin/bash

echo "Starting to build image..."
echo "========================================================================="

docker build --pull --tag strider2038:imgcache-service ./docker

echo "========================================================================="
echo "Image created"
