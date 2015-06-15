<?php

namespace BrauneDigital\GeoBundle\Entity;

use Gedmo\Translatable\TranslatableListener;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Translatable\Mapping\Event\Adapter\ORM as TranslatableAdapterORM;
use Doctrine\DBAL\Types\Type;


class PersonalTranslationRepository extends EntityRepository
{
    /**
     * Current TranslatableListener instance used
     * in EntityManager
     *
     * @var TranslatableListener
     */
    private $listener;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        if ($class->getReflectionClass()->isSubclassOf('Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation')) {
            throw new \Gedmo\Exception\UnexpectedValueException('This repository is useless for (not personal) translations');
        }
        parent::__construct($em, $class);
    }

    /**
     * Makes additional translation of $entity $field into $locale
     * using $value
     *
     * @param object $entity
     * @param string $field
     * @param string $locale
     * @param mixed  $value
     *
     * @throws \Gedmo\Exception\InvalidArgumentException
     *
     * @return static
     */
    public function translate($entity, $field, $locale, $value, $overrideDefault = false)
    {

        $meta = $this->_em->getClassMetadata(get_class($entity));
        $listener = $this->getTranslatableListener();
        $config = $listener->getConfiguration($this->_em, $meta->name);
        if (!isset($config['fields']) || !in_array($field, $config['fields'])) {
            throw new \Gedmo\Exception\InvalidArgumentException("Entity: {$meta->name} does not translate field - {$field}");
        }

        $needsPersist = true;
        if ($locale === $listener->getTranslatableLocale($entity, $meta) && $overrideDefault) {
            $meta->getReflectionProperty($field)->setValue($entity, $value);
            $this->_em->persist($entity);
        } else {
            if (isset($config['translationClass'])) {
                $class = $config['translationClass'];
            } else {
                $ea = new TranslatableAdapterORM();
                $class = $listener->getTranslationClass($ea, $config['useObjectClass']);
            }
            //$objectId = $meta->getReflectionProperty($meta->getSingleIdentifierFieldName())->getValue($entity);
            $object = $entity;
            $transMeta = $this->_em->getClassMetadata($class);
            $trans = $this->findOneBy(compact('locale', 'field', 'object'));
            if (!$trans) {
                $trans = $transMeta->newInstance();
                $transMeta->getReflectionProperty('object')->setValue($trans, $entity);
                $transMeta->getReflectionProperty('field')->setValue($trans, $field);
                $transMeta->getReflectionProperty('locale')->setValue($trans, $locale);
            }
            if ($listener->getDefaultLocale() != $listener->getTranslatableLocale($entity, $meta) &&
                $locale === $listener->getDefaultLocale()) {
                $listener->setTranslationInDefaultLocale(spl_object_hash($entity), $field, $trans);
                $needsPersist = $listener->getPersistDefaultLocaleTranslation();
            }
            $type = Type::getType($meta->getTypeOfField($field));
            $transformed = $type->convertToDatabaseValue($value, $this->_em->getConnection()->getDatabasePlatform());
            $transMeta->getReflectionProperty('content')->setValue($trans, $transformed);
            if ($needsPersist) {
                if ($this->_em->getUnitOfWork()->isInIdentityMap($entity)) {
                    $this->_em->persist($trans);
                } else {
                    $oid = spl_object_hash($entity);
                    $listener->addPendingTranslationInsert($oid, $trans);
                }
            }
        }

        return $this;
    }

    /**
     * Loads all translations with all translatable
     * fields from the given entity
     *
     * @param object $entity Must implement Translatable
     *
     * @return array list of translations in locale groups
     */
    public function findTranslations($entity)
    {
        $result = array();
        $wrapped = new EntityWrapper($entity, $this->_em);
        if ($wrapped->hasValidIdentifier()) {
            $entityId = $wrapped->getIdentifier();
            $entityClass = $wrapped->getMetadata()->rootEntityName;
            $translationMeta = $this->getClassMetadata(); // table inheritance support

            $config = $this
                ->getTranslatableListener()
                ->getConfiguration($this->_em, get_class($entity));

            $translationClass = isset($config['translationClass']) ?
                $config['translationClass'] :
                $translationMeta->rootEntityName;

            $qb = $this->_em->createQueryBuilder();
            $qb->select('trans.content, trans.field, trans.locale')
                ->from($translationClass, 'trans')
                ->where('trans.object = :entity')
                ->orderBy('trans.locale');
            $q = $qb->getQuery();
            $data = $q->execute(
                compact('entity', 'entityClass'),
                Query::HYDRATE_ARRAY
            );

            if ($data && is_array($data) && count($data)) {
                foreach ($data as $row) {
                    $result[$row['locale']][$row['field']] = $row['content'];
                }
            }
        }

        return $result;
    }

    /*
     *
     * @param object $entity
     * @param string $field
     * @param mixed  $value
     *
     * @throws \Gedmo\Exception\InvalidArgumentException
     *
     * @return static
     */
    public function updateTranslationField($entity, $field, $translations) {
        foreach($translations as $translation) {
            if(array_key_exists('locale', $translation) && array_key_exists('content', $translation)) {
                $this->translate($entity, $field, $translation['locale'], $translation['content']);
                $this->_em->flush();
            }
        }
        return $this;
    }

    /**
     * Get the currently used TranslatableListener
     *
     * @throws \Gedmo\Exception\RuntimeException - if listener is not found
     *
     * @return TranslatableListener
     */
    private function getTranslatableListener()
    {
        if (!$this->listener) {
            foreach ($this->_em->getEventManager()->getListeners() as $event => $listeners) {
                foreach ($listeners as $hash => $listener) {
                    if ($listener instanceof TranslatableListener) {
                        return $this->listener = $listener;
                    }
                }
            }

            throw new \Gedmo\Exception\RuntimeException('The translation listener could not be found');
        }

        return $this->listener;
    }

}
