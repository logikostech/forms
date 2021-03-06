<?php

namespace Logikos\Forms\Tag;

use Phalcon\Tag\Exception;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;
use Phalcon\Tag;
use Phalcon\Forms\Element\Radio;

abstract class Radioset extends Tag {
  
  protected static $radio_template = '
    <label class="Radioset-item">
      <input {{attributes}} class="Radioset-radio Radioset-radio-{{name}}" />
      <span class="Radioset-item-label">{{label}}</span>
    </label>
  ';
  
  protected static $defaultUsing = ['id','text'];
  
  public static function getRadioTemplate() {
    return static::$radio_template;
  }
  public static function useRadioTemplate($template) {
    static::$radio_template = $template;
  }
  /**
   * much of this logic was modeled after \Phalcon\Tag\Select::selectField()
   * @param unknown $parameters
   * @param string $data
   */
  public static function RadiosetField($params, $data=null) {
    
    if (!is_array($params))
      $params = ['name'=>$params];
    
    if (empty($params['name']) && !empty($params[0]))
      $params['name'] = $params[0];
    
    if (empty($params['name']))
      throw new Exception("name is required for radioset");
    
    if (!$data && !empty($params[1]))
      $data = $params[1];
    
    if (!is_array($data) && !is_object($data))
      throw new Exception("Invalid data provided to Radioset Helper, must be an array or a resultset");
    
    // if $using is a callable function let it loop though the data and return an array
    if (isset($params['using']) && is_callable($params['using']))
      $data = call_user_func($params['using'],$data,$params);
    
    // perhaps $data is a resultset object
    if (is_object($data))
      $data = static::_buildDataFromObject($data,$params);
    
    if (!is_array($data))
      throw new Exception('Invalid data');
    
    return static::_optionsFromArray($params,$data);
    
  }
  protected static function _buildDataFromObject($resultset,$params=null) {

    $using = isset($params['using']) ? $params['using'] : static::$defaultUsing;

    if (!$using || !is_array($using) || count($using) != 2)
      throw new Exception("Parameter 'using' must be an array with two values");
    
    list($idcol,$txtcol) = $using;
    
    $data = [];
    foreach ($resultset as $row) {
      
      if (method_exists($row,'readAttribute')) {
        $data[$row->readAttribute($idcol)] = $row->readAttribute($txtcol);
      }
      elseif (method_exists($row,$v='get'.$idcol) && method_exists($row,$t='get'.$txtcol)) {
        $data[$row->{$v}()] = $row->{$t}();
      }
      else {
        $data[$row->$idcol] = $row->$txtcol;
      }
    }
    return $data;
  }
  
  /**
   * 
   * @param array $options [ 'val' => 'The Label', 3256 => 'John Doe', ... ]
   * @throws Exception
   */
  protected static function _optionsFromArray(array $params, $data) {
    if (!is_array($data))
      throw new Exception("Invalid data provided to Radioset Helper");
    
    self::_autoCheckIfOnlyOneOption($params, $data);
    
    $value   = $params['name']
      ? static::getValue($params['name'],$params) // will use $params['vaue'] if set
      : null;
    
    $cb = (!empty($params[2]) && is_callable($params[2]))
        ? $params[2]
        : [static::class,'radioRender'];
    
    $radio = [];
    
    foreach ($data as $radio_value=>$radio_label) {
      $radio[] = call_user_func($cb,$params['name'],$radio_value,$radio_label,$value);
    }
    
    return implode("",$radio);
  }

  public static function radioRender($name,$value,$label,$curent_value) {
    
    $attr = [
        'type'  => 'radio',
        'name'  => $name,
        'value' => $value
    ];
    
    if (!is_null($curent_value) && $value == $curent_value)
      $attr['checked']='checked';
    
    $attributes = trim(self::renderAttributes('', $attr));
    $radio = "<input {$attributes} />";
    $markup = str_replace(
        ['{{name}}','{{attributes}}','{{label}}'],
        [$name,$attributes,$label],
        self::$radio_template
    );
    return $markup;
  }
  
  public static function getValue($name,$params=null) {
    return parent::getValue($name,$params);
  }
  

  protected static function _autoCheckIfOnlyOneOption($params, $data) {
    if (count($data) === 1)
      self::setDefault($params['name'], array_keys($data)[0]);
  }
}