# Logikos\Forms\Form Features

## Methods
### addClass($string $class)
Add a class to the form tag

### setMethod(String $method)
Sets the method attribute for the form tag if the method is a valid method

Regardless if the method is valid html form tag method attribute value, it creates a hidden input with `name="_method"` and sets a `data-method` attribute of the form tag with a value of `$method`.

### setAttribute(String $attribute, String $value)
Set form tag attribute

### getAttribute(String $attribute)
Get form tag attribute

### start(Array $attributes=[])
Returns String form tag with set attributes and all hidden elements

### end()
Returns String form close tag

### getElementNames() Method
Returns Array of all element names.

### addElement(ElementInterface $element, Array $attributes, String $label)
This method just does the $element->setAttributes() and setLabel() for you before doing a `$form->add($element)`.
Considering your often going to want to set a label and attributes for the elements.
```php
$form->addElement(new Phalcon\Forms\Element\Text, ['class'=>'foobar'], $label);
```

### RenderDecorated(String $name, Array $attributes=null, String $template=null)
Returns String element rendered with partial view $template
```php
$form->RenderDecorated('firstname',['data-someattr'=>'value'],'form/element/text');
```
In the partial view the following variables will be exposed:
- `$form` Object, eg: `Logikos\Forms\Form` object (which extends `Phalcon\Forms\Form`)
- `$element` Object for requested element, eg: `Phalcon\Forms\Element\Select` object
- `$name` String value of name attribute
- `$type` String element object shortname, so `Phalcon\Forms\Element\Select` would result in 'Select'
- `$label` String `$form->getLabel($name)`
- `$messages` Array `$form->getMessagesFor($name)`

