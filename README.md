# Nether\Common

[![Packagist](https://img.shields.io/packagist/v/netherphp/common.svg?style=for-the-badge)](https://packagist.org/packages/netherphp/common)
[![Build Status](https://img.shields.io/github/actions/workflow/status/netherphp/common/phpunit.yml?style=for-the-badge)](https://github.com/netherphp/common/actions)
[![codecov](https://img.shields.io/codecov/c/gh/netherphp/common?style=for-the-badge&token=VQC48XNBS2)](https://codecov.io/gh/netherphp/common)

A collection of things that made no sense as their own library and are typically
needed by every project I ever build at some point.

--------

## Classes of Note

`use Nether\Common;`

### `Common\Prototype`

Base class that provides a default constructor designed to handle filling in
the object from a keyed dataset, for example an array that is the result row
from a database query. Also provides by default access to all the attribute
handling packages.

### `Common\Datastore`

A data storage object so that arrays of data can be manipulated from an OOP
interface with a lot of common methods so where the current dataset can be
manipulated or return a fresh datastore with the modified dataset.

### `Common\PasswordTester`

A thing that can validate if a password is complex enough based on a few rules
that can be customised. It can additionally then explain why it failed to pass.

### `Common\Units\Bytes`

A thing that when given an integer of a filesize in bytes can print back the
info in a human readable format in units of choice (example: MB or MiB).

### `Common\Units\Timeframe`

When given two dates can print back how much time is between them in a human
readable format of choice. Can handle both time since and time until.

--------

## Traits of Note

`use Nether\Common;`

### `Common\Package\ClassInfoPackage`

Bolt onto a class to provide access for reading the attributes assigned to the
class.

### `Common\Package\MethodInfoPackage`

Bolt onto a class to provide access for reading the attributes assigned to
the methods within the class.

### `Common\Package\PropertyInfoPackage`

Bolt onto a class to provide access for reading the attributes assigned to the
properties within the class.




# ***DEVELOPER NOTES***

* **PHP Constant:** UNIT_TEST_GO_BRRRT=TRUE
* `if(defined('UNIT_TEST_GO_BRRRT'))`

This constant is defined by the `phpunit.xml` file and some code uses it to
alter its behaviour while the test suite is running. Primarily, code which is
designed to `exit()` seems to make PHPUnit explode.

* **ENV Variable:** UNIT_TEST_HITS_HARD=1
* `if(isset($_ENV['UNIT_TEST_HITS_HARD']))`

This variable is defined by the Github Actions `phpunit.yaml` file. When
defined will allow some unit tests to try harder to achieve full coverage,
which will include performing system altering tasks to make sure they work.
