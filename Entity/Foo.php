<?php


namespace Mate\RestBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Mate\RestBundle\Annotation\Writable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Foo
 * @package Mate\RestBundle\Entity
 * @Serializer\ExclusionPolicy("all")
 * @ORM\Entity()
 * @ORM\Table(name="foos")
 */
class Foo
{
    /**
     * @var integer
     * @Serializer\Expose()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Serializer\Expose()
     * @Writable(groups={"create", "DDD"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


}