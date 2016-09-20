# EXS-BulkEmailCheckerBundle

[![Build Status](https://travis-ci.org/ExSituMarketing/EXS-BulkEmailCheckerBundle.svg?branch=master)](https://travis-ci.org/ExSituMarketing/EXS-BulkEmailCheckerBundle)

## Installation

Download the bundle using composer

```
$ composer require exs/bulk-email-checker-bundle
```

Enable the bundle

```
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EXS\BulkEmailCheckerBundle\EXSBulkEmailCheckerBundle(),
        // ...
    );
}
```


## Configuration

Minimum required configuration

```yml
exs_bulk_email_checker:
    api_key: 'YourApiKey'
```

Complete configuration (default values shown)

```yml
exs_bulk_email_checker:
    enabled: true
    pass_on_error: true
    api_key: 'YourApiKey'
    api_url: 'http://api-v4.bulkemailchecker2.com/?key=#api_key#&email=#email#'
```

## Usage

Use the "BulkEmailChecker" constraint.

```php
<?php
// On any entity or model class.

use EXS\BulkEmailCheckerBundle\Validator\Constraints as ExsAssert;
// ...

class SomeClass
{
    // ...

    /**
     * @var string
     *
     * @ExsAssert\BulkEmailChecker()
     */
    private $email;
    
    // ...
}

```

You can also use directly the service called "exs_bulk_email_checker.bulk_email_checker_manager" and it's "validate()" method.

```php
// Dummy example

$email = 'foo@bar.baz';
$manager = $this->container->get('exs_bulk_email_checker.bulk_email_checker_manager');
$valid = $manager->validate($email); // boolean value

```
