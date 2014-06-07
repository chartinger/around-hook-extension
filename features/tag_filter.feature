Feature: Filter with Tags
  
  In order to hook only into specific scenarios
  As a tester
  I need the @AroundScenario hook to support tags

  Background: 
    Given a file named "behat.yml" with:
      """
      default:
        extensions:
          chartinger\Behat\AroundHookExtension\AroundHookExtension: ~
      """

  Scenario: Using a tag to apply the @AroundScenario hook only to scenarios with this tag
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php
      
      use Behat\Behat\Context\Context;
      use Behat\Gherkin\Node\PyStringNode,
          Behat\Gherkin\Node\TableNode;
      use chartinger\Behat\AroundHookExtension\AroundScenarioScope;
      
      class FeatureContext implements Context
      {
          /**
           * @AroundScenario @noexec
           */
          public function aroundScenario(AroundScenarioScope $scope)
          {
            // No execution
          }
           
          /**
           * @When /I press any key/
           */
          public function iPressAnyKey() { }
      
          /**
           * @Then /I should see "([^"]*)"/
           */
          public function iShouldSee($text) { }
      }
      """
    And a file named "features/run.feature" with:
      """
      Feature: run scenarios
      
      @noexec
      Scenario:
        When I press any key
        Then I should see "nothing?"
      
      Scenario:
        When I press any key
        Then I should see "Hello World"
      """
    When I run "behat --no-colors -f pretty"
    Then it should pass with:
      """
      Feature: run scenarios
      
        Scenario:                         # features/run.feature:8
          When I press any key            # FeatureContext::iPressAnyKey()
          Then I should see "Hello World" # FeatureContext::iShouldSee()
      
      1 scenario (1 passed)
      2 steps (2 passed)
      """
