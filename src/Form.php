<?php

namespace Logikos\Forms;

use Phalcon\Forms\Form as phForm;
use Phalcon\Forms\ElementInterface;
use Phalcon\Forms\Element\Hidden;

class Form extends phForm {
  
  const _METHOD = '_method';
  protected $_method_element_name = '_method';
  protected $_valid_methods = ['POST','GET'];
  protected $_method = 'POST';
  
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
  public function fieldlist() {
    return $this->getElements();
  }
}