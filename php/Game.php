<?php

include 'Player.php';
include 'Question.php';

/**
 * Represent the game logic.
 */
class Game {

    /**
     * The minimum required number of players.
     *
     * @var int MINIMAL_NUMBER_OF_PLAYERS
     */
    private const MINIMAL_NUMBER_OF_PLAYERS = 2;

    /**
     * The number of questions to generate per category.
     *
     * @var int NUMBER_OF_QUESTION_PER_CATEGORY
     */
    private const NUMBER_OF_QUESTION_PER_CATEGORY = 50;

    /**
     * The competing players.
     *
     * @var Player[] $players
     */
    private array $players               = [];

    /**
     * The positions of the players.
     *
     * @var array $playerPositions
     */
    private array $playerPositions       = [];

    /**
     * The purses holding the earnings of the players.
     *
     * @var int[] $playerPurses
     */
    private array $playerPurses           = [];

    /**
     * The player(s) in the penalty box.
     *
     * @var Player[] $penaltyBox
     */
    private array $penaltyBox             = [];

    /**
     * The list of questions by question category.
     *
     * @var Question[] $questions
     */
    private array $questions                = [];

    /**
     * Represent the player who has to play a turn.
     *
     * @var null|Player
     */
    private ?\Player $currentPlayer;

    /**
     * Flag that signals that the player will be released.
     *
     * @var bool
     */
    private ?bool $isGettingOutOfPenaltyBox = null;

    /**
     * When this variable holds a Player instance. the player has won the game.
     *
     * @var null|Player
     */
    private ?Player $winner = null;

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
     * Print a message on screen.
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
        $minimalNumberOfPlayers = static::MINIMAL_NUMBER_OF_PLAYERS;

        if ($this->getNumberOfPlayers() < $minimalNumberOfPlayers) {
            $this->printMessage("ERROR: A Minimum of {$minimalNumberOfPlayers} players or more required.");
            exit(1);
        }

        $this->createCategoryQuestions();

        do {
            $this->printMessage('-------------------------------------------------------------------');
            $this->currentPlayer = $this->getNextPlayer();
            $this->playTurn($this->rollDice());

            // Do this different.
            if ($this->generateAnswer()) {
                $this->incorrectlyAnswered();
            } else {
                $this->correctlyAnswered();
            }
            $this->printMessage('-------------------------------------------------------------------');
        } while (is_null($this->winner));

        $this->reset();
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
	private function createCategoryQuestions()
    {
	    // Initialize a list per category.
	    $this->questions[Question::QUESTION_CATEGORY_POP]         = [];
	    $this->questions[Question::QUESTION_CATEGORY_SCIENCE]     = [];
	    $this->questions[Question::QUESTION_CATEGORY_SPORTS]      = [];
	    $this->questions[Question::QUESTION_CATEGORY_ROCK]        = [];

		 for ($i = 1; $i <= static::NUMBER_OF_QUESTION_PER_CATEGORY; $i++) {
            $this->questions[Question::QUESTION_CATEGORY_POP][] = new Question(
                $i,
                Question::QUESTION_CATEGORY_POP,
                "Pop Question"
            );

             $this->questions[Question::QUESTION_CATEGORY_SCIENCE][] = new Question(
                 $i,
                 Question::QUESTION_CATEGORY_SCIENCE,
                 "Science Question"
             );

             $this->questions[Question::QUESTION_CATEGORY_SPORTS][] = new Question(
                 $i,
                 Question::QUESTION_CATEGORY_SPORTS,
                 "Sports Question"
             );

             $this->questions[Question::QUESTION_CATEGORY_ROCK][] = new Question(
                 $i,
                 Question::QUESTION_CATEGORY_ROCK,
                 "Rock Question"
             );
        }
	}

    /**
     * Add the game players.
     *
     * @param string $playerName The name of the player to add.
     */
	public function addPlayer(string $playerName)
    {
        $player = null;

        try {
            $player = Player::createPlayer(count($this->players)+1, $playerName);
        } catch (Throwable $error) {
            $this->printMessage("ERROR: Could not add player, reason: '{$error->getMessage()}'");
            exit(1);
        }

        $this->players[$player->getIdentifier()] = $player;

        // Initialize begin values for the given player.
        $this->setPlayerPosition($player, 0);
        $this->setPlayerEarnings($player, 0);
	}

