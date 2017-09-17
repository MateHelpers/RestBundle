<?php


namespace Mate\RestBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Writable
{
    /** @var array<string> @Required */
    public $groups;
}