# Image caching microservice

[![Build Status](https://travis-ci.org/strider2038/image-cache.svg?branch=master)](https://travis-ci.org/strider2038/image-cache) [![Coverage Status](https://coveralls.io/repos/github/strider2038/image-cache/badge.svg?branch=master)](https://coveralls.io/github/strider2038/image-cache?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/strider2038/image-cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/strider2038/image-cache/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cfe1201a-7dab-4eeb-9b00-f0edd63a1690/big.png)](https://insight.sensiolabs.com/projects/cfe1201a-7dab-4eeb-9b00-f0edd63a1690)

Microservice for caching images from remote storages (WebDAV or API) on local server. It can process images by applying simple transformations such as resizing, cropping and shifting.

## Getting started

### Capabilities

Main purpose of this service is to cache images from remote storage and save it to local filesystem. Thus a new request to the same URI will be quickly processed by nginx as a file server (see the picture below).

Besides image caching the application can process images by applying sequence of transformations: resizing, cropping ans shifting. Also it can be used as a proxy service to handle images in remote storages.

![Client server sequence UML diagram][client-server-uml]

Currently, the application can work with the following storages:

* local file storage;
* remote WebDAV storage with OAuth authentication (tested only for Yandex.Disk);
* Yandex static map API.

### Installation

Simplest way to use this project is to run it like a docker microservice.

```bash
docker pull strider2038/image-cache
```

## Authors

Code written by [Igor Lazarev](https://github.com/strider2038).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

[client-server-uml]: http://www.plantuml.com/plantuml/proxy?src=https://raw.githubusercontent.com/strider2038/image-cache/master/docs/uml/client-server.puml
