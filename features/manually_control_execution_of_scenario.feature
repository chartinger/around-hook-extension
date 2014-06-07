Feature: Manually control the execution of a scenario
  
  In order to control the execution of a scenario
  As a tester
  I need to be able to hook around a scenario

  Background: 
    Given a file named "behat.yml" with:
      """
      default:
        extensions:
          chartinger\Behat\AroundHookExtension\AroundHookExtension: ~
      """

  Scenario: Using a hook to manually execute a scenario
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
            $scope->callBlock();
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
      
      Scenario:
        When I press any key
        Then I should see "Hello World"
      """
    When I run "behat --no-colors -f pretty"
    Then it should pass with:
      """
      Feature: run scenarios
      
        Scenario:                         # features/run.feature:3
          When I press any key            # FeatureContext::iPressAnyKey()
          Then I should see "Hello World" # FeatureContext::iShouldSee()
      
      1 scenario (1 passed)
      2 steps (2 passed)
      """

  Scenario: Not manually executing a scenario i hooked to will not execute it at all
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
      
      Scenario:
        When I press any key
        Then I should see "Hello World"
      """
    When I run "behat --no-colors -f pretty"
    Then it should pass with:
      """
      Feature: run scenarios
      
      No scenarios
      No steps
      """

  Scenario: A scenario may be executed multiple times
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
            $scope->callBlock();
            $scope->callBlock();
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
      
      Scenario:
        When I press any key
        Then I should see "Hello World"
      """
    When I run "behat --no-colors -f pretty"
    Then it should pass with:
      """
      Feature: run scenarios
      
        Scenario:                         # features/run.feature:3
          When I press any key            # FeatureContext::iPressAnyKey()
          Then I should see "Hello World" # FeatureContext::iShouldSee()
      
        Scenario:                         # features/run.feature:3
          When I press any key            # FeatureContext::iPressAnyKey()
          Then I should see "Hello World" # FeatureContext::iShouldSee()
      
      2 scenarios (2 passed)
      4 steps (4 passed)
      """

  Scenario: If a scenario is executed multiple times, all executions share the same environment
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php
      
      use Behat\Behat\Context\Context;
      use Behat\Gherkin\Node\PyStringNode,
          Behat\Gherkin\Node\TableNode;
      use chartinger\Behat\AroundHookExtension\AroundScenarioScope;
      
      class FeatureContext implements Context
      {
          private $counter;
          
          /**
           * @AroundScenario
           */
          public function aroundScenario(AroundScenarioScope $scope)
          {
            $this->counter = 1;
            $scope->callBlock();
            $this->counter++;
            $scope->callBlock();
          }
           
          /**
           * @When /I press any key/
           */
          public function iPressAnyKey() { }
      
          /**
           * @Then /I should see "([^"]*)"/
           */
          public function iShouldSee($text)
          {
            if ($text !== "Pressed " . $this->counter . " times") 
              throw new Exception("\"" . $text . "\" does not match " . "\"Pressed " . $this->counter . " times\"");
          }
      }
      """
    And a file named "features/run.feature" with:
      """
      Feature: run scenarios
      
      Scenario:
        When I press any key
        Then I should see "Pressed 1 times"
      """
    When I run "behat --no-colors -f pretty"
    Then it should fail with:
      """
      Feature: run scenarios
      
        Scenario:                             # features/run.feature:3
          When I press any key                # FeatureContext::iPressAnyKey()
          Then I should see "Pressed 1 times" # FeatureContext::iShouldSee()
      
        Scenario:                             # features/run.feature:3
          When I press any key                # FeatureContext::iPressAnyKey()
          Then I should see "Pressed 1 times" # FeatureContext::iShouldSee()
            "Pressed 1 times" does not match "Pressed 2 times" (Exception)
      
      --- Failed scenarios:
      
          features/run.feature:3
      
      2 scenarios (1 passed, 1 failed)
      4 steps (3 passed, 1 failed)
      """
