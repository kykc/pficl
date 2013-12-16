<?php

namespace pficl\Model\Deprecated
{
	use \pficl\Collection\Util as ArrayUtil;
	use \pficl\Fp\Util as Fp;

	abstract class AbstractModel implements IGenericModel
	{
		protected $objectInstanceCreatedOn;
		protected $objectStorage = array();
		protected $objectListStorage = array();
		protected $serviceObjectStorage = array();
		protected $valueStorage = array();
		private $ownerObject = NULL;
		private $isSaved = FALSE;
		private $fieldList = NULL;
		private $fieldNotationList = NULL;

		const OBJ_STORAGE = 'objectStorage';
		const OBJ_LIST_STORAGE = 'objectListStorage';
		const VALUE_STORAGE = 'valueStorage';
		const SERVICE_OBJ_STORAGE = 'serviceObjectStorage';

		final protected function __construct($id)
		{
			$this->initObject($id, TRUE);
			$this->objectInstanceCreatedOn = date('Y-m-d H:i:s');
		}

		abstract public function db();

		final public function consumeOwnerObject(AbstractModel $owner)
		{
			$this->ownerObject = $owner;
		}

		final public function getOwnerObject()
		{
			return $this->ownerObject;
		}

		final public function getFieldList()
		{
			if ($this->fieldList === NULL)
			{
				$this->fieldList = array_keys($this->getNormalizationRuleList());
			}

			return $this->fieldList;
		}

		public function isAutoOwnerPushEnabled()
		{
			return TRUE;
		}

		final protected function readFrom($name, $property = self::VALUE_STORAGE)
		{
			if (array_key_exists($name, $this->$property))
			{
				return $this->{$property}[$name];
			}
			elseif (!$this->isSaved)
			{
				if ($property === self::VALUE_STORAGE)
				{
					return $this->applyNormalizationRule(NULL, $this->getNormalizationRule($name));
				}
				else
				{
					return NULL;
				}
			}
			else
			{
				throw new ReadValueFailedException(json_encode(array($name, $property)));
			}
		}

		final protected function pushToList($object, $fieldName, $assoc = FALSE)
		{
			$propName = self::OBJ_LIST_STORAGE;
			$this->{$propName}[$fieldName][] = $object;

			if ($assoc)
			{
				ModelCollection::linkByOwnership($this->getOwnershipInfo(), $object);
			}

			return $this;
		}

		final protected function writeTo($name, $value, $property = self::VALUE_STORAGE)
		{
			if ($property === self::VALUE_STORAGE)
			{
				$value = $this->applyNormalizationRule($value, $this->getNormalizationRule($name));
			}

			$this->{$property}[$name] = $value;

			return $value;
		}

		/** @return OwnershipInfoProvider */
		final public function getOwnershipInfo()
		{
			return OwnershipInfoProvider::makeByRawData($this->getPrimaryId(), $this->getStorageName());
		}

		public function getIsDisposable()
		{
			return $this->isDisposable();
		}

		protected function isDisposable()
		{
			return FALSE;
		}

		public function isDisposed()
		{
			return FALSE;
		}

		final public function ensureStored()
		{
			if (!$this->isSaved)
			{
				$this->saveToDb();
			}

			return $this;
		}

		final protected function fetchIdList($id, $model, $type = NULL, $includeDisposed = FALSE)
		{
			return self::fetchIdListStatic($id, $model, $type, $includeDisposed);
		}

		final public static function fetchIdListStatic($id, $model, $type = NULL, $includeDisposed = FALSE)
		{
			$typePart = $type === NULL ? '1' : 'ownerType = '.$model->db()->quote($type);
			$dispPart = !$model->isDisposable() || $includeDisposed ? '1' : 'NOT isDeleted';

			$sql = 'SELECT id FROM '.$model->getStorageName().' WHERE ownerId > 0 AND ownerId = '.intval($id).' AND '.$typePart.' AND '.$dispPart;

			return Fp::map(Fp::makeFetchElementLambda('id'), $model->db()->queryFetchAllAssoc($sql));
		}

		final protected function idLst($id, $model, $type = NULL, $includeDisposed = FALSE)
		{
			return function() use($id, $model, $type, $includeDisposed)
			{
				return AbstractModel::fetchIdListStatic($id, $model, $type, $includeDisposed);
			};
		}

		final protected function idLstNonStd($id, $model, $fieldName, $includeDisposed = FALSE)
		{
			$self = $this;

			return function() use ($self, $model, $fieldName, $id, $includeDisposed)
			{
				$dispPart = !$model->isDisposable() || $includeDisposed ? '1' : 'NOT isDeleted';

				$sql = 'SELECT id FROM `'.$model->getStorageName().'` WHERE `'.$fieldName.'` = '.intval($id).' AND '.$dispPart;

				return $self->db()->queryFetchAllAssoc($sql);
			};
		}

