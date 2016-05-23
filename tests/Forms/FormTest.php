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
use Phalcon\Forms\Element\Hidden;

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
  

  # TEST Form Tag Attributes
  
  public function testCanSetandGetFormTagAttribute() {
    $attr  = (object) ['name'=>'data-a','value'=>'abc'];
    $this->form->setAttribute($attr->name,$attr->value);
    $this->assertEquals(
        $attr->value,
        $this->form->getAttribute($attr->name)
    );
  }
  
  public function testFormAttributesViaSetAttributeMethod() {
    $attr  = (object) ['name'=>'data-foo','value'=>'bar'];
    $this->form->setAttribute($attr->name,$attr->value);
    $this->assertNodeAttributeValue(
        $this->getFormNode(), 
        $attr->name, 
        $attr->value
    );
  }
  public function testFormAttributesViaStartMethod() {
    $attr  = (object) ['name'=>'data-foo','value'=>'bar2'];
    
    $this->assertNodeAttributeValue(
        $this->getFormNode([$attr->name=>$attr->value]), 
        $attr->name, 
        $attr->value
    );
  }

  public function testFormStartMethodIncludesHidenElements() {
    $attr    = (object) ['name'=>'foo','value'=>'bar'];
    $element = new Hidden($attr->name);
    //$element->setAttribute('value',$attr->value);
    //$element->setDefault($attr->value);
    $this->form->add($element);
    
    $elementNode = $this->findFirstInNode(
        $this->getFormNode(),
        './input[@type="hidden" and @name="'.$attr->name.'"]'
    );
    
    $this->assertInstanceOf('DOMNode', $elementNode);
  }
  public function testFormImportData() {
    $attr    = (object) ['name'=>'foo','value'=>'bar'];
    $this->form
      ->add(new Hidden($attr->name))
      ->import([$attr->name=>$attr->value]);
    
    $elementNode = $this->findFirstInNode(
        $this->getFormNode(),
        './input[@type="hidden" and @name="'.$attr->name.'"]'
    );
    $this->assertNodeAttributeValue(
        $elementNode,
        'value',
        $attr->value,
        'testing form->import()'
    );
  }
  
  public function testAddElementWithLabel() {
    $label = 'First Name';
    $this->form->addElement(
        new Text('fname'),
        ['data-foo'=>'bar'],
        $label
    );
    $element = $this->form->get('fname');
    $this->assertEquals($label, $element->getLabel());
    $this->assertEquals('bar',$element->getAttribute('data-foo'));
  }
  
  # TEST Form Methods (POST, GET, PUT, PATCH etc.)
  
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
  
  
  
  
  
  
  
  

  # Data Providers
  public function methods() {
    static $methods = [];
    if (!count($methods))
      foreach ($this->methods as $method)
        $methods[] = [$method];
    return $methods;
  }
  
  
  # Helpers
  protected function assertNodeAttributeValue(\DOMNode $node,$attribute,$value,$message=null) {
    $attribute = $node->attributes->getNamedItem($attribute);
    $this->assertInstanceOf('DOMAttr', $attribute, 'Node attribute not defined');
    $this->assertEquals(
        $value,
        $attribute->nodeValue,
        $message
    );
  }
  
  protected function getFormNode($attribs=[]) {
    $start = $this->form->start($attribs);
    $end   = $this->form->end();
    $html  = "<html><body>%s</body></html>";
    $doc   = new \DOMDocument();
    $doc->loadHTML(sprintf($html,$start.$end));
    return $doc->getElementsByTagName('form')->item(0);
  }

  protected static function phView() {
    static $view;
    if (!$view) {
      $view = new View();
      $view->setViewsDir(static::$viewsdir);
    }
    return $view;
  }
  protected static function phSimpleView() {
    static $view;
    if (!$view) {
      $view = new SimpleView();
      $view->setViewsDir(static::$viewsdir);
    }
    return $view;
  }
  protected function dumpnode($node) {
    $markup = $node->ownerDocument->saveHTML();
    var_dump($markup);
  }

  protected function findFirstInNode(\DOMNode $node, $query) {
    if (is_array($query))
      $query = $this->buildDomQuery($query[0], array_slice($attr,-1));
    
    $xpath   = new \DOMXPath($node->ownerDocument);
    $nodes   = $xpath->query($query,$node);
    $this->assertEquals(1, $nodes->length, 'No matching nodes found');
    return $nodes->item(0);
  }
  
  protected function buildDomQuery($tag,array $attr) {
    $attr = [];
    foreach($attributes as $k=>$v)
      $attr[] = sprintf('@%s="%s"',$k,$v);

    $query = sprintf("./%s[%s]",$tag,explode($attr,' and '));
    return $query;
    $example = './input[@type="hidden" and @name="'.$attr->name.'"]';
  }
}