Feature: Title Suffix
  
  In order to distinguish between multiple executions
  As a tester
  I should be able to define a suffix for the scenario title

  Background: 
    Given a file named "behat.yml" with:
      """
      default:
        extensions:
          chartinger\Behat\AroundHookExtension\AroundHookExtension: ~
      """

  Scenario: Use suffix to distinguish executions
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
           * @AroundScenario
           */
          public function aroundScenario(AroundScenarioScope $scope)
          {
            $scope->callBlock(" [First Call]");
            $scope->callBlock(" [Second Call]");
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
      
      Scenario: Sample
        When I press any key
        Then I should see "Hello World"
      """
    When I run "behat --no-colors -f pretty"
    Then it should pass with:
      """
      Feature: run scenarios
      
        Scenario: Sample [First Call]     # features/run.feature:3
          When I press any key            # FeatureContext::iPressAnyKey()
          Then I should see "Hello World" # FeatureContext::iShouldSee()
      
        Scenario: Sample [Second Call]    # features/run.feature:3
          When I press any key            # FeatureContext::iPressAnyKey()
          Then I should see "Hello World" # FeatureContext::iShouldSee()
      
      2 scenarios (2 passed)
      4 steps (4 passed)
      """
