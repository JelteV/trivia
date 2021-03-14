<?php

include __DIR__.'/Game.php';

$aGame = new Game();

$aGame->addPlayer("Chet");
$aGame->addPlayer("Pat");
$aGame->addPlayer("Sue");

$aGame->run();
