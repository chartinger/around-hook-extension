<?php
namespace chartinger\Behat\AroundHookExtension;

use Behat\Testwork\ServiceContainer\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\DependencyInjection\Reference;
use Behat\Behat\Tester\ServiceContainer\TesterExtension;
use Symfony\Component\DependencyInjection\Definition;
use Behat\Testwork\Environment\EnvironmentManager;
use Behat\Testwork\Environment\ServiceContainer\EnvironmentExtension;
use Behat\Testwork\Hook\ServiceContainer\HookExtension;
use Behat\Behat\Context\ServiceContainer\ContextExtension;

class AroundHookExtension implements Extension
{

  public function process(ContainerBuilder $container)
  {
    // TODO Auto-generated method stub
  }

  public function load(ContainerBuilder $container, array $config)
  {
    $definition = new Definition('chartinger\Behat\AroundHookExtension\AroundHookFeatureTester', array(
        new Reference(TesterExtension::SPECIFICATION_TESTER_ID),
        new Reference(TesterExtension::SCENARIO_TESTER_ID),
        new Reference(TesterExtension::OUTLINE_TESTER_ID),
        new Reference(EnvironmentExtension::MANAGER_ID),
        new Reference(HookExtension::DISPATCHER_ID)
    ));
    $definition->addTag(TesterExtension::SPECIFICATION_TESTER_WRAPPER_TAG, array('priority' => 99));
    $container->setDefinition(TesterExtension::SPECIFICATION_TESTER_WRAPPER_TAG . '.substep', $definition);
    
    $this->loadAnnotationReader($container);
  }

  public function getConfigKey()
  {
    return "around_hook";
  }

  public function configure(ArrayNodeDefinition $builder)
  {
    // TODO Auto-generated method stub
  }

  public function initialize(ExtensionManager $extensionManager)
  {
    // TODO Auto-generated method stub
  }

  /**
   * Loads hook annotation reader.
   *
   * @param ContainerBuilder $container
   */
  private function loadAnnotationReader(ContainerBuilder $container)
  {
     $definition = new Definition('chartinger\Behat\AroundHookExtension\AroundHookAnnotationReader');
     $definition->addTag(ContextExtension::ANNOTATION_READER_TAG, array('priority' => 50));
     $container->setDefinition(ContextExtension::ANNOTATION_READER_TAG . '.aroundhook', $definition);
  }
  
}