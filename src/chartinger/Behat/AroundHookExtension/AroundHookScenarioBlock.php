<?php

namespace chartinger\Behat\AroundHookExtension;

use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Result\TestWithSetupResult;
use Behat\Behat\Tester\ScenarioTester;
use Behat\Behat\Tester\OutlineTester;
use Behat\Testwork\Environment\EnvironmentManager;
use Behat\Testwork\Environment\Environment;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\FeatureNode;
class AroundHookScenarioBlock implements ScenarioBlock 
{
  private $calls;
  private $results;
  private $scenarioTester;
  private $outlineTester;
  private $envManager;
  private $scenario;
  private $env;
  private $feature;
  private $skip;
  
  public function __construct(ScenarioInterface $scenario, Environment $env, FeatureNode $feature, $skip, ScenarioTester $scenarioTester, OutlineTester $outlineTester, EnvironmentManager $envManager)
  {
    $this->calls = 0;
    $this->results = array();
    $this->scenarioTester = $scenarioTester;
    $this->outlineTester = $outlineTester;
    $this->envManager = $envManager;
    $this->scenario = $scenario;
    $this->env = $env;
    $this->feature = $feature;
    $this->skip = $skip;
  }

  public function call()
  {
    $this->calls++;
    $scenario = $this->scenario;
    $feature = $this->feature;
    $skip = $this->skip;
    
    $isolatedEnvironment = $this->env;
    $tester = $scenario instanceof OutlineNode ? $this->outlineTester : $this->scenarioTester;
    
    $setup = $tester->setUp($isolatedEnvironment, $feature, $scenario, $skip);
    $localSkip = ! $setup->isSuccessful() || $skip;
    $testResult = $tester->test($isolatedEnvironment, $feature, $scenario, $localSkip);
    $teardown = $tester->tearDown($isolatedEnvironment, $feature, $scenario, $localSkip, $testResult);
    
    $integerResult = new IntegerTestResult($testResult->getResultCode());
    
    $result = new TestWithSetupResult($setup, $integerResult, $teardown);
    
    $this->results[] = $result;
  }

  public function getResults()
  {
    return $this->results;
  }

  public function getCalls()
  {
    return $this->calls;
  }

}