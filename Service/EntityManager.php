<?php


namespace Mate\RestBundle\Service;

use Mate\RestBundle\Annotation\AnnotationObserver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityManager extends Manager
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var AnnotationObserver */
    protected $annotationObserver;

    /**
     * @param $object
     * @param array $data
     * @param null $group
     * @return SerializableObject
     * @throws \Exception
     */
    public function write($object, $data = array(), $group = null)
    {
        if (empty($data)) {
            return $this->serializable($object);
        }

        foreach ($data as $property => $value) {
            if (!in_array($property, $this->annotationObserver->getWritableProps($object, $group))) {
                continue;
            }

            $this->propertyAccessor->setValue($object, $property, $value);
        }

        $errors = $this->validator->validate($object);

        if (count($errors) > 0) throw new BadRequestHttpException((string) $errors);


        if (!$this->propertyAccessor->getValue($object, 'id')) {
            $this->getManager()->persist($object);
        }

        $this->getManager()->flush();

        return $this->serializable($object);
    }

    /**
     * @param $object
     * @return $this
     */
    public function remove($object)
    {
        $this->getManager()->remove($object);
        $this->getManager()->flush();

        return $this;
    }

    /**
     * @param $repository
     * @return SerializableObject
     */
    public function findAll($repository)
    {
        return $this->serializable($this->getRepository($repository)->findAll());
    }

    /**
     * @param $repository
     * @param $data
     * @return SerializableObject
     */
    public function findOne($repository, $data)
    {
        return  $this->serializable($this->getRepository($repository)->findOneBy($data));
    }

    /**
     * @param $repository
     * @param $data
     * @param null $orderBy
     * @param null $limit
     * @return SerializableObject
     */
    public function find($repository, $data, $orderBy = null, $limit = null)
    {
        if (is_array($data)) {
            return $this->serializable($this->getRepository($repository)->findBy($data, $orderBy, $limit));
        }

        return $this->serializable($this->getRepository($repository)->find($data));
    }

    /**
     * @param $object
     * @return array|mixed
     */
    public function render($object)
    {
        return $this->serializer->toArray($object);
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param AnnotationObserver $annotationObserver
     */
    public function setAnnotationObserver(AnnotationObserver $annotationObserver)
    {
        $this->annotationObserver = $annotationObserver;
    }

    /**
     * @param $data
     * @return SerializableObject
     */
    private function serializable($data)
    {
        return new SerializableObject($data, $this->serializer);
    }
}