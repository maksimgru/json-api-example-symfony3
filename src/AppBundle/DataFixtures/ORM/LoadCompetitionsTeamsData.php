<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadTeamData
 *
 * @package AppBundle\DataFixtures\ORM
 */
class LoadCompetitionsTeamsData extends Fixture implements OrderedFixtureInterface
{
    /*
     * @param ObjectManager $em
     *
     * @return void
     */
    public function load(ObjectManager $em): void
    {
        $competitionsData = $this->getFixturesCompetitionsTeamsData();

        foreach($competitionsData as $competitionData) {
            $homeTeam = $this->persistTeam($em, $competitionData['home_team']);
            $awayTeam = $this->persistTeam($em, $competitionData['away_team']);

            $competitionData['home_team'] = $homeTeam;
            $competitionData['away_team'] = $awayTeam;

            $this->persistCompetition($em, $competitionData);
        }

        $em->flush();
    }

    /*
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }

    /*
     * @param ObjectManager $em
     * @param array $data
     *
     * @return Team
     */
    private function persistTeam(
        ObjectManager $em,
        array $data
    ): Team
    {
        $team = (new Team())
            ->setName($data['Name'])
            ->setPlace($data['Rank'])
            ->setPlayed($data['Matches played'])
            ->setPoints($data['Points'])
            ->setWins($data['Wins'])
            ->setLosses($data['Losses'])
            ->setDraws($data['Draws']);

        $em->persist($team);

        return $team;
    }

    /*
     * @param ObjectManager $em
     * @param array $data
     *
     * @return Competition
     */
    private function persistCompetition(
        ObjectManager $em,
        array $data
    ): Competition
    {
        $formattedDate = \DateTime::createFromFormat('d/m/y', $data['start_at']);

        $competition = (new Competition())
            ->setHomeTeam($data['home_team'])
            ->setAwayTeam($data['away_team'])
            ->setScore($data['score'])
            ->setStartAt($formattedDate);

        $em->persist($competition);

        return $competition;
    }

    /**
     * @return array
     */
    private function getFixturesCompetitionsTeamsData(): array
    {
        return [
            [
                'home_team' => [
                    'Name' => 'FooTeam',
                    'Rank' => '1',
                    'Matches played' => '9',
                    'Points' => '20',
                    'Wins' => '4',
                    'Losses' => '3',
                    'Draws' => '2',
                ],
                'away_team' => [
                    'Name' => 'BarTeam',
                    'Rank' => '2',
                    'Matches played' => '7',
                    'Points' => '21',
                    'Wins' => '3',
                    'Losses' => '3',
                    'Draws' => '1',
                ],
                'score'     => '1-2',
                'start_at'  => '28/04/12',
            ],
            [
                'home_team' => [
                    'Name' => 'BugTeam',
                    'Rank' => '4',
                    'Matches played' => '9',
                    'Points' => '28',
                    'Wins' => '4',
                    'Losses' => '3',
                    'Draws' => '2',
                ],
                'away_team' => [
                    'Name' => 'DugTeam',
                    'Rank' => '3',
                    'Matches played' => '8',
                    'Points' => '22',
                    'Wins' => '4',
                    'Losses' => '4',
                    'Draws' => '0',
                ],
                'score'     => '3-2',
                'start_at'  => '29/04/12',
            ],
        ];
    }
}
