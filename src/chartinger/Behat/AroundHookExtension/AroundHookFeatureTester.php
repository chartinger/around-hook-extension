<?php

namespace chartinger\Behat\AroundHookExtension;

use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Tester\SpecificationTester;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Result\TestWithSetupResult;
use Behat\Testwork\Tester\Result\TestResults;
use Behat\Behat\Tester\ScenarioTester;
use Behat\Behat\Tester\OutlineTester;
use Behat\Testwork\Environment\EnvironmentManager;
use Behat\Testwork\Hook\HookDispatcher;

class AroundHookFeatureTester implements SpecificationTester
{
  /**
   *
   * @var ScenarioTester
   */
  private $scenarioTester;
  /**
   *
   * @var OutlineTester
   */
  private $outlineTester;
  /**
   *
   * @var EnvironmentManager
   */
  private $envManager;

  /**
   * @var HookDispatcher
   */
  private $hookDispatcher;

  /**
   *
   * @var SpecificationTester
   */
  private $baseTester;
  
  
  public function __construct(SpecificationTester $baseTester, ScenarioTester $scenarioTester, OutlineTester $outlineTester, EnvironmentManager $envManager, HookDispatcher $hookDispatcher)
  {
    $this->baseTester = $baseTester;
    $this->scenarioTester = $scenarioTester;
    $this->outlineTester = $outlineTester;
    $this->envManager = $envManager;
    $this->hookDispatcher = $hookDispatcher;
    
  }
  
  /*
   * (non-PHPdoc) @see \Behat\Testwork\Tester\SpecificationTester::setUp()
   */
  public function setUp(Environment $env, $spec, $skip)
  {
    return $this->baseTester->setUp($env, $spec, $skip);
  }
  
  /*
   * (non-PHPdoc) @see \Behat\Testwork\Tester\SpecificationTester::test()
   */
  public function test(Environment $env, $feature, $skip)
  {
    $results = array ();
    foreach($feature->getScenarios() as $scenario)
    {
      $isolatedEnvironment = $this->envManager->isolateEnvironment($env, $scenario);
      $block = new AroundHookScenarioBlock($scenario, $isolatedEnvironment, $feature, $skip, $this->scenarioTester, $this->outlineTester, $this->envManager);
      
      $scope = new AroundScenarioScope($isolatedEnvironment, $feature, $scenario, $block);
      $hookCallResults = $this->hookDispatcher->dispatchScopeHooks($scope);
      foreach ($hookCallResults as $callResult) {
        echo $callResult->getStdOut();
        if ($callResult->hasException())
        {
          throw $callResult->getException();
        }
      }
      if (count($hookCallResults) == 0)
      {
        $block->call();
      }
      $results = array_merge($results, $block->getResults());
    }
    return new TestResults($results);
  }
  
  /*
   * (non-PHPdoc) @see \Behat\Testwork\Tester\SpecificationTester::tearDown()
   */
  public function tearDown(Environment $env, $spec, $skip, TestResult $result)
  {
    return $this->baseTester->tearDown($env, $spec, $skip, $result);
  }

}