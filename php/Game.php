<?php
// Ref: move this method into class.
//function $this->printMessage($string) {
//  echo $string."\n";
//}

class Game {
    // Ref: Give these vars a scope.
    private array $players          = [];
    private array $places           = [0];
    private array $purses           = [0];
    private array $inPenaltyBox     = [0];

    private array $popQuestions     = [];
    private array $scienceQuestions = [];
    private array $sportsQuestions  = [];
    private array $rockQuestions    = [];

    private int $currentPlayer = 0;
    private bool $isGettingOutOfPenaltyBox;

    public function  __construct()
    {

    }

    /**
     * Print a message on screen
     *
     * @param string $message
     */
    private function printMessage(string $message)
    {
        echo "{$message}\n";
    }

    /**
     * Added method to run the game loop.
     */
    public function run()
    {
        $this->createQuestions();

        do {
            // Ref: create method for throwing the dice.
            $this->roll(rand(0,5) + 1);

            if (rand(0,9) == 7) {
                $notAWinner = $this->wrongAnswer();
            } else {
                // Ref: Change name of this method.
                $notAWinner = $this->wasCorrectlyAnswered();
            }

        } while ($notAWinner);
    }

    // Ref: Rename this one to createQuestions.
	private function createQuestions(){
		 for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, "Science Question " . $i);
            array_push($this->sportsQuestions, "Sports Question " . $i);
            array_push($this->rockQuestions, "Rock Question " . $i);
        }
	}

	private function isPlayable() {
		return ($this->howManyPlayers() >= 2);
	}

	public function add(string $playerName): bool {
	   array_push($this->players, $playerName);
	   $this->places[$this->howManyPlayers()] = 0;
	   $this->purses[$this->howManyPlayers()] = 0;
	   $this->inPenaltyBox[$this->howManyPlayers()] = false;

	    $this->printMessage($playerName . " was added");
	    $this->printMessage("They are player number " . count($this->players));
		return true;
	}

	private function howManyPlayers(): int {
		return count($this->players);
	}

	// Ref: Rename method to turn.
	private function  roll(int $roll) {
		$this->printMessage($this->players[$this->currentPlayer] . " is the current player");
		$this->printMessage("They have rolled a " . $roll);

		if ($this->inPenaltyBox[$this->currentPlayer]) {
		    // Ref: implement method that represents the logic going to penalty box.
			if ($roll % 2 != 0) {
				$this->isGettingOutOfPenaltyBox = true;

				$this->printMessage($this->players[$this->currentPlayer] . " is getting out of the penalty box");
			$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
				if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;

				$this->printMessage($this->players[$this->currentPlayer]
						. "'s new location is "
						.$this->places[$this->currentPlayer]);
				$this->printMessage("The category is " . $this->currentCategory());
				$this->askQuestion();
			} else {
				$this->printMessage($this->players[$this->currentPlayer] . " is not getting out of the penalty box");
				$this->isGettingOutOfPenaltyBox = false;
				}

		} else {

		$this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] + $roll;
			if ($this->places[$this->currentPlayer] > 11) $this->places[$this->currentPlayer] = $this->places[$this->currentPlayer] - 12;

			$this->printMessage($this->players[$this->currentPlayer]
					. "'s new location is "
					.$this->places[$this->currentPlayer]);
			$this->printMessage("The category is " . $this->currentCategory());
			$this->askQuestion();
		}

	}

	private function  askQuestion() {
		if ($this->currentCategory() == "Pop")
			$this->printMessage(array_shift($this->popQuestions));
		if ($this->currentCategory() == "Science")
			$this->printMessage(array_shift($this->scienceQuestions));
		if ($this->currentCategory() == "Sports")
			$this->printMessage(array_shift($this->sportsQuestions));
		if ($this->currentCategory() == "Rock")
			$this->printMessage(array_shift($this->rockQuestions));
	}

	private function currentCategory() {
        // Could be done shorter.
		if ($this->places[$this->currentPlayer] == 0) return "Pop";
		if ($this->places[$this->currentPlayer] == 4) return "Pop";
		if ($this->places[$this->currentPlayer] == 8) return "Pop";
		if ($this->places[$this->currentPlayer] == 1) return "Science";
		if ($this->places[$this->currentPlayer] == 5) return "Science";
		if ($this->places[$this->currentPlayer] == 9) return "Science";
		if ($this->places[$this->currentPlayer] == 2) return "Sports";
		if ($this->places[$this->currentPlayer] == 6) return "Sports";
		if ($this->places[$this->currentPlayer] == 10) return "Sports";
		return "Rock";
	}

	private function wasCorrectlyAnswered() {
		if ($this->inPenaltyBox[$this->currentPlayer]){
			if ($this->isGettingOutOfPenaltyBox) {
				$this->printMessage("Answer was correct!!!!");
			$this->purses[$this->currentPlayer]++;
				$this->printMessage($this->players[$this->currentPlayer]
						. " now has "
						.$this->purses[$this->currentPlayer]
						. " Gold Coins.");

                // Note: This looks weird...
				$winner = $this->didPlayerWin();
				$this->currentPlayer++;
				if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;

				return $winner;
			} else {
				$this->currentPlayer++;
				if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
				return true;
			}



		} else {

			$this->printMessage("Answer was corrent!!!!");
			// Create method: EarnCoin
		$this->purses[$this->currentPlayer]++;
			$this->printMessage($this->players[$this->currentPlayer]
					. " now has "
					.$this->purses[$this->currentPlayer]
					. " Gold Coins.");

			// Note: This looks weird...
			$winner = $this->didPlayerWin();
			$this->currentPlayer++;
			if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;

			return $winner;
		}
	}

	private function wrongAnswer(){
		$this->printMessage("Question was incorrectly answered");
		$this->printMessage($this->players[$this->currentPlayer] . " was sent to the penalty box");
	$this->inPenaltyBox[$this->currentPlayer] = true;

		$this->currentPlayer++;
		if ($this->currentPlayer == count($this->players)) $this->currentPlayer = 0;
		return true;
	}


	private function didPlayerWin() {
		return !($this->purses[$this->currentPlayer] == 6);
	}
}
