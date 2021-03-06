# Logikos\Forms
Form manager which extends Phalcon PHP

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

### [Forms Class](src/Form.php)
Wrapper for [Phalcon\Forms\Form](https://github.com/phalcon/cphalcon/blob/master/phalcon/forms/form.zep) which adds some additional [features](docs/Form.md).

### Radioset [Element](src/Element/Radioset.php) and [Tag](src/Tag/Radioset.php)

Added [Logikos\Forms\Element\Radioset](src/Element/Radioset.php) which works like and extends [Phalcon\Forms\Element\Select](https://github.com/phalcon/cphalcon/blob/master/phalcon/forms/element/select.zep) and works the same way except that it outputs radio options instead of a select box.  Resulting radio tag option markup can be controlled by setting [Logikos\Forms\Tag\Radioset](src/Tag/Radioset.php)::useRadioTemplate()

### [SelectOptions](src/SelectOptions.php)

Phalcon plugin to query selectbox options. Works well as a backend to select2. [docs/SelectOptions.md](docs/SelectOptions.md) contains basic usage information.


