<?php

namespace chartinger\Behat\AroundHookExtension;

use Behat\Behat\Hook\Call\RuntimeScenarioHook;
use Behat\Behat\Hook\Scope\ScenarioScope;

class AroundScenario extends RuntimeScenarioHook
{
  /**
   * Initializes hook.
   *
   * @param null|string $filterString
   * @param callable    $callable
   * @param null|string $description
   */
  public function __construct($filterString, $callable, $description = null)
  {
    parent::__construct(AroundScenarioScope::AROUND, $filterString, $callable, $description);
  }
  
  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'AroundScenario';
  }
  
}