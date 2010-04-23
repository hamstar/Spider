<?php

/** Get the ratings for Daybreakers **/

include 'globals.inc';

echo '<h2>Daybreakers ratings</h2>';

$s = new Spider;

echo 'IMDB: '.$s->qf(".//*[@id='tn15rating']/div[1]/div/div[2]/b", 'http://www.imdb.com/title/tt0433362/' )->inner.'<br/>';
echo 'Metacritic: '.$s->qf(".//*[@id='metascore']", 'http://www.metacritic.com/film/titles/daybreakers' )->inner.'<br/>';
echo 'Rotten Tomatoes: '.$s->qf(".//*[@id='tomatometer_score']/span[1]", 'http://www.rottentomatoes.com/m/daybreakers/' )->inner .'<br/>';

?>