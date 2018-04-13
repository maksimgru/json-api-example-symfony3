<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection|Competition[]
     */
    private $competitions;

    /**
     * @ORM\Column(name="place", type="integer")
     */
    private $place;

    /**
     * @ORM\Column(name="played", type="integer")
     */
    private $played;

    /**
     * @ORM\Column(name="wins", type="integer")
     */
    private $wins;

    /**
     * @ORM\Column(name="draws", type="integer")
     */
    private $draws;

    /**
     * @ORM\Column(name="losses", type="integer")
     */
    private $losses;

    /**
     * @ORM\Column(name="points", type="integer")
     */
    private $points;

    /**
     * Create new Team instance.
     *
     */
    public function __construct()
    {
        $this->competitions = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName(string $name): Team
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set place.
     *
     * @param int $place
     *
     * @return Team
     */
    public function setPlace(int $place): Team
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return int
     */
    public function getPlace(): int
    {
        return $this->place;
    }

    /**
     * Set played.
     *
     * @param int $played
     *
     * @return Team
     */
    public function setPlayed(int $played): Team
    {
        $this->played = $played;

        return $this;
    }

    /**
     * Get played.
     *
     * @return int
     */
    public function getPlayed(): int
    {
        return $this->played;
    }

    /**
     * Set wins.
     *
     * @param int $wins
     *
     * @return Team
     */
    public function setWins(int $wins): Team
    {
        $this->wins = $wins;

        return $this;
    }

    /**
     * Get wins.
     *
     * @return int
     */
    public function getWins(): int
    {
        return $this->wins;
    }

    /**
     * Set draws.
     *
     * @param int $draws
     *
     * @return Team
     */
    public function setDraws(int $draws): team
    {
        $this->draws = $draws;

        return $this;
    }

    /**
     * Get draws.
     *
     * @return int
     */
    public function getDraws(): int
    {
        return $this->draws;
    }

    /**
     * Set losses.
     *
     * @param int $losses
     *
     * @return Team
     */
    public function setLosses(int $losses): Team
    {
        $this->losses = $losses;

        return $this;
    }

    /**
     * Get losses.
     *
     * @return int
     */
    public function getLosses(): int
    {
        return $this->losses;
    }

    /**
     * Set points.
     *
     * @param int $points
     *
     * @return Team
     */
    public function setPoints(int $points): Team
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points.
     *
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * Get competitions.
     *
     * @return ArrayCollection|Competition[]
     */
    public function getCompetitions(): ArrayCollection
    {
        return $this->competitions;
    }
}
