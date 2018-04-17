<?php

namespace AppBundle\Command;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Team;
use AppBundle\Service\SoccerWayService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchSoccerWayDataCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var SoccerWayService $soccerWay
     */
    private $soccerWay;

    /**
     * @param EntityManagerInterface $em
     * @param SoccerWayService $soccerWay
     *
     * @throws LogicException
     */
    public function __construct (EntityManagerInterface $em, SoccerWayService $soccerWay)
    {
        parent::__construct();

        $this->em = $em;
        $this->soccerWay = $soccerWay;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('fetch:soccerway:data')
            ->setDescription('Fetch matches and team data from soccerway service.');
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): bool
    {
        $io = new SymfonyStyle($input, $output);

        // Fetch Data
        $io->title('Fetching competitions and teams data from external resource SoccerWay.com ....');
        $competitions = $this->soccerWay->fetchCompetitionsData();
        $competitionsCount = \count($competitions);
        $competitionsOutputTableData = [];
        $numberEntitiesAdded = ['competitions' => 0, 'teams' => 0];

        // If No Data
        if ($competitionsCount === 0) {
            $io->warning('No competitions found!!!');
            return false;
        }

        // Setup Data
        $io->title('Importing competitions and teams data into database ....');
        $io->progressStart($competitionsCount);

        foreach ($competitions as $competitionData) {
            // Setup Home Team
            $homeTeam = $this->em->getRepository(Team::class)->findOneBy(['name' => $competitionData['home_team']['Name']]);
            if (!$homeTeam) {
                $homeTeam = (new Team())
                    ->setName($competitionData['home_team']['Name'])
                    ->setPlace((int)$competitionData['home_team']['Rank'])
                    ->setPlayed((int)$competitionData['home_team']['Matches played'])
                    ->setPoints((int)$competitionData['home_team']['Points'])
                    ->setWins((int)$competitionData['home_team']['Wins'])
                    ->setLosses((int)$competitionData['home_team']['Losses'])
                    ->setDraws((int)$competitionData['home_team']['Draws'])
                ;
                $numberEntitiesAdded['teams']++;
                $this->em->persist($homeTeam);
                $this->em->flush();
            }

            // Setup Away Team
            $awayTeam = $this->em->getRepository(Team::class)->findOneBy(['name' => $competitionData['away_team']['Name']]);
            if (!$awayTeam) {
                $awayTeam = (new Team())
                    ->setName($competitionData['away_team']['Name'])
                    ->setPlace((int)$competitionData['away_team']['Rank'])
                    ->setPlayed((int)$competitionData['away_team']['Matches played'])
                    ->setPoints((int)$competitionData['away_team']['Points'])
                    ->setWins((int)$competitionData['away_team']['Wins'])
                    ->setLosses((int)$competitionData['away_team']['Losses'])
                    ->setDraws((int)$competitionData['away_team']['Draws'])
                ;
                $numberEntitiesAdded['teams']++;
                $this->em->persist($awayTeam);
                $this->em->flush();
            }

            // Setup Competition
            $competitionFormattedDate = \DateTime::createFromFormat('d/m/y', $competitionData['start_at']);
            $competition = (new Competition())
                ->setHomeTeam($homeTeam)
                ->setAwayTeam($awayTeam)
                ->setScore($competitionData['score'])
                ->setStartAt($competitionFormattedDate)
            ;
            $numberEntitiesAdded['competitions']++;
            $this->em->persist($competition);

            // Setup data for console table
            $competitionsOutputTableData[] = [
                'date'      => $competitionFormattedDate->format('Y-m-d'),
                'home_team' => $competitionData['home_team']['Name'],
                'score'     => $competitionData['score'],
                'away_team' => $competitionData['away_team']['Name'],
            ];

            $io->progressAdvance();
        }

        $io->progressFinish();

        // Output Table
        $table = new Table($output);
        $table
            ->setHeaders(['Date', 'Home Team', 'Score', 'Away Team'])
            ->setRows($competitionsOutputTableData)
        ;
        $table->render();

        $this->em->flush();

        // Finished Messages
        $io->success(sprintf('Added %d competitions, nice one.', $numberEntitiesAdded['competitions']));
        $io->success(sprintf('Added %d teams, nice one.', $numberEntitiesAdded['teams']));

        return true;
    }
}
