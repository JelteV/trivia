<?php

/**
 * Represents a game player.
 */
class Player
{
    /**
     * The player identifier.
     *
     * @var int $identifier
     */
    private int $identifier;

    /**
     * The player name.
     *
     * @var string
     */
    private string $name;

    /**
     * Initialize new player instance.
     *
     * @param int $identifier The identifier for the player.
     * @param string $name The name of the player.
     */
    private function __construct(int $identifier, string $name)
    {
        $this->setIdentifier($identifier);
        $this->setName($name);
    }

    /**
     * Create a new player
     *
     * @param int $identifier The player identifier
     * @param string $name The player name.
     * @return Player The new player instance.
     */
    public static function createPlayer(int $identifier, string $name): Player
    {
        return new static($identifier, $name);
    }

    /**
     * Get the identifier for the player.
     *
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->identifier;
    }

    /**
     * Set the identifier for the player.
     *
     * @param int $identifier
     */
    private function setIdentifier(int $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Get the name of the player.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the player
     *
     * @param string $name
     */
    private function setName(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("ERROR: The name of a new player cannot be empty.");
        }

        $this->name = $name;
    }
}