    /**
     * Set the position of the given player.
     *
     * @param Player $player The player to position.
     * @param int $position The position to place the player.
     * @param int $roll The result of throwing dices.
     */
	private function setPlayerPosition(Player $player, int $position, int $roll = 0)
    {
        $this->playerPositions[$player->getIdentifier()] = $position;

        // Create a new player position by combining the current position and the roll.
        $newPlayerPosition = $position + $roll;

        // If the new position reaches over the 11th position, correct this by subtracting 12 positions.
        // @todo: check the subtraction part.
        $this->playerPositions[$player->getIdentifier()] =
            $newPlayerPosition > 11
                ? $newPlayerPosition - 12
                : $newPlayerPosition;
    }

    /**
     * Get the position for the given player.
     *
     * @param Player $player The player to get the position for.
     * @return int
     */
    private function getPlayerPosition(Player $player): int
    {
        return $this->playerPositions[$player->getIdentifier()];
    }

    /**
     * Set the earning for the given player
     *
     * @param Player $player The player who earned coins
     * @param int $numberOfCoins The number of coins earned by the player.
     */
    private function setPlayerEarnings(Player $player, int $numberOfCoins)
    {
        if (!isset($this->playerPurses[$player->getIdentifier()])) {
            $this->playerPurses[$player->getIdentifier()] = 0;
        }

        $this->playerPurses[$player->getIdentifier()] += $numberOfCoins;
    }

    /**
     * Get the earnings of the given player.
     *
     * @param Player $player The player to get the earnings for.
     * @return int
     */
    public function getPlayerEarnings(Player $player): int
    {
        return $this->playerPurses[$player->getIdentifier()];
    }

    /**
     * Get the number of players competing players.
     *
     * @return int
     */
	private function getNumberOfPlayers(): int
    {
		return count($this->players);
	}

    /**
     * Run a players turn.
     *
     * @param int $roll The number rolled by the dice.
     */
	private function playTurn(int $roll)
    {
	    $currentPlayer = $this->currentPlayer;

		$this->printMessage($currentPlayer->getName() . " is the current player");
		$this->printMessage("They have rolled a " . $roll);

		if ($this->isPlayerInPenaltyBox($currentPlayer)) {
			if ($this->needToReleasePenaltyBoxPlayer($roll)) {
				$this->isGettingOutOfPenaltyBox = true;

				$this->printMessage($currentPlayer->getName() . " is getting out of the penalty box");
				$this->releasePlayerFromPenaltyBox($this->currentPlayer);


                $this->setPlayerPosition(
                    $currentPlayer,
                    $this->getPlayerPosition($currentPlayer),
                    $roll
                );

				$this->printMessage($currentPlayer->getName() . "'s new location is " . $this->getPlayerPosition($currentPlayer));
				$this->printMessage("The category is " . $this->currentCategory());
				$this->asksQuestion();
			} else {
				$this->printMessage($currentPlayer->getName() . " is not getting out of the penalty box");
				$this->isGettingOutOfPenaltyBox = false;
            }

		} else {

            $this->setPlayerPosition(
                $currentPlayer,
                $this->getPlayerPosition($currentPlayer),
                $roll
            );

            $this->printMessage("{$currentPlayer->getName()} 's new location is {$this->getPlayerPosition($currentPlayer)}");
            $this->printMessage("The category is " . $this->currentCategory());
            $this->asksQuestion();
        }
	}

    /**
     * Release the player from the penalty box.
     *
     * @param Player $player The player to release.
     */
    private function movePlayerIntoPenaltyBox(Player $player)
    {
        $this->penaltyBox[$player->getIdentifier()] = true;
    }

    /**
     * Release the player from the penalty box.
     *
     * @param Player $player The player to release.
     */
    private function releasePlayerFromPenaltyBox(Player $player)
    {
        unset($this->penaltyBox[$player->getIdentifier()]);
    }

