<?php

namespace Devimteam\Component\RpcServer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AbstractCRUDRpcService
 */
abstract class AbstractCRUDRpcService
{
    const ENTITY_ALIAS = 'e';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $entityClassName;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    private $entityClassMetadata;

    public function __construct(string $entityClassName, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityClassName = $entityClassName;
        $this->entityClassMetadata = $this->entityManager->getClassMetadata($this->entityClassName);
    }

    /**
     * @param array $data
     *
     * @return bool|array
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function create(array $data)
    {
        return $this->doSave(new $this->entityClassName(), $data);
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return bool|array
     */
    public function update(int $id, array $data)
    {
        $entity = $this->entityManager->find($this->entityClassName, $id);

        if (null === $entity) {
            return false;
        }

        return $this->doSave($entity, $data);
    }

    /**
     * @param $entity
     * @param array $data
     *
     * @return bool|array
     */
    final private function doSave($entity, array $data)
    {
        $this->fillEntityData($entity, $data);
        $this->saveEntity($entity);

        return $this->normalizeEntity($entity);
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function find(int $id)
    {
        return $this->findArrayResult($id);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->findAllArrayResult();
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id)
    {
        $entity = $this->entityManager->find($this->entityClassName, $id);
        $this->removeEntity($entity);

        return true;
    }

    /**
     * @param $entity
     * @param $data
     */
    private function fillEntityData($entity, array $data)
    {
        $fieldNames = array_merge(
            $this->entityClassMetadata->getFieldNames(),
            $this->entityClassMetadata->getAssociationNames()
        );

        foreach ($fieldNames as $fieldName) {
            if (isset($data[$fieldName])) {
                $setter = 'set' . ucfirst($fieldName);
                if (method_exists($entity, $setter)) {
                    $entity->$setter($data[$fieldName]);
                }
            }
        }
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    private function findArrayResult(int $id)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ENTITY_ALIAS)
            ->from($this->entityClassName, self::ENTITY_ALIAS)
            ->andWhere(self::ENTITY_ALIAS . '.id = :id')
            ->setParameter('id', $id);

        $this->applyFilter($qb);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @return array
     */
    private function findAllArrayResult()
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb->select(self::ENTITY_ALIAS)
            ->from($this->entityClassName, self::ENTITY_ALIAS);

        $this->applyFilter($qb);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param QueryBuilder $qb
     */
    protected function applyFilter(QueryBuilder $qb)
    {
    }

    /**
     * @param $entity
     */
    private function saveEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param $entity
     */
    private function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @param $entity
     *
     * @return array
     */
    abstract protected function normalizeEntity($entity) : array;
}