<?php namespace Webunion\View;

require_once( dirname(__DIR__) . '/src/View.php');

use Webunion\View\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
	public $viewPath;
	
	public function setUp()
    {
		$this->viewPath = __DIR__ . '/views/';
	
        if (!class_exists('Webunion\\View\\ViewTest')) {
            $this->markTestSkipped('ViewTest was not installed.');
        }
    }

	public function testShouldContainHtmlFromDefaultLayoutAndPage()
    {
		$view = new View( $this->viewPath );
		$view->setVar('msg', '');
		$test = $view->render();
		$this->assertContains( 'DEFAULT LAYOUT', $test );
		$this->assertContains( 'Default:', $test );
    }
	
	public function testShouldContainHtmlFromCustomLayoutAndPage()
    {
		$view = new View( $this->viewPath );
		
		$view->loadLayout('custom');
		$view->setVar('msg', '');
		
		$view->loadPage('custom');
		
		$test = $view->render();
		$this->assertContains( 'CUSTOM LAYOUT', $test );
		$this->assertContains( 'Custom:', $test );
    }
	
	public function testShouldContainDefinedVariable()
    {
		$view = new View( $this->viewPath );
		$view->setVar('msg', 'test');
		$test = $view->render();
		$this->assertContains( 'test', $test );
    }	
	
	public function testShouldContainDefinedFixVariable()
    {
		$view = new View( $this->viewPath );
		$view->setVar('msg', '');
		$view->setFixVar('MSG', 'TEST');
		$test = $view->render();
		$this->assertContains( 'TEST', $test );
    }
	
	public function testShouldNotContainDefinedFixVariable()
    {
		$view = new View( $this->viewPath );
		$view->setVar('msg', '');
		$test = $view->render();
		$this->assertNotContains( '{#MSG#}', $test );
    }
	
	public function testShouldContainValidJSON()
    {
		$array = ['Foo'=>'Bar'];
		$json = '{"Foo":"Bar"}';
		$test = View::encodeJSON( $array );
		
		$this->assertJsonStringEqualsJsonString( $json, $test );
    }	
	
	public function testShouldContainValidJSONWithAditionalNode()
    {
		$array = ['Foo'=>'Bar'];
		$json = '{"aditionalNode":{"Foo":"Bar"}}';
		$test = View::encodeJSON( $array, 'aditionalNode' );
		
		$this->assertJsonStringEqualsJsonString( $json, $test );
    }	
	
	public function testShouldContainValidJSONWithCallBack()
    {
		$array = ['Foo'=>'Bar'];
		$json = '/**/callbackStringFunction({"Foo":"Bar"});';
		$test = View::encodeJSON( $array, null, 'callbackStringFunction' );
		
		$this->assertEquals( $json, $test );
    }
	
	public function testShouldContainValidXML()
    {
		$array = ['Foo'=>'Bar'];
		$xml = '<Foo>Bar</Foo>';
		$test = View::encodeXML( $array );
		
		$this->assertXmlStringEqualsXmlString( $xml, $test );
    }	
	
	public function testShouldContainValidXMLWithAditionalNode()
    {
		$array = ['Foo'=>'Bar'];
		$xml = '<Test>'."\n";
		$xml .= '<Foo>Bar</Foo>';
		$xml .= "\n".'</Test>';
		$test = View::encodeXML( $array, 'Test' );
		
		$this->assertXmlStringEqualsXmlString( $xml, $test );
    }
	
	public function testShouldContainValidXMLWithHeader()
    {
		$array = ['Foo'=>'Bar'];
		$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$xml .= '<Foo>Bar</Foo>';
		$test = View::encodeXML( $array, null, true );
		
		$this->assertXmlStringEqualsXmlString( $xml, $test );
    }
}