#!/bin/bash

echo "{\"github-oauth\": {\"github.com\": \"$GITHUB_OAUTH\"}}" >> .docker/files/var/run/composer/auth.json
