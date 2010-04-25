<?php

include 'globals.inc';
include 'template.IMDB.php';

$s = new Spider;
$imdb = new IMDBTemplate;

$movie = $s->applyTemplate( $imdb, 'http://www.imdb.com/title/tt0433362/' );

print_r( $movie );

?>