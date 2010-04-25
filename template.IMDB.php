<?php

class IMBDTemplate {

	public $title = '//div[@id="tn15title"]/h1';
	public $year = '//h1/span/a';
	public $rating = '//div[@class="starbar-meta"]/b';
	public $votes = './/*[@id='tn15rating']/div[1]/div/div[2]/a';

	function process( $o ) {
	
		$o->title = substr( $o->title, 0, strpos( $o->title, '<') );
		$o->votes = preg_replace('/[^\d]/','', $o->votes);
		
	}
	
}

?>