<?php

namespace pficl\Model\Deprecated
{
	abstract class ModelCollection
	{
		public static function linkByOwnership(OwnershipInfoProvider $owner, IOwnedModel $model)
		{
			$model->setOwnerId($owner->getOwnerId());
			$model->setOwnerType($owner->getOwnerType());

			return $model;
		}

		public static function makeByOwnership(OwnershipInfoProvider $owner, IOwnedModel $model)
		{
			$sql = 'SELECT id FROM '.$model->getStorageName().' '.
					'WHERE 1 '.
						'AND ownerId = '.intval($owner->getOwnerId()).' AND ownerId <> 0 '.
						'AND ownerType = '.$model->db()->quote($owner->getOwnerType()).' ';

			if ($model->getIsDisposable())
			{
				$sql .= 'AND NOT isDeleted ';
			}

			$sql.= 'ORDER BY id DESC';

			$objectList = array();

			$statement = $model->db()->query($sql);

			foreach ($statement->fetchAll(\pficl\Db\PDO::FETCH_ASSOC) as $row)
			{
				$modelClass = get_class($model);
				$objectList[] = $modelClass::make($row['id']);
			}

			return $objectList;
		}
	}
}

?>
