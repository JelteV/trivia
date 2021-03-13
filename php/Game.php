<?php

class Game {
    private const MINIMAL_NUMBER_OF_PLAYERS = 2;

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

    /**
     * Initialize a new Game.
     */
    public function  __construct()
    {
        $this->setHeaders();
    }

    /**
     * Set the required headers for the game.
     */
    private function setHeaders()
    {
        // This fixes the issue that all the message where printed on one line.
        header('Content-Type: text/plain');
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
        if ($this->howManyPlayers() < static::MINIMAL_NUMBER_OF_PLAYERS) {
            $this->printMessage("ERROR: A Minimum of two players or more required.");
            return;
        }

        $this->createQuestions();

        do {
            $this->roll($this->rollDice());

            if (rand(0,9) == 7) {
                $notAWinner = $this->wrongAnswer();
            } else {
                $notAWinner = $this->correctAnswer();
            }

        } while ($notAWinner);
    }

    /**
     * Roll the game dice.
     *
     * @return int
     */
    private function rollDice(): int
    {
        return rand(0,5) + 1;
    }

    /**
     * Generate the game questions.
     */
	private function createQuestions(){
		 for ($i = 0; $i < 50; $i++) {
            array_push($this->popQuestions, "Pop Question " . $i);
            array_push($this->scienceQuestions, "Science Question " . $i);
            array_push($this->sportsQuestions, "Sports Question " . $i);
            array_push($this->rockQuestions, "Rock Question " . $i);
        }
	}

    /**
     * Add the game players
     *
     * @param string $playerName
     * @return bool
     */
	public function add(string $playerName): bool {
	   array_push($this->players, $playerName);
	   $this->places[$this->howManyPlayers()] = 0;
	   $this->purses[$this->howManyPlayers()] = 0;
	   $this->inPenaltyBox[$this->howManyPlayers()] = false;

	    $this->printMessage($playerName . " was added");
	    $this->printMessage("They are player number " . count($this->players));
		return true;
	}

    /**
     *
     *
     * @return int
     */
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

	private function correctAnswer() {
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
