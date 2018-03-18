# Goals and ideas

## Goals for version v1.1

- [ ] Performance optimization: configuration caching and/or compiling
- [ ] Performance optimization: composer cache 
- [ ] Performance optimization: compiling to PHAR archive
- [ ] Performance optimization: lazy services, validation cache
- [ ] Refactor ImageParameters to be ignored while saving
- [ ] Cache mechanism for source accessor
- [ ] Add log level to configuration
- [ ] Support for phpDocumentor
- [ ] JPEG optimization by https://github.com/tjko/jpegoptim
- [ ] Refactor HttpClientInterface to use object-based options
- [ ] Migrate from supervisor to systemd daemons
- [ ] Migrate to PHPUnit 7 when Phake will be updated

## Ideas

- Google map source (inspect licensing problems)
- Yandex map marker
- Layer support for GIF and PNG http://php.net/manual/ru/imagick.coalesceimages.php
- Add referrer control to Security component
- rename ImageExtractor and ImageWriter?
- rename ImageExtractor::extractImage() and ImageWriter::insertImage()?
- rename project to Image Caching Proxy Service?
