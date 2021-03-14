<?php

/**
 * Represent a game question.
 */
class Question
{
    /**
     * The identifier for the game question category: "Pop"
     *
     * @var int QUESTION_CATEGORY_POP
     */
    public const QUESTION_CATEGORY_POP         = 1;

    /**
     * The identifier for the game question category: "Science"
     *
     * @var int QUESTION_CATEGORY_SCIENCE
     */
    public const QUESTION_CATEGORY_SCIENCE     = 2;

    /**
     * The identifier for the game question category: "Sports"
     *
     * @var int QUESTION_CATEGORY_SPORTS
     */
    public const QUESTION_CATEGORY_SPORTS      = 3;

    /**
     * The identifier for the game question category: "Rock"
     *
     * @var int QUESTION_CATEGORY_ROCK
     */
    public const QUESTION_CATEGORY_ROCK        = 4;

    /**
     * Initialize a new game question.
     *
     * @param int $number The question number.
     * @param int $category The question category identifier the question belongs to.
     * @param string $phrase The phrase question phrase.
     */
    public function __construct(int $number, int $category, string $phrase)
    {
        $this->setNumber($number);
        $this->setCategory($category);
        $this->setPhrase($phrase);
    }

    /**
     * The question number.
     *
     * @var int $number
     */
    private int $number;

    /**
     * The question phrase.
     *
     * @var string $phrase
     */
    private string $phrase;

    /**
     * The category identifier the question belongs to.
     *
     * @var int $category
     */
    private int $category;

    /**
     * Get the question number.
     *
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * Set the question number.
     *
     * @param int $number
     */
    private function setNumber(int $number)
    {
        if ($number < 1) {
            throw new InvalidArgumentException(
                "ERROR: Question number needs to be equals or greater than 1, got: {$number}."
            );
        }

        $this->number = $number;
    }

    /**
     * Get the question phrase.
     *
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * Set the question phrase.
     *
     * @param string $phrase
     */
    private function setPhrase(string $phrase)
    {
        if (empty($phrase)) {
            throw new InvalidArgumentException(
                "ERROR: It is not allowed for the question phrase to be empty."
            );
        }

        $this->phrase = $phrase;
    }

    /**
     * Get the question category identifier.
     *
     * @return int
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * Set the question category identifier.
     *
     * @param int $category
     */
    private function setCategory(int $category)
    {
        $possibilities = [
            static::QUESTION_CATEGORY_POP,
            static::QUESTION_CATEGORY_SCIENCE,
            static::QUESTION_CATEGORY_SPORTS,
            static::QUESTION_CATEGORY_ROCK
        ];

        if (!in_array($category, $possibilities, true)) {
            throw new InvalidArgumentException(
                "ERROR: Invalid question category identifier supplied, got: {$category}"
            );
        }

        $this->category = $category;
    }
}
