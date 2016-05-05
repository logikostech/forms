<?php

namespace Logikos\Forms;

use Phalcon\Forms\Form as phForm;
use Phalcon\Forms\ElementInterface;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Mvc\ViewInterface;
use Phalcon\Mvc\ViewBaseInterface;

class Form extends phForm {
  
  const _METHOD = '_method';
  protected $_method_element_name = '_method';
  protected $_valid_methods = ['POST','GET'];
  protected $_method = 'POST';
  protected $_decoration_template;
  
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
  
  public function setDecorationTemplate($path) {
    $this->_decoration_template = $path;
  }
  
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