Feature: Search
  In order to filter competitions
  As a website user
  I need to be able to search for competitions by team name

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

  #@fixtures
  Scenario: List of all available competitions
    Given I am on "/"
    Then I should see 2 competitions
    And I should see "FooTeam"
    And I should see "BarTeam"
    And I should see "BugTeam"
    And I should see "DugTeam"

  Scenario Outline: Filter competitions by team name
    Given I am on "/"
    When I fill in the search box with "<keyword>"
    And I press the search button
    #And print last response
    Then I should see "<result>"

    Examples:
      | keyword | result                              |
      | FooTeam | FooTeam                             |
      | XBox    | No competitions found!!!            |
      | X       | Please, provide min 2 characters!!! |