		final protected function mkObj($model)
		{
			return function($id) use($model)
			{
				$className = is_object($model) ? get_class($model) : $model;

				return $className::make($id);
			};
		}

		final public function filterDisposedObjectData()
		{
			return function(array $pod)
			{
				return !ArrayUtil::getField($pod, 'isDeleted');
			};
		}

		final public function getExternalRepresentation($simplify = TRUE)
		{
			$this->fill();
			$data = array();
			$data['fieldList'] = $this->getObjectState();
			$data['fieldList']['objectType'] = $this->getStorageName();
			$data['objectList'] = array();
			$data['objectListList'] = array();

			$propObj = self::OBJ_STORAGE;
			$propObjList = self::OBJ_LIST_STORAGE;

			foreach ($this->$propObj as $key => $object)
			{
				if ($object !== NULL)
				{
					$data['objectList'][$key] = $object->getExternalRepresentation();
				}
			}

			foreach ($this->$propObjList as $key => $objectList)
			{
				$data['objectListList'][$key] = array();

				foreach ($objectList as $object)
				{
					$data['objectListList'][$key][] = $object->getExternalRepresentation();
				}
			}

			if ($simplify)
			{
				$data = ArrayUtil::arrayConcat($data);
			}

			$data = $this->postprocessExternal($data, $simplify);

			return $data;
		}

		protected function postprocessExternal($data, $simplify)
		{
			return $data;
		}

		protected function fill()
		{

		}

		final protected function findObjectById($id, $fieldName)
		{
			$property = self::OBJ_LIST_STORAGE;
			$collection = $this->{$property}[$fieldName];

			$obj = ArrayUtil::findObject($collection, \GenericPredicate::makeSameIdDbObjectById($id));

			return $obj;
		}

		final private function handleIdChange($id)
		{
			if (is_callable(array($this, 'setId')))
			{
				$this->setId($id);
			}
			else
			{
				$this->writeTo('id', $id);
			}
		}

		final protected function initObject($id, $fetchStorageData = FALSE)
		{
			$this->writeTo('dbObjectStorageProvider', NULL, self::SERVICE_OBJ_STORAGE);
			$this->writeTo('lifetimeDiffProvider', NULL, self::SERVICE_OBJ_STORAGE);

			$this->isAlive = FALSE;

			if ($id)
			{
				$this->handleIdChange($id);

				if ($fetchStorageData)
				{
					$state = $this->getDbObjectStorageProvider()->fetchDataFromStorage();
					$this->setObjectState($state);
				}

				$this->nonEmptyInit();
				$this->isSaved = TRUE;
			}
			else
			{
				$this->handleIdChange(0);
				$this->emptyInit();
				//$this->fillFieldStorage();
			}

			$this->isAlive = TRUE;

			$this->getLifetimeDiffProvider();
			$this->performAfterFullInit();
		}

		private function fillFieldStorage()
		{
			foreach ($this->getFieldList() as $fieldName)
			{
				$this->writeTo($fieldName, NULL);
			}
		}

		protected function emptyInit()
		{

		}

		protected function nonEmptyInit()
		{

		}

		protected function performAfterFullInit()
		{

		}

		protected function checkBeforeSave()
		{

		}

		final public function saveToDb()
		{
			$this->checkBeforeSave();
			$insertId = $this->getDbObjectStorageProvider()->storeData();

			$id = $this->getId() ? $this->getId() : $insertId;
			$this->writeTo('id', $id);
			$this->storeChilds();
			$this->initObject($id, TRUE);
			$this->performAfterSave();

			return $this;
		}

		protected function performAfterSave()
		{
		}

		final protected function storeChilds()
		{
			$storeSingle = $this->getStoreChildCallback();

			$storeCollection = function(array $collection) use ($storeSingle)
			{
				array_map($storeSingle, $collection);
			};

			$objectStorage = self::OBJ_STORAGE;
			$objectListStorage = self::OBJ_LIST_STORAGE;
			array_map($storeSingle, $this->$objectStorage);
			array_map($storeCollection, $this->$objectListStorage);
		}

		final protected function eraseField($fieldName, $propName = self::VALUE_STORAGE)
		{
			$this->{$propName}[$fieldName] = NULL;
		}

