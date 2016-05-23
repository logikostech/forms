<?php

namespace Logikos\Forms;

use Phalcon\Forms\Form as phForm;
use Phalcon\Forms\ElementInterface;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Mvc\ViewInterface;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Tag;

class Form extends phForm {
  
  const _METHOD = '_method';
  protected $_method_element_name = '_method';
  protected $_valid_methods = ['POST','GET'];
  protected $_method = 'POST';
  protected $_decoration_template;
  
  protected $_attributes = [];
  
  
  public function isValidMethod($method) {
    return in_array($method,$this->_valid_methods);
  }
  public function setMethod($method) {
    $method  = strtoupper($method);
    
    if ($this->has(self::_METHOD))
      $this->get(self::_METHOD)
        ->setAttribute('value', $method);
    else {
      $element = new Hidden(self::_METHOD);
      $element->setAttribute('value', $method);
      $this->add($element);
    }
    
    $this->setAttribute('data-method',$method);
    
    if ($this->isValidMethod($method))
      $this->_method = $method;
    
    return $this;
  }
  protected function _methodHackElement() {
    if ($this->has(self::_METHOD))
      return $this->get(self::_METHOD);
  }
  public function getMethod($virtual=true) {
    $method = $this->_method;
    if ($virtual && $element=$this->_methodHackElement())
      if ($v=$element->getAttribute('value'))
        $method = $v;
    return $method;
  }
  
  public function getAction() {
    $requri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
    return parent::getAction() ?: $requri;
  }
  
  public function start(array $attributes=[]) {
    $attributes = array_merge(
        $this->getAttributes(),
        $attributes
    );
    $tags = array();
    $tags[] = Tag::form($attributes);
    
    foreach($this->getElements() as $element) {
      
    }
    
    return implode("",$tags);
  }
  public function end() {
    return '</form>';
  }
  
  public function setAttribute($attribute, $value) {
    $this->_attributes[$attribute] = $value;
    return $this;
  }
  public function getAttribute($attribute, $defaultValue=null) {
    return isset($this->_attributes[$attribute])
        ? $this->_attributes[$attribute]
        : $defaultValue;
  }
  public function getAttributes() {
    return $this->_attributes;
  }
  
  public function fieldlist() {
    return $this->getElements();
  }
  
  public function setDecorationTemplate($path) {
    $this->_decoration_template = $path;
  }
  
  /**
   * This likly is not needed if you have this class loaded with Phalcon\Di
   * @param ViewBaseInterface $view
   */
  public function setView(ViewBaseInterface $view) {
    $this->view = $view;
  }
  public function getView() {
    if (isset($this->view) && $this->view instanceof ViewBaseInterface)
      return $this->view;
    return $this->getDI()->get('view');
  }
  public function renderDecorated($name, $attributes=null, $template=null) {
    $template = $template ?: $this->_decoration_template;
    
    if (!$template)
      return $this->render($name,$attributes);
    
    $element  = $this->get($name);
    $messages = $this->getMessagesFor($element->getName()) ?: [];
    
    ob_start();
    $this->getView()->partial(
        $template,
        [
            'form'     => $this,
            'element'  => $element,
            'messages' => $messages
        ]
    );
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }
}