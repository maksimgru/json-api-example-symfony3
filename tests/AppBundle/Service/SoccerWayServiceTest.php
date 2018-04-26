<?php

namespace Tests\AppBundle\Command;

use AppBundle\Service\DomCrawlerService;
use AppBundle\Service\HttpClientInterface;
use AppBundle\Service\SoccerWayService;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class SoccerWayServiceTest
 */
class SoccerWayServiceTest extends KernelTestCase
{
    /**
     * @var HttpClientInterface $httpClient
     */
    private $httpClient;

    public function setUp()
    {
        self::bootKernel();

        $this->httpClient = $this->prophesize(HttpClientInterface::class);
    }

    public function testFetchTeamDataFromRawHtml()
    {
        $this->httpClient
            ->get(Argument::type('string'), Argument::withEntry('type', 'html'))
            ->shouldBeCalled()
            ->willReturn(
                [
                    'content' => $this->getMockRawHtmlTeamData(),
                    'type'    => 'html'
                ]
            );

        $soccerWayService = new SoccerWayService($this->httpClient->reveal(), $this->getDomCrawler());

        $teamData = $soccerWayService->fetchTeamData(Argument::type('string'));

        $this->assertArrayHasKey('Name', $teamData, 'Not exist "Name"!!!');
        $this->assertEquals('Wigan Athletic FC', $teamData['Name']);

        $this->assertArrayHasKey('Rank', $teamData, 'Not exist "Rank"!!!');
        $this->assertEquals('1', $teamData['Rank']);

        $this->assertArrayHasKey('Matches played', $teamData, 'Not exist "Matches played"!!!');
        $this->assertEquals('44', $teamData['Matches played']);

        $this->assertArrayHasKey('Points', $teamData, 'Not exist "Points"!!!');
        $this->assertEquals('94', $teamData['Points']);

        $this->assertArrayHasKey('Wins', $teamData, 'Not exist "Wins"!!!');
        $this->assertEquals('28', $teamData['Wins']);

        $this->assertArrayHasKey('Losses', $teamData, 'Not exist "Losses"!!!');
        $this->assertEquals('6', $teamData['Losses']);

        $this->assertArrayHasKey('Draws', $teamData, 'Not exist "Draws"!!!');
        $this->assertEquals('10', $teamData['Draws']);
    }

    public function testFetchCompetitionsDataFromRawHtml()
    {
        $this->httpClient
            ->get(Argument::type('string'), Argument::withEntry('type', 'html'))
            ->shouldBeCalled()
            ->willReturn(
                [
                    'content' => $this->getMockRawHtmlCompetitionsData(),
                    'type'    => 'html'
                ]
            );

        $soccerWayService = new SoccerWayService($this->httpClient->reveal(), $this->getDomCrawler());

        $competitionsData = $soccerWayService->fetchCompetitionsData();

        $this->assertCount(2, $competitionsData);

        $this->assertArrayHasKey('start_at', $competitionsData[0]);
        $this->assertEquals('28/04/12', $competitionsData[0]['start_at']);
        $this->assertArrayHasKey('start_at', $competitionsData[1]);
        $this->assertEquals('29/04/12', $competitionsData[1]['start_at']);

        $this->assertArrayHasKey('score', $competitionsData[0]);
        $this->assertEquals('4 - 0', $competitionsData[0]['score']);
        $this->assertArrayHasKey('score', $competitionsData[1]);
        $this->assertEquals('4 - 0', $competitionsData[1]['score']);

        $this->assertArrayHasKey('home_team', $competitionsData[0]);
        $this->assertArrayHasKey('away_team', $competitionsData[0]);
        $this->assertArrayHasKey('home_team', $competitionsData[1]);
        $this->assertArrayHasKey('away_team', $competitionsData[1]);
    }

    /**
     * @return DomCrawlerService
     */
    private function getDomCrawler(): DomCrawlerService
    {
        return self::$kernel->getContainer()
            ->get('test.' . DomCrawlerService::class);
    }

