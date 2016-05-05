<?php

namespace Logikos\Tests\Forms;

use Logikos\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Phalcon\Forms\Element;

class FormTest extends \PHPUnit_Framework_TestCase {
  protected $di;
  protected $form;
  protected $methods = ['GET','POST','PUT','PATCH','DELETE'];
  protected $valid_methods = ['POST','GET'];
  
  public function setup() {
    $this->di = \Phalcon\Di::getDefault();
    $this->form = new Form();
  }
  public function methods() {
    static $methods = [];
    if (!count($methods))
      foreach ($this->methods as $method)
        $methods[] = [$method];
    return $methods;
  }
  public function testIsValidMethod() {
    foreach ($this->methods as $method) {
      if (in_array($method,$this->valid_methods))
        $this->assertTrue($this->form->isValidMethod($method));
      else
        $this->assertFalse($this->form->isValidMethod($method));
    }
  }
  public function testDefaultMethodIsPost() {
    $this->assertEquals('POST',$this->form->getMethod());
  }
  /**
   * @dataProvider methods
   */
  public function testSetAndGetMethod($method) {
    $this->form->setMethod($method);
    $this->assertEquals($method,$this->form->getMethod());
  }
  public function testGetMethodNotVirtual() {
    $this->form->setMethod('POST');
    $this->form->setMethod('PATCH');
    $this->assertEquals('POST',$this->form->getMethod(false));
  }
  
  public function testFieldList() {
    $this->form->add(new Text('txtfld'));
    $this->form->add(new TextArea('txtarea'));
    $fields = $this->form->fieldList();
    $this->assertArrayHasKey('txtfld',$fields);
    $this->assertArrayHasKey('txtarea',$fields);
  }
}