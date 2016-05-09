<?php

namespace Logikos\Tests\Forms\Tag;

use Logikos\Forms\Tag\Radioset;
use Phalcon\Di\FactoryDefault as Di;

class RadiosetTest extends \PHPUnit_Framework_TestCase {
  public static $di;
  public static $viewsdir;
  
  public static function setUpBeforeClass() {
    $basedir = realpath(__DIR__.'/../../../');
    $testdir = $basedir.'/tests';
    self::$viewsdir = realpath($testdir.'/views/').'/';
    include_once $basedir."/vendor/autoload.php";
    
    $di = new DI();
    static::$di = $di;
  }
  public function setUp() {}
  
  public function testRadiosetEmptyDataThrowsException() {
    $this->setExpectedException('Phalcon\Tag\Exception');
    Radioset::RadiosetField('empid');
  }
  public function testRadiosetStringDataThrowsException() {
    $this->setExpectedException('Phalcon\Tag\Exception');
    Radioset::RadiosetField('empid','string');
  }
  public function testOneRadioInSet() {
    $this->assertRadiosetWorks('empid',[
        1234 => 'john'
    ]);
  }
  public function testTwoRadioInSet() {
    $this->assertRadiosetWorks('empid',[
        1234 => 'john',
        4321 => 'bob'
    ]);
  }
  public function testCanChangeTemplate() {
    $teststring = '<!-- foobar -->';
    Radioset::useRadioTemplate($teststring.Radioset::getRadioTemplate());
    $name  = 'empid';
    $value = 1234;
    $data  = [$value=>'john'];
    $markup = Radioset::RadiosetField($name,$data);
    $this->assertContainsRadio($markup, $name, $value);
    $this->assertContains($teststring,$markup);
  }
  protected function assertRadiosetWorks($name,$data) {
    $result = Radioset::RadiosetField($name,$data);
    $this->assertTrue(is_string($result),"Result needs to be a sting, '".gettype($result)."' given instead");
    foreach ($data as $value=>$label)
      $this->assertContainsRadio($result,$name,$value);
  }
  protected function assertContainsRadio($haystack,$name,$value) {
    $substr = '<input type="radio" name="'.$name.'" value="'.$value.'"';
    $this->assertRegExp('/'.preg_quote($substr).'/', $haystack);
  }
}