<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competition
 *
 * @ORM\Table(
 *     name="competition",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *              name="home_away_start_uniq",
 *              columns={"home_team_id", "away_team_id", "start_at"}
 *          )
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompetitionRepository")
 */
class Competition
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="home_team_id", referencedColumnName="id", nullable=false)
     */
    private $homeTeam;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="away_team_id", referencedColumnName="id", nullable=false)
     */
    private $awayTeam;

    /**
     * @ORM\Column(name="start_at", type="date", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(name="score", type="string", length=255, nullable=true)
     */
    private $score;


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
     * Set homeTeam.
     *
     * @param Team
     *
     * @return Competition
     */
    public function setHomeTeam(Team $team): Competition
    {
        $this->homeTeam = $team;

        return $this;
    }

    /**
     * Get homeTeam.
     *
     * @return Team
     */
    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    /**
     * Set awayTeam.
     *
     * @param Team
     *
     * @return Competition
     */
    public function setAwayTeam(Team $team): Competition
    {
        $this->awayTeam = $team;

        return $this;
    }

    /**
     * Get awayTeam.
     *
     * @return team
     */
    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    /**
     * Set startAt.
     *
     * @param \DateTime|null $startAt
     *
     * @return Competition
     */
    public function setStartAt($startAt = null): Competition
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt.
     *
     * @return \DateTime|null
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set score.
     *
     * @param string|null $score
     *
     * @return Competition
     */
    public function setScore($score = null): Competition
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score.
     *
     * @return string|null
     */
    public function getScore()
    {
        return $this->score;
    }
}
