<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\FetchSoccerWayDataCommand;
use AppBundle\Entity\Competition;
use AppBundle\Entity\Team;
use AppBundle\Repository\TeamRepository;
use AppBundle\Service\SoccerWayService;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class FetchSoccerWayDataCommandTest
 */
class FetchSoccerWayDataCommandTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SoccerWayService
     */
    private $soccerWayService;

    public function setUp()
    {
        self::bootKernel();

        $this->truncateEntities();

        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->soccerWayService = $this->prophesize(SoccerWayService::class);
    }

    public function testOutputOfExecuteWithEmptyFetchedData()
    {
        $this->soccerWayService
            ->fetchCompetitionsData()
            ->shouldBeCalledTimes(1)
            ->willReturn([]);

        $commandTester = $this->executeCommand(
            $this->getEntityManager(),
            $this->soccerWayService->reveal()
        );

        $output = $commandTester->getDisplay();
        $this->assertContains('Fetching competitions and teams data from external resource SoccerWay.com ....', $output);
        $this->assertContains('No competitions found!!!', $output);
        $this->assertNotContains('Importing competitions and teams data into database ....', $output);
    }

    public function testOutputOfExecuteWithMockedFetchedData()
    {
        $this->soccerWayService
            ->fetchCompetitionsData()
            ->shouldBeCalledTimes(1)
            ->willReturn($this->getMockFetchedData());

        $commandTester = $this->executeCommand(
            $this->getEntityManager(),
            $this->soccerWayService->reveal()
        );

        $output = $commandTester->getDisplay();
        $this->assertContains('Fetching competitions and teams data from external resource SoccerWay.com ....', $output);
        $this->assertNotContains('No competitions found!!!', $output);
        $this->assertContains('Importing competitions and teams data into database ....', $output);
        $this->assertContains('Added 2 competitions, nice one.', $output);
        $this->assertContains('Added 3 teams, nice one.', $output);
    }

    public function testPersistAndFlushTeamsAndCompetitionsWhenExecuteWithMockedFetchedData()
    {
        $teamRepository = $this->prophesize(TeamRepository::class);

        $this->em
            ->getRepository(Team::class)
            ->willReturn($teamRepository->reveal());

        $this->em
            ->getRepository(Team::class)
            ->shouldBeCalled();

        $this->em->persist(Argument::type(Team::class))
            ->shouldBeCalled();

        $this->em->persist(Argument::type(Competition::class))
            ->shouldBeCalled();

        $this->em->flush()
            ->shouldBeCalled();

        $this->soccerWayService
            ->fetchCompetitionsData()
            ->willReturn($this->getMockFetchedData());

        $this->executeCommand(
            $this->em->reveal(),
            $this->soccerWayService->reveal()
        );
    }

    public function testCountOfStoredTeamsAndCompetitionsWhenExecuteWithMockedFetchedData()
    {
        $this->soccerWayService
            ->fetchCompetitionsData()
            ->willReturn($this->getMockFetchedData());

        $this->executeCommand(
            $this->getEntityManager(),
            $this->soccerWayService->reveal()
        );

        $countTeams = (int) $this->getEntityManager()
            ->getRepository(Team::class)
            ->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(3, $countTeams, 'Amount of stored Teams is not the same');

        $countCompetitions = (int) $this->getEntityManager()
            ->getRepository(Competition::class)
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(2, $countCompetitions, 'Amount of stored Competitions is not the same');
    }

    /**
     * @return void
     */
    private function truncateEntities(): void
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEntityManager(): EntityManagerInterface
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     *
     * @param EntityManagerInterface $em
     * @param SoccerWayService $soccerWayService
     *
     * @return CommandTester
     */
    private function executeCommand(
        EntityManagerInterface $em,
        SoccerWayService $soccerWayService
    ): CommandTester {
        $application = new Application(self::$kernel);
        $application->add(new FetchSoccerWayDataCommand($em, $soccerWayService));

        $command = $application->find('fetch:soccerway:data');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        return $commandTester;
    }

    /**
     * @return array
     */
    private function getMockFetchedData(): array
    {
        return [
            [
                'home_team' => [
                    'Name' => 'FooTeam',
                    'Rank' => '1',
                    'Matches played' => '9',
                    'Points' => '28',
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
                    'Name' => 'FooTeam',
                    'Rank' => '1',
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
