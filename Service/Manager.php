<?php


namespace Mate\RestBundle\Service;


use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\Serializer;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Manager
{
    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var PropertyAccessor */
    protected $propertyAccessor;

    /** @var Serializer */
    protected $serializer;


    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function setPropertyAccessor(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getManager()
    {
        return $this->managerRegistry->getManager();
    }

    protected function getRepository($entityClass)
    {
        return $this->managerRegistry->getRepository($entityClass);
    }
}