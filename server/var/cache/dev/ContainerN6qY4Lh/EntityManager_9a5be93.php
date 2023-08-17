<?php

namespace ContainerN6qY4Lh;
include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/src/Persistence/ObjectManager.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/lib/Doctrine/ORM/EntityManagerInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/lib/Doctrine/ORM/EntityManager.php';

class EntityManager_9a5be93 extends \Doctrine\ORM\EntityManager implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager|null wrapped object, if the proxy is initialized
     */
    private $valueHolderf507d = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer49a57 = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicProperties82182 = [
        
    ];

    public function getConnection()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getConnection', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getConnection();
    }

    public function getMetadataFactory()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getMetadataFactory', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getMetadataFactory();
    }

    public function getExpressionBuilder()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getExpressionBuilder', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getExpressionBuilder();
    }

    public function beginTransaction()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'beginTransaction', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->beginTransaction();
    }

    public function getCache()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getCache', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getCache();
    }

    public function transactional($func)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'transactional', array('func' => $func), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->transactional($func);
    }

    public function wrapInTransaction(callable $func)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'wrapInTransaction', array('func' => $func), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->wrapInTransaction($func);
    }

    public function commit()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'commit', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->commit();
    }

    public function rollback()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'rollback', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->rollback();
    }

    public function getClassMetadata($className)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getClassMetadata', array('className' => $className), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getClassMetadata($className);
    }

    public function createQuery($dql = '')
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'createQuery', array('dql' => $dql), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->createQuery($dql);
    }

    public function createNamedQuery($name)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'createNamedQuery', array('name' => $name), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->createNamedQuery($name);
    }

    public function createNativeQuery($sql, \Doctrine\ORM\Query\ResultSetMapping $rsm)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'createNativeQuery', array('sql' => $sql, 'rsm' => $rsm), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'createNamedNativeQuery', array('name' => $name), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->createNamedNativeQuery($name);
    }

    public function createQueryBuilder()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'createQueryBuilder', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->createQueryBuilder();
    }

    public function flush($entity = null)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'flush', array('entity' => $entity), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->flush($entity);
    }

    public function find($className, $id, $lockMode = null, $lockVersion = null)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'find', array('className' => $className, 'id' => $id, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->find($className, $id, $lockMode, $lockVersion);
    }

    public function getReference($entityName, $id)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getReference', array('entityName' => $entityName, 'id' => $id), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getPartialReference', array('entityName' => $entityName, 'identifier' => $identifier), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getPartialReference($entityName, $identifier);
    }

    public function clear($entityName = null)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'clear', array('entityName' => $entityName), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->clear($entityName);
    }

    public function close()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'close', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->close();
    }

    public function persist($entity)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'persist', array('entity' => $entity), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->persist($entity);
    }

    public function remove($entity)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'remove', array('entity' => $entity), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->remove($entity);
    }

    public function refresh($entity, ?int $lockMode = null)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'refresh', array('entity' => $entity, 'lockMode' => $lockMode), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->refresh($entity, $lockMode);
    }

    public function detach($entity)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'detach', array('entity' => $entity), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->detach($entity);
    }

    public function merge($entity)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'merge', array('entity' => $entity), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->merge($entity);
    }

    public function copy($entity, $deep = false)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'copy', array('entity' => $entity, 'deep' => $deep), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'lock', array('entity' => $entity, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->lock($entity, $lockMode, $lockVersion);
    }

    public function getRepository($entityName)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getRepository', array('entityName' => $entityName), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getRepository($entityName);
    }

    public function contains($entity)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'contains', array('entity' => $entity), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->contains($entity);
    }

    public function getEventManager()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getEventManager', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getEventManager();
    }

    public function getConfiguration()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getConfiguration', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getConfiguration();
    }

    public function isOpen()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'isOpen', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->isOpen();
    }

    public function getUnitOfWork()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getUnitOfWork', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getHydrator', array('hydrationMode' => $hydrationMode), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'newHydrator', array('hydrationMode' => $hydrationMode), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->newHydrator($hydrationMode);
    }

    public function getProxyFactory()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getProxyFactory', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getProxyFactory();
    }

    public function initializeObject($obj)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'initializeObject', array('obj' => $obj), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->initializeObject($obj);
    }

    public function getFilters()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'getFilters', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->getFilters();
    }

    public function isFiltersStateClean()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'isFiltersStateClean', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->isFiltersStateClean();
    }

    public function hasFilters()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'hasFilters', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return $this->valueHolderf507d->hasFilters();
    }

    /**
     * Constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;

        $reflection = $reflection ?? new \ReflectionClass(__CLASS__);
        $instance   = $reflection->newInstanceWithoutConstructor();

        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $instance, 'Doctrine\\ORM\\EntityManager')->__invoke($instance);

        $instance->initializer49a57 = $initializer;

        return $instance;
    }

    public function __construct(\Doctrine\DBAL\Connection $conn, \Doctrine\ORM\Configuration $config, ?\Doctrine\Common\EventManager $eventManager = null)
    {
        static $reflection;

        if (! $this->valueHolderf507d) {
            $reflection = $reflection ?? new \ReflectionClass('Doctrine\\ORM\\EntityManager');
            $this->valueHolderf507d = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $this, 'Doctrine\\ORM\\EntityManager')->__invoke($this);

        }

        $this->valueHolderf507d->__construct($conn, $config, $eventManager);
    }

    public function & __get($name)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, '__get', ['name' => $name], $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        if (isset(self::$publicProperties82182[$name])) {
            return $this->valueHolderf507d->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf507d;

            $backtrace = debug_backtrace(false, 1);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $realInstanceReflection->getName(),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
        }

        $targetObject = $this->valueHolderf507d;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __set($name, $value)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, '__set', array('name' => $name, 'value' => $value), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf507d;

            $targetObject->$name = $value;

            return $targetObject->$name;
        }

        $targetObject = $this->valueHolderf507d;
        $accessor = function & () use ($targetObject, $name, $value) {
            $targetObject->$name = $value;

            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __isset($name)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, '__isset', array('name' => $name), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf507d;

            return isset($targetObject->$name);
        }

        $targetObject = $this->valueHolderf507d;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __unset($name)
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, '__unset', array('name' => $name), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf507d;

            unset($targetObject->$name);

            return;
        }

        $targetObject = $this->valueHolderf507d;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);

            return;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $accessor();
    }

    public function __clone()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, '__clone', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        $this->valueHolderf507d = clone $this->valueHolderf507d;
    }

    public function __sleep()
    {
        $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, '__sleep', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;

        return array('valueHolderf507d');
    }

    public function __wakeup()
    {
        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $this, 'Doctrine\\ORM\\EntityManager')->__invoke($this);
    }

    public function setProxyInitializer(\Closure $initializer = null) : void
    {
        $this->initializer49a57 = $initializer;
    }

    public function getProxyInitializer() : ?\Closure
    {
        return $this->initializer49a57;
    }

    public function initializeProxy() : bool
    {
        return $this->initializer49a57 && ($this->initializer49a57->__invoke($valueHolderf507d, $this, 'initializeProxy', array(), $this->initializer49a57) || 1) && $this->valueHolderf507d = $valueHolderf507d;
    }

    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolderf507d;
    }

    public function getWrappedValueHolderValue()
    {
        return $this->valueHolderf507d;
    }
}

if (!\class_exists('EntityManager_9a5be93', false)) {
    \class_alias(__NAMESPACE__.'\\EntityManager_9a5be93', 'EntityManager_9a5be93', false);
}
