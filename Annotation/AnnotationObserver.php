<?php


namespace Mate\RestBundle\Annotation;


use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationObserver
{
    /** @var AnnotationReader */
    protected $annotationReader;

    /**
     * AnnotationObserver constructor.
     */
    function __construct()
    {
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @param $object
     * @param $group
     * @return array
     */
    public function getWritableProps($object, $group)
    {
        $props         = $this->observe($object, Writable::class);
        $writableProps = [];

        /**
         * @var string $propertyName
         * @var Writable  $writable
         */
        foreach ($props as $propertyName => $writable) {
            $groups = $writable->groups;

            if (in_array($group, $groups)) {
                $writableProps[] = $propertyName;
                continue;
            }
        }

        return $writableProps;
    }

    /**
     * @param $object
     * @param $annotationClass
     * @return array
     */
    private function observe($object, $annotationClass)
    {
        $objectProperties     = $this->getProperties($object);
        $observableProperties = [];

        /** @var \ReflectionProperty $property */
        foreach ($objectProperties as $property) {
            if ($annotation = $this->annotationReader->getPropertyAnnotation($property, $annotationClass)) {
                $observableProperties[$property->getName()] = $annotation;
            }
        }

        return $observableProperties;
    }

    /**
     * @param $object
     * @return array|\ReflectionProperty[]
     */
    private function getProperties($object)
    {
        $reflection = new \ReflectionClass($object);
        return $reflection->getProperties();
    }
}