    /**
     * @return string
     */
    private function getMockRawHtmlTeamData(): string
    {
        $html = <<<HEREDOC
            <!DOCTYPE html>
            <html>
            <head></head>
            <body>
            <div class="content">
            <div id="subheading"><h1>Wigan Athletic FC</h1></div>
            <div class="block  clearfix block_general_statistics-wrapper" id="page_team_1_block_team_statistics_3_block_general_statistics_1-wrapper">
            <div class="content">
            <div class="block_general_statistics real-content clearfix " id="page_team_1_block_team_statistics_3_block_general_statistics_1">
            <table class="table compare">
            <thead>
            <tr class="sub-head">
            <th>&nbsp;</th>
            <th>Total</th>
            <th>Home</th>
            <th>Away</th>
            </tr>
            </thead>
            <tbody>
            <tr class="first odd">
            <th>Rank</th>
            <td class="team_a_col total">1</td>
            <td class="team_a_col home">&nbsp;
            </td><td class="team_a_col away">&nbsp;
            </td></tr>  
            <tr class="first even">
            <th>Matches played</th>
            <td class="team_a_col total">44</td>
            <td class="team_a_col home">22
            </td><td class="team_a_col away">22
            </td></tr>  
            <tr class="first odd">
            <th>Wins</th>
            <td class="team_a_col total">28</td>
            <td class="team_a_col home">13
            </td><td class="team_a_col away">15
            </td></tr>  
            <tr class="first even">
            <th>Draws</th>
            <td class="team_a_col total">10</td>
            <td class="team_a_col home">7
            </td><td class="team_a_col away">3
            </td></tr>  
            <tr class="first odd">
            <th>Losses</th>
            <td class="team_a_col total">6</td>
            <td class="team_a_col home">2
            </td><td class="team_a_col away">4
            </td></tr>  
            <tr class="first even">
            <th>Goals for</th>
            <td class="team_a_col total">87</td>
            <td class="team_a_col home">36
            </td><td class="team_a_col away">51
            </td></tr>  
            <tr class="first odd">
            <th>Goals against</th>
            <td class="team_a_col total">28</td>
            <td class="team_a_col home">10
            </td><td class="team_a_col away">18
            </td></tr>  
            <tr class="first even">
            <th>Points</th>
            <td class="team_a_col total">94</td>
            <td class="team_a_col home">46
            </td><td class="team_a_col away">48
            </td></tr>  
            <tr class="first odd">
            <th>Clean sheets</th>
            <td class="team_a_col total">26</td>
            <td class="team_a_col home">16
            </td><td class="team_a_col away">10
            </td></tr>  
            <tr class="first even">
            <th>Avg. goals scored p/m</th>
            <td class="team_a_col total">1.98</td>
            <td class="team_a_col home">1.64
            </td><td class="team_a_col away">2.32
            </td></tr>  
            <tr class="first odd">
            <th>Avg. goals conceded p/m</th>
            <td class="team_a_col total">0.64</td>
            <td class="team_a_col home">0.45
            </td><td class="team_a_col away">0.82
            </td></tr>  
            <tr class="first even">
            <th>Avg. time 1st goal scored</th>
            <td class="team_a_col total">38m</td>
            <td class="team_a_col home">29m
            </td><td class="team_a_col away">46m
            </td></tr>  
            <tr class="first odd">
            <th>Avg. time 1st goal conced.</th>
            <td class="team_a_col total">32m</td>
            <td class="team_a_col home">26m
            </td><td class="team_a_col away">36m
            </td></tr>  
            <tr class="first even">
            <th>Failed to score</th>
            <td class="team_a_col total">7</td>
            <td class="team_a_col home">6
            </td><td class="team_a_col away">1
            </td></tr>  
            <tr class="first odd">
            <th>Biggest victory</th>
            <td class="team_a_col total"><a href="/matches/2017/12/23/england/league-one/oxford-united-fc/wigan-athletic-football-club/2468211/" title="Oxford United">7 - 0</a></td>
            <td class="team_a_col home"><a href="/matches/2018/04/07/england/league-one/wigan-athletic-football-club/milton-keynes-dons-fc/2468445/" title="Milton Keynes Dons">5 - 1</a>
            </td><td class="team_a_col away"><a href="/matches/2017/12/23/england/league-one/oxford-united-fc/wigan-athletic-football-club/2468211/" title="Oxford United">7 - 0</a>
            </td></tr>  
            <tr class="first even">
            <th>Biggest defeat</th>
            <td class="team_a_col total"><a href="/matches/2018/02/13/england/league-one/wigan-athletic-football-club/blackpool-fc/2468337/" title="Blackpool">0 - 2</a></td>
            <td class="team_a_col home"><a href="/matches/2018/02/13/england/league-one/wigan-athletic-football-club/blackpool-fc/2468337/" title="Blackpool">0 - 2</a>
            </td><td class="team_a_col away"><a href="/matches/2018/02/10/england/league-one/southend-united-fc/wigan-athletic-football-club/2468325/" title="Southend United">1 - 3</a>
            </td></tr>  
            </tbody>
            </table>
            </div>
            </div>
            </div>
            </div>
            </body>
            </html>
HEREDOC;

        return $html;
    }

