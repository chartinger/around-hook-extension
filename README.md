# @AroundScenario hook extension

Around hook support for Behat 3

This project aims to mimic the [Cucumber arround feature](https://github.com/cucumber/cucumber/wiki/Hooks) in Behat 3.x

## Example

Given you want to run the same Scenario with multiple screen resolutions it could look like this:
```php
...
  /**
   * @AroundScenario
   */
  public function runWithMultipleWindowSizes(AroundScenarioScope $scope)
  {
    $this->width = 1920;
    $scope->callBlock(" [1920]");
    $this->width = 800;
    $scope->callBlock(" [800]");
  }
  
  /**
   * @BeforeScenario
   */
  public function resizeWindow()
  {
    $this->getSession()->resizeWindow($this->width, $this->height);
  }
...
```
Where in the optional parameter of `callBlock()` you can define a suffix to the scenario title

Example output of Behat could be:
```Cucumber
...
  Scenario: Simpler Test [1920]
    Given I am on "http://somehomepage"
    When I do something
    Then i should see "Hello World"

  Scenario: Simpler Test [800]
    Given I am on "http://somehomepage"
    When I do something
    Then i should see "Hello World"

...
```

## Installation

In your `composer.json` add
```json
{
    "require": {
        ...
        "chartinger/around-hook-extension": "*@dev"
    }
}
```
and update your dependencies

## Usage

To activate this extension add this to your `behat.yml`
```YAML
default:
  extensions:
    chartinger\Behat\AroundHookExtension\AroundHookExtension: ~
```

You can now use the `@AroundScenario` annotation in your Behat Context
```php
  use chartinger\Behat\AroundHookExtension\AroundScenarioScope;
  
  ...
  
  /**
   * @AroundScenario
   */
  public function aroundScenario(AroundScenarioScope $scope)
  {
    $scope->callBlock();
  }
```
