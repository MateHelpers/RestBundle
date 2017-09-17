<?php


namespace Mate\RestBundle\Service;


use JMS\Serializer\Serializer;

class SerializableObject
{
    /** @var Serializer */
    protected $serializer;

    protected $data;

    function __construct($data, Serializer $serializer)
    {
        $this->data       = $data;
        $this->serializer = $serializer;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render()
    {
        return $this->serializer->toArray($this->data);
    }
}