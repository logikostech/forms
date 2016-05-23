<?php

namespace Logikos\Forms\Element;

use Phalcon\Tag;
use Phalcon\Forms\Element;
use Phalcon\Forms\Element\Radio;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\ElementInterface;
use Logikos\Forms\Tag\Radioset as RadiosetTag;
use InvalidArgumentException;

class Radioset extends Select implements ElementInterface {
  protected $_rendercallback;
  protected $_radiotemplate;

  public function setRadioTemplate($view) {
    $this->_radiotemplate = $view;
  }
  public function getRadioTemplate() {
    return $this->_radiotemplate;
  }
  
  public function setRenderCallback($callback) {
    if (!is_callable($callback))
      throw new InvalidArgumentException("RenderCallback must be callable...");
    
    $this->_rendercallback = $callback;
  }
  
  public function getRenderCallback() {
    if ($this->getRadioTemplate())
      $this->setRenderCallback([$this,'renderWithTemplate']);
    
    return $this->_rendercallback;
  }
  
  public function render($attributes=null) {
    $params    = $this->prepareAttributes($attributes);
    $params[0] = $this->getName();
    $params[1] = $this->getOptions();
    $params[2] = $this->getRenderCallback();
    return RadiosetTag::RadiosetField($params);
  }
  
  public function renderWithTemplate($name,$value,$label,$curent_value) {
    
    $attr = [
        'type'  => 'radio',
        'name'  => $name,
        'value' => $value,
        'id'    => null
    ];
    
    $radio = new Radio($name,$attr);
    
    $radio->setDefault($curent_value);
    
    $view = $this->getView();
    $view->partial(
        $this->_radiotemplate,
        [
            'form'    => $this->getForm(),
            'element' => $radio,
            'name'    => $name,
            'value'   => $value,
            'label'   => $label
        ]
    );
  }
  
  /**
   * @return \Phalcon\Mvc\ViewBaseInterface
   */
  protected function getView() {
    /* @var $form \Phalcon\Forms\Form */
    $form = $this->getForm();
    
    if (isset($this->view) && $this->view instanceof ViewBaseInterface)
      return $this->view;
    return $form->getDI()->get('view');
  }
}