		final protected function getStoreChildCallback()
		{
			$current = $this;

			return function($object) use($current)
			{
				if ($object instanceof IOwnedModel)
				{
					if (!intval($object->getPrimaryId()) || !intval($object->getOwnerId()))
					{
						$object->setOwnerId($current->getPrimaryId());
					}

					if (!strval($object->getOwnerType()))
					{
						$object->setOwnerType($current->getStorageName());
					}
				}

				if (!is_null($object) && is_callable(array($object, 'saveToDb')))
				{
					$object->saveToDb();
				}
			};
		}

		final protected function getChildObject($className, $fieldName = NULL, $fetchId = NULL, $model = NULL)
		{
			$model = $model ? $model : $className::make(0);
			$fieldName = $fieldName ? $fieldName : $model->getStorageName();
			$className = $model && $className === NULL ? get_class($model) : $className;
			$id = $fetchId ? $fetchId() : ArrayUtil::getFirst($this->fetchIdList($this->getId(), $model, $this->getStorageName()));
			$createFunc = function() use ($id, $className) {return $className::make($id);};
			$obj = $this->getOnDemand($fieldName, self::OBJ_STORAGE, $createFunc);
			$obj->setOwnerType($this->getStorageName());
			$obj->setOwnerId($this->getPrimaryId());
			$this->pushOwner($obj);

			return $obj;
		}

		final protected function pushOwner($obj)
		{
			$func = $this->getPushOwnerFunc($this);
			$func($obj);
		}

		final protected function getPushOwnerFunc(AbstractModel $owner)
		{
			return function($obj) use ($owner)
			{
				if ($owner->isAutoOwnerPushEnabled() && $obj instanceof IOwnedModel && $obj instanceof AbstractModel)
				{
					$obj->consumeOwnerObject($owner);
				}

				return $obj;
			};
		}

		final protected function getChildObjectList($model, $fieldName, $fetchIdList = NULL, $includeDisposed = FALSE)
		{
			$current = $this->getOnDemand($fieldName, self::OBJ_LIST_STORAGE, function(){return NULL;});

			if ($current === NULL)
			{
				$className = get_class($model);
				$idList = $fetchIdList ? $fetchIdList() : $this->fetchIdList($this->getId(), $model, $this->getStorageName(), $includeDisposed);
				$createFunc = function($id) use ($className) {return $className::make($id);};
				$createFunc = Fp::dot($createFunc, $this->getPushOwnerFunc($this));
				$objList = array_map($createFunc, $idList);

				return $this->writeTo($fieldName, $objList, self::OBJ_LIST_STORAGE);
			}
			else
			{
				if (!$includeDisposed)
				{
					return array_filter($this->readFrom($fieldName, self::OBJ_LIST_STORAGE), $this->getDisposedFilter());
				}
				else
				{
					return $this->readFrom($fieldName, self::OBJ_LIST_STORAGE);
				}
			}
		}

		protected function getHideDisposedChilds()
		{
			return TRUE;
		}

		final protected function getChildObjectListGeneric($createFunc, $fetchIdFunc, $fieldName, $ordering = NULL)
		{
			$current = $this->getOnDemand($fieldName, self::OBJ_LIST_STORAGE, function(){return NULL;});
			$createFunc = Fp::dot($createFunc, $this->getPushOwnerFunc($this));

			if ($current === NULL)
			{
				$result = $this->writeTo($fieldName, array_map($createFunc, $fetchIdFunc()), self::OBJ_LIST_STORAGE);
			}
			elseif ($this->getHideDisposedChilds())
			{
				$result = array_filter($this->readFrom($fieldName, self::OBJ_LIST_STORAGE), $this->getDisposedFilter());
			}
			else
			{
				$result = $this->readFrom($fieldName, self::OBJ_LIST_STORAGE);
			}

			if (is_callable($ordering))
			{
				usort($result, $ordering);
			}

			return $result;
		}

		final protected function getChildObjectGeneric($createFunc, $fetchIdFunc, $fieldName)
		{
			$current = $this->getOnDemand($fieldName, self::OBJ_STORAGE, function(){return NULL;});
			$createFunc = Fp::dot($createFunc, $this->getPushOwnerFunc($this));

			if ($current === NULL)
			{
				return $this->writeTo($fieldName, $createFunc($fetchIdFunc()), self::OBJ_STORAGE);
			}
			else
			{
				return $this->readFrom($fieldName, self::OBJ_STORAGE);
			}
		}

