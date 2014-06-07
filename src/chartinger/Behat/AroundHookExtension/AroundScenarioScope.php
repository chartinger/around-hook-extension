<?php

namespace chartinger\Behat\AroundHookExtension;

use Behat\Behat\Hook\Scope\ScenarioScope;
use Behat\Testwork\Environment\Environment;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\ScenarioNode;

class AroundScenarioScope implements ScenarioScope
{
  const AROUND = "feature.around";
  /**
   *
   * @var Environment
   */
  private $environment;
  /**
   *
   * @var FeatureNode
   */
  private $feature;
  /**
   *
   * @var ScenarioNode
   */
  private $scenario;
  
  private $block;

  public function __construct(Environment $env, FeatureNode $feature, ScenarioNode $scenario, ScenarioBlock $block)
  {
    $this->environment = $env;
    $this->feature = $feature;
    $this->scenario = $scenario;
    $this->block = $block;
  }

  public function getEnvironment()
  {
    return $this->environment;
  }

  public function getSuite()
  {
    return $this->environment->getSuite();
  }

  public function getName()
  {
    return self::AROUND;
  }

  public function getFeature()
  {
    return $this->feature;
  }

  public function getScenario()
  {
    return $this->scenario;
  }

  public function getBlock()
  {
    return $this->block;
  }
  
  public function callBlock($suffix = null)
  {
    $this->block->call($suffix);
  }
}