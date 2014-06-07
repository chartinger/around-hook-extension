<?php

namespace chartinger\Behat\AroundHookExtension;

use ReflectionMethod;
use Behat\Behat\Context\Annotation\AnnotationReader;

class AroundHookAnnotationReader implements AnnotationReader
{
  
  /**
   *
   * @var string
   */
  private static $regex = '/^\@(aroundscenario)(?:\s+(.+))?$/i';
  /**
   *
   * @var string[]
   */
  private static $classes = array (
      'aroundscenario' => 'chartinger\Behat\AroundHookExtension\AroundScenario' 
  );
  
  /*
   * (non-PHPdoc) @see \Behat\Behat\Context\Annotation\AnnotationReader::readCallee()
   */
  public function readCallee($contextClass, ReflectionMethod $method, $docLine, $description)
  {
    if (! preg_match(self::$regex, $docLine, $match))
    {
      return null;
    }
    
    $type = strtolower($match[1]);
    $class = self::$classes[$type];
    $pattern = isset($match[2]) ? $match[2] : null;
    $callable = array (
        $contextClass,
        $method->getName() 
    );
    
    return new $class($pattern, $callable, $description);
  }

}