		final protected function getFetchIdListCallback($id, $tableName, $disposable = FALSE, $ownerType = NULL)
		{
			$self = $this;

			return function() use ($id, $tableName, $disposable, $ownerType, $self)
			{
				$typePart = $ownerType === NULL ? '1' : 'ownerType = '.$self->db()->quote($ownerType);
				$dispPart = !$disposable ? '1' : 'NOT '.$disposable;

				$sql = 'SELECT id FROM `'.$tableName.'` WHERE ownerId = '.intval($id).' AND '.$typePart.' AND '.$dispPart;

				return Fp::map(Fp::makeFetchElementLambda('id'), $self->db()->queryFetchAllAssoc($sql));
			};
		}

		final protected function getDisposedFilter()
		{
			return function($obj)
			{
				return !$obj->isDisposed();
			};
		}

		final protected function addChildObject($className, $fieldName, $fetchIdList = NULL, $model = NULL)
		{
			$property = self::OBJ_LIST_STORAGE;
			$this->getChildObjectList($className, $fieldName, $fetchIdList, $model);
			$obj = is_string($className) ? $className::make(0) : $className;
			$obj->setOwnerType($this->getStorageName());
			$obj->setOwnerId($this->getPrimaryId());
			$this->pushOwner($obj);
			$this->{$property}[$fieldName][] = $obj;

			return $obj;
		}

		final protected function getIsAlive()
		{
			return TRUE;
		}

		final protected function getOnDemand($name, $property, $createFunc)
		{
			$val = NULL;

			try
			{
				$val = $this->readFrom($name, $property);
			}
			catch (ReadValueFailedException $ex)
			{
				$val = NULL;
			}

			return $val === NULL ? $this->writeTo($name, $createFunc(), $property) : $val;
		}

		final public function getPrimaryId()
		{
			return $this->readFrom('id');
		}

		public function prepareStorageState(array $state)
		{
			return $state;
		}

		public function getObjectState($forPersist = FALSE)
		{
			$property = self::VALUE_STORAGE;
			$state = $this->$property;
			return ArrayUtil::transformArray($state, array_combine($this->getFieldList(), $this->getFieldList()));
		}

		protected function getNormalizationRuleList()
		{
			if ($this->fieldNotationList === NULL)
			{
				$this->fieldNotationList = $this->makeFieldNotationList();
			}

			return $this->makeFieldNotationList();
		}

		abstract protected function makeFieldNotationList();

		final protected function getNormalizationRule($name)
		{
			return ArrayUtil::getField($this->getNormalizationRuleList(), $name, Fp::id());
		}

		final protected function applyNormalizationRule($value, $rule)
		{
			if (is_callable($rule))
			{
				return call_user_func($rule, $value);
			}
			else
			{
				return $value;
			}
		}

		public function setObjectState(array $state)
		{
			$state = ArrayUtil::transformArray($state, array_combine($this->getFieldList(), $this->getFieldList()));

			foreach ($state as $fieldName => $fieldValue)
			{
				$state[$fieldName] = $this->applyNormalizationRule($fieldValue, $this->getNormalizationRule($fieldName));
			}

			$property = self::VALUE_STORAGE;
			$this->$property = $state;

			return $this;
		}

		public function updateObjectState(array $state)
		{
			foreach ($state as $fieldName => $fieldValue)
			{
				if (in_array($fieldName, $this->getFieldList()) && $fieldName != 'id')
				{
					$this->writeTo($fieldName, $fieldValue);
				}
			}

			return $this;
		}

		public function getParentObject($generation = 1)
		{
			$res = $this;

			while ($generation > 0)
			{
				if ($res instanceof AbstractModel)
				{
					$res = $res->getOwnerObject();
				}
				else
				{
					throw new ParentObjectNotFoundException(get_class($this));
				}

				$generation--;
			}

			return $res;
		}

		/** @return \pficl\Model\Deprecated\ObjectLifetimeDiffProvider */
		public function getLifetimeDiffProvider()
		{
			$current = $this;
			$createFunc = function() use ($current) {return ObjectLifetimeDiffProvider::make($current);};
			return $this->getOnDemand('lifetimeDiffProvider', self::SERVICE_OBJ_STORAGE, $createFunc);
		}

		public function isSlaveAllowed()
		{
			return FALSE;
		}

		/** @return \DbObjectStorageProvider */
		public function getDbObjectStorageProvider()
		{
			$current = $this;
			$createFunc = function() use ($current) {return DbObjectStorageProvider::make($current, $current->isSlaveAllowed());};
			return $this->getOnDemand('dbObjectStorageProvider', self::SERVICE_OBJ_STORAGE, $createFunc);
		}

		public function getAlwaysUpdatedFieldList()
		{
			return array();
		}

		public function getCurrentLifetimeDiff()
		{
			return $this->getLifetimeDiffProvider()->getDiff($this->getAlwaysUpdatedFieldList());
		}
	}
}

?>
