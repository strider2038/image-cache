# Image Cache microservice

[![Build Status](https://travis-ci.org/strider2038/imgcache-service.svg?branch=master)](https://travis-ci.org/strider2038/imgcache-service) [![Coverage Status](https://coveralls.io/repos/github/strider2038/imgcache-service/badge.svg?branch=master)](https://coveralls.io/github/strider2038/imgcache-service?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/strider2038/imgcache-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/strider2038/imgcache-service/?branch=master)

Image caching microservice with connection to cloud hostings

## Goals for version v1.0

- [x] Migrate to Symfony Dependency Injection component
- [x] File operations service
- [x] Refactor render methods in images (eliminate side effect in classes)
- [x] Refactor controllers and routing to use different image caches
- [x] Basic logging
- [x] Transformation classes refactoring (changing names and using flyweight pattern)
- [x] Image saving with options
- [x] Migrate to PSR-7 request and response interfaces (PSR like realisation)
- [x] Image writing to sources
- [x] Move processing configuration parsing into key parser
- [x] Functional testing
- [x] Yandex Static Map source
- [x] Tune console and IDE debug via docker, not ssh
- [x] Split ImageController to action classes
- [x] Split ImageCache class to ImageStorage and ImageCache
- [x] Router rewrites Request URI, eliminate location in controllers
- [ ] Logging guzzle requests https://michaelstivala.com/logging-guzzle-requests/
- [ ] Add CodeSniffer and Sensio Labs to CI
- [ ] Yandex.Disk source
- [ ] Migrate to collections based on Doctrine ArrayCollection
- [ ] Migrate to validation based on Symfony and Doctrine annotations
  - storage and cache key as data object
- [ ] Shift transformation
- [ ] Rotate transformation
- [ ] Flip transformation
- [ ] Migrate from supervisor to systemd daemons
- [ ] Support for building docker containers in Travis
- [ ] Fix tests codestyle
- [ ] Acceptance testing
- [ ] Move all todos to github issues
- [ ] Rename project to "Image caching microservice"
- [ ] Refactor SaveOptions to be ignored while saving

## Goals for version v1.1
- [ ] Cache mechanism for source accessor
- [ ] Performance optimization (lazy services, validation cache)
- [ ] Support for phpDocumentor
- [ ] JPEG optimization by https://github.com/tjko/jpegoptim

## Ideas
- Google map source (inspect licensing problems)
- Yandex map marker
- Layer support for GIF and PNG http://php.net/manual/ru/imagick.coalesceimages.php
