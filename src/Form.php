<?php

namespace Logikos\Forms;

use Phalcon\Forms\Form as phForm;
use Phalcon\Forms\ElementInterface;
use Phalcon\Forms\Element\Hidden;

class Form extends phForm {
  public function action($action) {
    parent::setAction($action);
    return $this;
  }
  public function method($method='POST') {
    $hidden = new Hidden('_method');
    $hidden->setAttribue('value',strtoupper($method));
    $this->add($hidden);
    return $this;
  }
  public function fieldlist() {
    var_dump($this->getElements());
  }
}