Feature: Standings API
  In order to get standings
  As an API client
  I need to be able to filter standings by competitions date

  Background:
    Given the following teams exist:
      | Name    | Rank| Matches played | Points | Wins | Losses | Draws |
      | FooTeam | 1   | 9              | 20     | 4    | 3      | 2     |
      | BarTeam | 2   | 7              | 21     | 3    | 3      | 1     |
      | BugTeam | 4   | 9              | 28     | 4    | 3      | 2     |
      | DugTeam | 3   | 8              | 22     | 4    | 4      | 0     |
    Given the following competitions exist:
      | start_at   | score | home_team_name | away_team_name |
      | 2012-04-28 | 1-2   | FooTeam        | BarTeam        |
      | 2012-04-29 | 3-2   | BugTeam        | DugTeam        |

  Scenario: Json response of available teams
    Given I am on "/api/standings"
    Then the response status code should be 200
    And the "Content-Type" header should be "application/json"
    And the response should contain 4 items

  Scenario: Proper 400 error on filtering by invalid date format
    Given I am on "/api/standings?from=2012:04:28&to=2012:04:29"
    Then the response status code should be 400
    And the "Content-Type" header should be "application/json"
    And the response should be valid json
    And the "error" property should exist
    And the "error" property should equal "Please, provide a valid date format like Y-m-d"

  Scenario Outline: Json response of filtered teams by competitions date
    Given I am on "/api/standings?from=<from>&to=<to>"
    Then the response status code should be 200
    And the "Content-Type" header should be "application/json"
    And the response should be valid json
    And the response should contain <countItems> items

    Examples:
    | from       | to         | countItems |
    | 2012-04-28 | 2012-04-29 | 4          |
    | 2012-04-29 | 2012-04-30 | 2          |
    | 2012-04-30 | 2012-05-01 | 0          |
