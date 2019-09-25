[![Build Status](https://travis-ci.org/improvframework/configuration.svg?branch=master)](https://travis-ci.org/improvframework/configuration)
[![Dependency Status](https://www.versioneye.com/user/projects/575991347757a00034dc4f95/badge.svg?style=flat)](https://www.versioneye.com/user/projects/575991347757a00034dc4f95)
[![Code Climate](https://codeclimate.com/github/improvframework/configuration/badges/gpa.svg)](https://codeclimate.com/github/improvframework/configuration)
[![Coverage Status](https://coveralls.io/repos/improvframework/configuration/badge.svg?branch=master&service=github)](https://coveralls.io/github/improvframework/configuration?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/improvframework/configuration.svg)](http://hhvm.h4cc.de/package/improvframework/configuration)

# Improv Framework - Configuration

An opinionated Configuration object for parsing Key-Value arrays.

## Overview ##
Almost all applications require some sort of runtime Configuration. Often, this is done via environment variables, XML or YML files, Property files, etc.  Regardless of the mechanism, the configuration typically exists as an array of string Key-Value pairs at some point during application bootstrapping.

In many cases, multiple values will have to be encoded into a single entry, which will need to be parsed and transformed into something meaningful to the application.  Some values, like a list a single string of multiple IP addresses (e.g.). might need to be extracted into an array of the same, while other values may simply require conversion to native integers or booleans.  Aside from the primitive mappings, each application will likely have a unique set of necessary transformations than any other.

There may be times when an incoming set of Key-Value pairs contains several more tuples than a specific, "configurable" portion of an application may need.  This is particularly common for Dockerized services wherein there are many environment variables, but only some of them are intended to affect the _application's_ behavior; the balance is typically used for container or OS -level instruction. The bootstrapping portion of an application may receive _all_ Key-Value pairs from the environment, although it is really only concerned with a small few.  Similarly, there may be components or subcomponents within the application that also demand tailored config, but unrelated to any other portion of the software.  In these cases, it is sometimes desirable to limit the scope of any "Configuration" object to only those items with which a specific portion of the application is concerned.

The `\Improv\Configuration` library aims to ease the parsing of Key-Value pairs into objects that are useful within the context of a specific application, without requiring extension or bloating of the Configuration object, itself.  Further, it provides the ability to quickly organize related configurations into dedicated objects as desired.


## Package Installation ##

### Using Composer (Recommended) ###

```
composer require improvframework/configuration
```

### Manual ###

Each release is available for download from the [releases page on Github](https://github.com/improvframework/configuration/releases). Alternatively, you may fork, clone, and [build the package](#buildpackage). Then, install into the location of your choice.

This package conforms to PSR-4 autoloading standards.


## Usage ##
Suppose some set of environment variables, the means by which they end up "in" the environment aside (Mesos, `docker run -e""`, `docker-compose.yml`, etc):

```
APP_DEBUG_ON="0"
APP_DEBUG_VERBOSITY="4"
APP_DB_READ_POOL="mysql://dbname=appname;host=1.1.1.1|mysql://dbname=appname;host=2.2.2.2"
APP_DB_READ_USER="username"
APP_DB_READ_PASS="password"
APP_DB_WRITE_POOL="mysql://dbname=appname;host=2.2.0.0"
APP_DB_WRITE_USER="username"
APP_DB_WRITE_PASS="password"
APP_MAX_LOGIN_ATTEMPTS="3"
APP_WHITELIST_CIDR="123.456.7.8/32|123.456.255/12,"
APP_SOMESERVICE_API_KEY="oJE5Zh1HUATturnZFHgHuYEZk1q30TWkdIQ"
APP_SOMESERVICE_API_SECRET="4vqK8XYZef1KygvHxROhjGRPAhMteHdMD6LD0a"
APP_SOMESERVICE_API_URL="https://api.example.com/v1.5"
NGINX_MAX_BODY_SIZE="4M"
NGINX_SENDFILE="off"
PHPFPM_HEADER_SIZE="4M"
PHPFPM_BUFFER_COUNT="16"
FPM_BUFFER_SIZE="1M"
```

### Basic Key Reading ###
Read in all configuration, do some existence checking, and pluck a couple elements out:

```php
$config = new \Improv\Configuration($_ENV);

$config->has('APP_DEBUG_VERBOSITY'); // true
$config->has('SOME_OTHER_KEY');      // false

$config->get('APP_DEBUG_VERBOSITY'); // string(1) "4"
$config->get('PHPFPM_BUFFER_COUNT'); // string(2) "16"
```

### Prefix Filtration ###
The environment variables above are nicely "namespaced" to indicate their intention/direction within the container.  If our application is really only concerned with the variables that are prefixed with `APP_`, we can filter to just those by passing a second parameter during instantiation.  Note here that when the key is requested, you no longer need to specify the prefix:

```php
$config = new \Improv\Configuration($_ENV, 'APP_');

$config->get('DEBUG_VERBOSITY');     // string(1) "4"
$config->get('APP_DEBUG_VERBOSITY'); // throws InvalidKeyException
$config->get('PHPFPM_BUFFER_COUNT'); // throws InvalidKeyException
```
This becomes particularly useful when registering and referencing various configurations within, say, a container and services within the application are only interested in certain variables.

The `withPrefix` method will take a given, "base" Configuration object and return a new one from it, applying another layer of filtering by prefix on from the keys in the base.

```php
$container['config.app'] = function () {
	return new \Improv\Configuration($_ENV, 'APP_');
};

$container['config.debug'] = function (Container $container) {
    $base = $container->get('config.app');
    return $base->withPrefix('DEBUG_');
};

$container['config.db'] = function (Container $container) {
	$base = $container->get('config.app');
	return $base->withPrefix('DB_');
};

$container['config.api'] = function (Container $container) {
    $base = $container->get('config.app');
    return $base->withPrefix('SOMESERVICE_API_');
};

// ... //

$container['database.read'] = function (Container $container) {
	$dbconfig = $container->get('config.db');
	return new Database(
		$dbconfig->get('READ_POOL'),
		$dbconfig->get('READ_USER'),
		$dbconfig->get('READ_PASS')
	);
};

// ... //

$container['api.service'] = function (Container $container) {
	$apiconfig = $container->get('config.api');
	return new ApiService(
		$apiconfig->get('URL'),
		$apiconfig->get('KEY'),
		$apiconfig->get('SECRET')
	);
};

```

### Mapping Values ###
The values pulled out of the environment most often come through as strings, and may also be encoded in some way, for example multiple values delimited by pipes, semi-colons or commas.  It may be desirable to map strings to primitive types like `int` or `bool`, and to explode delimited strings into arrays or even objects. This library supports this behavior.

With no action, values come out "as is".

```php
$config = new \Improv\Configuration($_ENV, 'APP_');

$config->get('DEBUG_ON');           // string(1) "0"
$config->get('DEBUG_VERBOSITY');    // string(1) "4"
$config->get('MAX_LOGIN_ATTEMPTS'); // string(1) "3"
$config->get('APP_WHITELIST_CIDR'); // string(29) "123.456.7.8/32|123.456.255/12"
$config->get('DB_READ_POOL');       // string(71) "mysql://dbname=appname;host=1.1.1.1|mysql://dbname=appname;host=2.2.2.2"
```

The `map` method can be used to specify translations using built-in methods or callbacks. Additionally, multiple keys can be targeted for the same mapping at once:

```php
$config = new \Improv\Configuration($_ENV, 'APP_');

// Use the built-in boolean mapper
$config->map('DEBUG_ON')->toBool();

// Assign multiple keys at once, using built-in int mapper
$config->map('DEBUG_VERBOSITY', 'MAX_LOGIN_ATTEMPTS')->toInt();

// Invoke the "using" method to and callback to parse values
$config->map(
	'WHITELIST_CIDR',
	'DB_READ_POOL'
)->using(
	function($val) {
		return explode('|', $val);
	}
);

// Now, all future retrievals from any part of the application
// will spit out the translated value(s)

// ... //

$config->get('DEBUG_ON');           // bool(false)
$config->get('DEBUG_VERBOSITY');    // int(4)
$config->get('MAX_LOGIN_ATTEMPTS'); // int(3)

$config->get('WHITELIST_CIDR');
// array(2) {
//  [0] => string(14) "123.456.7.8/32"
//  [1] => string(14) "123.456.255/12"
// }

```

The `using` method can take any valid `callable`, the signature of which should accept a single string value as input. The `callable` can return any value relevant to the application - primitives or even full-blown objects.  A callable may also exist as any class that implements the `__invoke($value)` method.  This makes it possible to share "Maps", inject dependencies, etc.

```php
class DelimiterMap
{
	private $delimiter;

	public function __construct($delimiter)
	{
		$this->delimiter = $delimiter;
	}

	public function __invoke($value)
	{
		return explode($this->delimiter, $value);
	}
}

// ... //

$config->map('WHITELIST_CIDR')->using(new DelimiterMap('|'));

```

## Notes and Issues ##
Please note that this is a new package, currently in beta. Feel free to reach out with ideas, bug reports, or contribution questions.

## Additional Documentation

You may [run the API Doc build target](#buildtargets) to produce and peruse API documentation for this package.

## <a name="buildtest"></a>Running the Build/Test Suite

This package makes extensive use of the [Phing](https://www.phing.info/ "Click to Learn More") build tool.

Below is a list of notable build targets, but please feel free to peruse the `build.xml` file for more insight.

### Default Target

`./vendor/bin/phing` will execute the `build` target (the same as executing `./vendor/bin/phing build`).
This performs a linting, syntax check, runs all static analysis tools, the test suite, and produces API documentation.

### <a name="buildpackage"></a>"Full" Packaging Target

Executing `./vendor/bin/phing package` will run all above checks and, if passing, package the source into a shippable file
with only the relevant source included therein.

### <a name="buildtargets"></a>Selected Individual Targets
 
- Run the Tests
    - `./vendor/bin/phing test`
    - `./vendor/bin/phpunit`
- Perform Static Analysis
    - `./vendor/bin/phing static-analysis`
    - The generated reports are in `./build/output/reports`
- Produce API Documentation
    - `./vendor/bin/phing documentapi`
    - The generated documentation is in `./build/docs/api`
- Build Package from Source
    - `./vendor/bin/phing package`
    - The artifacts are in `./build/output/artifacts`

[![License](https://poser.pugx.org/improvframework/configuration/license)](https://packagist.org/packages/improvframework/configuration)
[![Latest Stable Version](https://poser.pugx.org/improvframework/configuration/v/stable)](https://packagist.org/packages/improvframework/configuration)
[![Latest Unstable Version](https://poser.pugx.org/improvframework/configuration/v/unstable)](https://packagist.org/packages/improvframework/configuration)
[![Total Downloads](https://poser.pugx.org/improvframework/configuration/downloads)](https://packagist.org/packages/improvframework/configuration)
