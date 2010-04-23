<?php

/** Test out some functionality **/

// Init
include 'globals.inc';
$s = new Spider;

// Get an array of search results from teh google
$a = $s->qa('.//*[@id="res"]/div/ol/li/h3/a','http://www.google.co.nz/search?q=daybreakers');

// Print the headers and the array
echo '<pre>',print_r($s->getHead()),'</pre>';
echo '<pre>',print_r($a),'</pre>';

// Get a full DOMList of the search results
$list = $s->qq('.//*[@id="res"]/div/ol/li/h3/a');

// Echo the first nodes innertext
echo $list(0)->inner;

// Print a list of the search results from google
echo '<ul>';
foreach ( $list() as $a ) {
	echo '<li><a href="'.$a->href.'">'.$a->inner.'</a></li>';
}
echo '</ul>';

// Get the score from metacritic
$score = $s->qf('.//*[@id="metascore"]', 'http://www.metacritic.com/film/titles/daybreakers')->inner;
echo "<p>Score for Daybreakers: $score</p>";

?>