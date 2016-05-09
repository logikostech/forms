<?php

namespace Logikos\Tests\Forms;

use Logikos\Forms\Form;
use Phalcon\DI\FactoryDefault as DI;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Phalcon\Forms\Element;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Simple as SimpleView;

class FormTest extends \PHPUnit_Framework_TestCase {
  static $di;
  static $viewsdir;
  
  /**
   * @var Form
   */
  protected $form;
  protected $methods = ['GET','POST','PUT','PATCH','DELETE'];
  protected $valid_methods = ['POST','GET'];

  public static function setUpBeforeClass() {
    $basedir = realpath(__DIR__.'/../../');
    $testdir = $basedir.'/tests';
    self::$viewsdir = realpath($testdir.'/views/').'/';
    include_once $basedir."/vendor/autoload.php";
    
    $di = new DI();
    $di->set('form',"Logikos\Forms\Form");
    static::$di = $di;
  }
  public function setUp() {
    $this->form = static::$di->get('form');
  }
  public static function phView() {
    static $view;
    if (!$view) {
      $view = new View();
      $view->setViewsDir(static::$viewsdir);
    }
    return $view;
  }
  public static function phSimpleView() {
    static $view;
    if (!$view) {
      $view = new SimpleView();
      $view->setViewsDir(static::$viewsdir);
    }
    return $view;
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
  
  public function testCanSetView() {
    $view = static::phSimpleView();
    $view->foo='bar';
    $this->form->setView($view);
    $this->assertEquals('bar',$this->form->view->foo);
    $this->assertEquals('bar',$this->form->getView()->foo);
  }
  
  public function testSetDecorationTemplate() {
    $this->form->view = static::phSimpleView();
    $this->form->add(new Text('test'));
    $this->form->setDecorationTemplate('template/foo');
    $output = $this->form->renderDecorated('test');
    $this->assertEquals('foobar',$output);
  }
}