    /**
     * @return string
     */
    private function getMockRawHtmlCompetitionsData(): string
    {
        $html = <<<HEREDOC
            <!DOCTYPE html>
            <html>
            <head></head>
            <body>
            <div class="content">
            <div class="block_competition_matches">
            <table class="matches">
            <thead>
            <tr class="sub-head">
            <th class="day">Day</th>
            <th class="date">Date</th>
            <th class="team team-a">Home team</th>
            <th class="score-time">Score/Time</th>
            <th class="team team-b">Away team</th>
            <th class="events-button button">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr class="even  expanded match no-date-repetition" data-timestamp="1335621600" id="page_competition_1_block_competition_matches_5_match-1117398" data-competition="8">
            <td class="day no-repetition">Sat</td>
            <td class="date no-repetition">28/04/12</td>
            <td class="team team-a ">
              <a href="/teams/england/wigan-athletic-football-club/686/" title="Wigan Athletic">
                Wigan Athletic
              </a>
            </td>
            <td class="score-time score">
            <a href="/matches/2012/04/28/england/premier-league/wigan-athletic-football-club/newcastle-united-football-club/1117398/?ICID=PL_MS_01">
            4 - 0
            </a>
            </td>
            <td class="team team-b ">
            <a href="/teams/england/newcastle-united-football-club/664/" title="Newcastle United">
              Newcastle United
            </a>
            </td>
            <td class="events-button button first-occur">
            <a href="/matches/2012/04/28/england/premier-league/wigan-athletic-football-club/newcastle-united-football-club/1117398/#events" title="View events" class="events-button-button ">View events</a>
            </td>
            <td class="info-button button">
              <a href="/matches/2012/04/28/england/premier-league/wigan-athletic-football-club/newcastle-united-football-club/1117398/" title="More info">More info</a>
            </td>
            </tr>
            <tr class="odd  expanded    match no-date-repetition" data-timestamp="1335621600" id="page_competition_1_block_competition_matches_5_match-1117390" data-competition="8">
            <td class="day no-repetition"></td>
            <td class="date no-repetition">29/04/12</td>
            <td class="team team-a ">
              <a href="/teams/england/everton-football-club/674/" title="Everton">
                Everton
              </a>
            </td>
            <td class="score-time score">
            <a href="/matches/2012/04/28/england/premier-league/everton-football-club/fulham-football-club/1117390/?ICID=PL_MS_02">
            4 - 0
            </a>
            </td>
            <td class="team team-b ">
            <a href="/teams/england/fulham-football-club/667/" title="Fulham">
              Fulham
            </a>
            </td>
            <td class="events-button button first-occur">
            <a href="/matches/2012/04/28/england/premier-league/everton-football-club/fulham-football-club/1117390/#events" title="View events" class="events-button-button ">View events</a>
            </td>
            <td class="info-button button">
            <a href="/matches/2012/04/28/england/premier-league/everton-football-club/fulham-football-club/1117390/" title="More info">More info</a>
            </td>
            </tr>
            </tbody>
            </table>
            </div>
            </div>
            </body>
            </html>
HEREDOC;

        return $html;
    }
}