    /**
     * Check if the given player is in the penalty box.
     *
     * @param Player $player The player to check.
     * @return bool
     */
	private function isPlayerInPenaltyBox(Player $player): bool
    {
        return array_key_exists($player->getIdentifier(), $this->penaltyBox);
    }

    /**
     * Determine if the player should be release from the penalty box.
     *
     * @param int $roll The roll
     * @return bool
     */
    private function needToReleasePenaltyBoxPlayer(int $roll): bool
    {
        return $roll % 2 != 0;
    }

    /**
     * Get the next player.
     *
     * Instead of keeping track of the player identifier, rotate the players.
     *
     * @return Player
     */
	private function getNextPlayer(): Player
    {
        if (!isset($this->currentPlayer)) {
            $player = array_shift($this->players);
        } else {
            $previousPlayer = $this->currentPlayer;
            $this->players[] = $previousPlayer;
            $player = array_shift($this->players);
        }

        return $player;
    }

    /**
     * The the current game question category.
     *
     * @return string
     */
	private function currentCategory(): string
    {

	    switch ($this->getPlayerPosition($this->currentPlayer)) {
            case 0:
            case 4:
            case 8:
                $category = "Pop";
                break;
            case 1:
            case 5:
            case 9:
                $category = "Science";
                break;
            case 2:
            case 6:
            case 10:
                $category = "Sports";
                break;
            default:
                $category = "Rock";
        }

        return $category;
	}

    /**
     * Ask the questions.
     */
    private function asksQuestion()
    {
        $question = null;

        switch ($this->currentCategory()) {
            case "Pop":
                $question = array_shift($this->questions[Question::QUESTION_CATEGORY_POP]);
                break;
            case "Science":
                $question = array_shift($this->questions[Question::QUESTION_CATEGORY_SCIENCE]);
                break;
            case "Sports":
                $question = array_shift($this->questions[Question::QUESTION_CATEGORY_SPORTS]);
                break;
            case "Rock":
                $question = array_shift($this->questions[Question::QUESTION_CATEGORY_ROCK]);
                break;
        }

        if (!$question) {
            /** @var Question $question */
            $this->printMessage("{$question->getPhrase()} {$question->getNumber()}");
        }
    }

    /**
     * Generate an answer.
     *
     * @return bool
     */
	private function generateAnswer(): bool
    {
        return rand(0,9) === 7;
    }

    /**
     * Determines if the question of answered correctly.
     */
	private function correctlyAnswered()
    {
	    $isPlayerInPenaltyBox = $this->isPlayerInPenaltyBox($this->currentPlayer);
	    $isPlayerReleased     = $this->isGettingOutOfPenaltyBox;

	    // Correct is:
        // Player is not in penaltybox
        // Player is in penaltybox but will be released.
	    $isAnswerCorrect = !$isPlayerInPenaltyBox || ($isPlayerInPenaltyBox && $isPlayerReleased);

	    if ($isAnswerCorrect) {
            $this->printMessage("Answer was correct!!!!");
            // Increase player earnings for correctly answering the question.
            $this->setPlayerEarnings($this->currentPlayer, 1);
	        $playerEarnings = $this->getPlayerEarnings($this->currentPlayer);
            $this->printMessage(
                 "{$this->currentPlayer->getName()} now has {$playerEarnings} Golden Coins."
            );

            $this->checkForWinner();
        }
	}

    /**
     * Determines if the question is answered incorrectly.
     */
	private function incorrectlyAnswered()
    {
		$this->printMessage("Question was incorrectly answered");
		$this->printMessage($this->currentPlayer->getName() . " was sent to the penalty box");
	    $this->movePlayerIntoPenaltyBox($this->currentPlayer);
	}

    /**
     * Check if the player has won.
     */
    private function checkForWinner()
    {
        if ($this->getPlayerEarnings($this->currentPlayer) === 6) {
            $this->winner = $this->currentPlayer;
        }
    }

    /**
     * Reset the game data.
     */
    private function reset()
    {
        $this->players                  = [];
        $this->playerPositions          = [];
        $this->playerPurses             = [];
        $this->penaltyBox               = [];
        $this->questions                = [];
        $this->currentPlayer            = null;
        $this->isGettingOutOfPenaltyBox = null;
        $this->winner                   = null;
    }
}
