<?php

include __DIR__.'/Game.php';

$notAWinner;

  $aGame = new Game();
  
  $aGame->add("Chet");
  $aGame->add("Pat");
  $aGame->add("Sue");
  
// Ref: move this code into Game class
// Ref: While do is probably not needed
  do {

    // Ref: create method for throwing the dice.
    $aGame->roll(rand(0,5) + 1);
    
    if (rand(0,9) == 7) {
      $notAWinner = $aGame->wrongAnswer();
    } else {
      // Ref: Change name of this method.
      $notAWinner = $aGame->wasCorrectlyAnswered();
    }
    
  } while ($notAWinner);
  
