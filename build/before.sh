#!/bin/bash

echo "{\"github-oauth\": {\"github.com\": \"$GITHUB_OAUTH\"}}" >> .docker/composer/auth.json
