<?php

	require_once('include.php');
	
	date_default_timezone_set('Europe/Riga');
    
    class Test extends \pficl\Test\SimpleTest
    {
    	public function getTestList()
    	{
    		return array(
    			'test1' => array(),
    			'test2' => array(),
    			);
    	}
    	
    	protected function test1()
    	{
    		$this->assertEquals(1,1);
    		$this->assertEquals(1,2);
    	}
    	
    	protected function test2()
    	{
    		$this->assertEquals(1+2, 2+1);
    	}
    }
    
    print_r(Test::make()->run()->makeCliTrace());
?>
