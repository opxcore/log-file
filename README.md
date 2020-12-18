# Logging to local file

[![Build Status](https://www.travis-ci.com/opxcore/log-file.svg?branch=master)](https://www.travis-ci.com/opxcore/log-file)
[![Coverage Status](https://coveralls.io/repos/github/opxcore/log-file/badge.svg?branch=master)](https://coveralls.io/github/opxcore/log-file?branch=master)
[![Latest Stable Version](https://poser.pugx.org/opxcore/log-file/v/stable)](https://packagist.org/packages/opxcore/log-file)
[![Total Downloads](https://poser.pugx.org/opxcore/log-file/downloads)](https://packagist.org/packages/opxcore/log-file)
[![License](https://poser.pugx.org/opxcore/log-file/license)](https://packagist.org/packages/opxcore/log-file)

# Introduction

File logger is a PSR-3 compatible logger what records log messages to local files. It can be used as standalone logger
or with [log manager](https://github.com/opxcore/log-manager).

# Installing

`composer require opxcore/log-file`

# Using

All you need is create logger instance and specify a name of file to lag to be written. Then log your messages.

```php
$logger = new \OpxCore\Log\LogFile('/file/name');
$logger->info('Hello world!');
```

File logger extends `AbstractLogger`. See [log-interface](https://github.com/opxcore/log-interface#abstractlogger)