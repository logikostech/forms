<?php

namespace Logikos\Forms\Element;

use Phalcon\Tag;
use Phalcon\Forms\Element;
use Phalcon\Forms\Element\Radio;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\ElementInterface;
use Logikos\Forms\Tag\Radioset as RadiosetTag;

class Radioset extends Select implements ElementInterface {
  public function render($attributes=null) {
    return RadiosetTag::RadiosetField($this->prepareAttributes($attributes), $this->getOptions());
  }
}