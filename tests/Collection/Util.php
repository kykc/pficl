<?php

namespace pficlTests\Collection
{
	use \pficl\Collection\Util as ColUtil;
	
	class Util extends \pficl\Test\SimpleTest
	{
		public function getTestList()
    	{
    		return array(
    			'getFieldTest' => array(),
    			);
    	}
    	
    	protected function getFieldTest()
    	{
    		$this->assertEquals(ColUtil::getField(array('a' => 'a'), 'a'), 'a');
    	}
	}
}

?>