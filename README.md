# forms
Form manager built on phalcon

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

Add or register the following namespace strategy to your Phalcon\Loader:

```php

$loader = new Phalcon\Loader();

$loader->registerNamespaces([
    'Logikos\Forms' => '/path/to/this/repo/src/'
]);

$loader->register();
```
