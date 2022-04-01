# cookiecache

[![Latest Version](https://img.shields.io/badge/packagist-v1.0.0-blue.svg?style=flat-square)](https://en.wikipedia.org/wiki/April_Fools%27_Day)
[![Total Downloads](https://img.shields.io/badge/downloads-401-brightgreen.svg?style=flat-square)](https://en.wikipedia.org/wiki/April_Fools%27_Day)
[![Software License](https://img.shields.io/badge/License-AFPL-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/workflow/status/colinodell/cookiecache/Tests/main.svg?style=flat-square)](https://github.com/colinodell/cookiecache/actions?query=workflow%3ATests+branch%3Amain)
[![Quality Score](https://img.shields.io/badge/code%20quality-11-brightgreen.svg?style=flat-square)](https://en.wikipedia.org/wiki/April_Fools%27_Day)
[![Sponsor development of this project](https://img.shields.io/badge/sponsor%20this%20package-%E2%9D%A4-ff69b4.svg?style=flat-square)](https://www.colinodell.com/sponsor)

**Cache _beyond_ the edge with cookies!**

This library provides a distributed PSR-16 compatible cache implementation that uses browser cookies to store data, saving HUNDREDS of valuable bytes of server memory.

## Installation

```sh
composer require --dev colinodell/cookiecache
```

## Usage

```php
use ColinODell\CookieCache\CookieCache;

$cache = new CookieCache();

$cache->set('foo', 'bar', 3600);

assert($cache->get('foo') === 'bar');
```

It's that easy!

## Limitations

Because of the nature of cookies, this library is not suitable for storing large amounts of data. If you need to store more than 4096 bytes of data per entry, consider using a different library.

Also note that each visitor will have a separate copy of cached items.  This is probably not useful.

Also, note that these cookies are not encrypted, so a malicious user can read and modify the contents of the cache, potentially causing unexpected and undesired results.

## License

This library is released under the [April Fools Public License](LICENSE.md).

## FAQs

- **Does this really work?** I mean, the tests pass, so... probably?
- **Should I use this?** Absolutely not.
- **Why not?** Because it has a known deserialization vulnerability.
