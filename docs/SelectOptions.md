# Logikos\Forms\SelectOptions Usage

## Add to Dependency Injector
```php
$di = new Phalcon\Di();

$di->set('selectOptions',function($modelname,$options=null) {
  $e = new EventsManager;
  $s = new SelectOptions($modelname,$options);
  $s->setEventsManager($e);
  return $s;
});

Phalcon\Di::setDefault($di);
```

## Optional method in BaseModel for easy access
```php
class BaseModel extends Phalcon\Mvc\Model {
  public static function getSelectOptions($options=null) {
    
    $selectoptions = Phalcon\Di::getDefault()->get(
        'selectOptions',
        [static::class,$options]
    );
    
    if (defined(static::class.'::ID_COLUMN'))
      $selectoptions->setIdColumn(static::ID_COLUMN);
    
    if (defined(static::class.'::TEXT_COLUMN'))
      $selectoptions->setTextColumn(static::TEXT_COLUMN);
    
    return $selectoptions;
  }
}
```

## Use BaseModel Method from controller or anywhere
```php
public function robotOptionsAction() {
  /* @var $selectoptions \Logikos\Forms\SelectOptions */
  $request = new Phalcon\Http\Request();
  $selectoptions = Robots::getSelectOptions();
  
  $selectoptions
    ->options($request->get())   // good for select2 serverside, ex: ?search=Terminator
    ->setIdColumn('id')
    ->setTextColumn('name')
    ->addSelect([
      'price',
      'year'
    ])
    ->addConditions(
        "type = :type:",
        [
            'type' => 'mechanical',
        ]
    );
  $result = $selectoptions->result();
  
  $response = new Phalcon\Http\Response();
  $response->setJsonContent($result);
  $response->send();
}
```
