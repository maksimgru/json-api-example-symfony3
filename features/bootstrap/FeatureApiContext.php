<?php

use AppBundle\Entity\Competition;
use AppBundle\Entity\Team;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\DocumentElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\Assert;

require_once __DIR__.'/../../vendor/bin/.phpunit/phpunit-6.5/vendor/autoload.php';
require_once __DIR__.'/../../vendor/bin/.phpunit/phpunit-6.5/src/Framework/Assert/Functions.php';

/**
 * Defines application features from the specific context.
 */
class FeatureApiContext extends RawMinkContext
{
    private static $container;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @BeforeSuite
     */
    public static function bootstrapSymfony()
    {
        require_once __DIR__.'/../../vendor/autoload.php';
        require_once __DIR__.'/../../app/AppKernel.php';

        $kernel = new AppKernel('test', true);
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }

    /**
     * @BeforeScenario
     */
    public function clearData()
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * @Given the following teams exist:
     */
    public function theFollowingTeamsExist(TableNode $table)
    {
        $em = $this->getEntityManager();

        foreach($table as $row) {
            $this->persistTeam($em, $row);
        }

        $em->flush();
    }

    /**
     * @Given the following competitions exist:
     */
    public function theFollowingCompetitionsExist(TableNode $table)
    {
        $em = $this->getEntityManager();

        foreach($table as $row) {
            $this->persistCompetition($em, $row);
        }

        $em->flush();
    }

    /**
     * @Given /^the "([^"]*)" header should be "([^"]*)"$/
     */
    public function theHeaderShouldBe($headerName, $expectedHeaderValue)
    {
        assertEquals($expectedHeaderValue, $this->getHeader($headerName));
    }

    /**
     * @Then /^the response should be valid json$/
     */
    public function theResponseContainsValidJson()
    {
        $responseJson = $this->getResponseContent();

        Assert::assertJson($responseJson, 'Response contains invalid json');
    }

    /**
     * @Then /^the response should contain (\d+) item(?:|s)$/
     */
    public function theResponseContainsItems($count)
    {
        $responseJson = $this->getResponseContent();
        $responseDecoded = json_decode($this->getResponseContent());

        $message = sprintf(
            'Asserting the response contains [%d] items: %s',
            $count,
            $responseJson
        );

        Assert::assertCount((int)$count, $responseDecoded, $message);
    }

    /**
     * @Then /^the "([^"]*)" property should exist$/
     */
    public function thePropertyExists($property)
    {
        $responseJson = $this->getResponseContent();
        $responseDecoded = json_decode($this->getResponseContent());

        $message = sprintf(
            'Asserting the [%s] property exists in the response [%s]',
            $property,
            $responseJson
        );

        Assert::assertObjectHasAttribute($property, $responseDecoded, $message);
    }

    /**
     * @Then /^the "([^"]*)" property should equal "([^"]*)"$/
     */
    public function thePropertyEquals($property, $expectedValue)
    {
        $responseJson = $this->getResponseContent();
        $responseDecoded = json_decode($this->getResponseContent());
        $actualValue = $responseDecoded->$property;

        $message = sprintf(
            'Asserting the [%s] property in current scope equals [%s]: %s',
            $property,
            $expectedValue,
            $responseJson
        );

        Assert::assertEquals($expectedValue, $actualValue, $message);
    }

    /**
     * @return string
     */
    private function getResponseContent(): string
    {
        return $this->getSession()->getPage()->getContent();
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    private function getHeader($name): string
    {
        return $this->getSession()->getResponseHeader($name);
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager(): EntityManager
    {
        return self::$container->get('doctrine')->getManager();
    }

    /*
     * @param EntityManager $em
     * @param array $data
     *
     * @return Team
     */
    private function persistTeam(
        EntityManager $em,
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
     * @param EntityManager $em
     * @param array $data
     *
     * @return Competition
     */
    private function persistCompetition(
        EntityManager $em,
        array $data
    ): Competition
    {
        $home_team = $em->getRepository(Team::class)->findOneBy(['name' => $data['home_team_name']]);
        $away_team = $em->getRepository(Team::class)->findOneBy(['name' => $data['away_team_name']]);
        $formattedDate = \DateTime::createFromFormat('Y-m-d', $data['start_at']);

        $competition = (new Competition())
            ->setHomeTeam($home_team)
            ->setAwayTeam($away_team)
            ->setScore($data['score'])
            ->setStartAt($formattedDate);

        $em->persist($competition);

        return $competition;
    }
}
