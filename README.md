# Logikos\Forms
Form manager which extends Phalcon PHP

Please note that this is **NOT** yet a stable repo.  Once a stable release is available I will create a version tag for it.

## Installation

### Installing via Composer

Install Composer in a common location or in your project:

```bash
curl -s http://getcomposer.org/installer | php
```

create or edit the `composer.json` file as follows:

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/logikostech/forms"
        }
    ],
    "require": {
        "logikostech/forms": "dev-master"
    }
}
```

Run the composer installer:

```bash
$ php composer.phar install
```

### Installing via GitHub

Just clone the repository in a common location or inside your project:

```
git clone https://github.com/logikostech/forms.git
```

## Autoloading

Add or register the following namespace strategy to your `Phalcon\Loader`:

```php

$loader = new Phalcon\Loader();

$loader->registerNamespaces([
    'Logikos\Forms' => '/path/to/this/repo/src/'
]);

$loader->register();
```

## Key features

Added [Logikos\Forms\Element\Radioset](https://github.com/logikostech/forms/blob/master/src/Element/Radioset.php) which works like and extends [Phalcon\Forms\Element\Select](https://github.com/phalcon/cphalcon/blob/master/phalcon/forms/element/select.zep) and works the same way except that it outputs radio options instead of a select box.  Resulting radio tag option markup can be controlled by setting [Logikos\Forms\Tag\Radioset](https://github.com/logikostech/forms/blob/master/src/Tag/Radioset.php)::useRadioTemplate()