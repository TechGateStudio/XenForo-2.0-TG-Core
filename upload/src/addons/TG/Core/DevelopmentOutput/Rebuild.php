<?php

namespace TG\Core\DevelopmentOutput;

use XF\Mvc\Entity\Entity;
use XF\Util\Json;

class Rebuild extends \XF\DevelopmentOutput\AbstractHandler
{
	protected function getTypeDir()
	{
		return 'rebuilds';
	}
	
	public function export(Entity $rebuild)
	{
		if (!$this->isRelevant($rebuild))
		{
			return true;
		}

		$fileName = $this->getFileName($rebuild);

		$json = $this->pullEntityKeys($rebuild, [
            'rebuild_id',
			'class',
			'template'
		]);

		return $this->developmentOutput->writeFile(
			$this->getTypeDir(), $rebuild->addon_id, $fileName, Json::jsonEncodePretty($json)
		);
	}

	protected function getEntityForImport($name, $addOnId, $json, array $options)
	{
		/** @var \TG\Core\Entity\Rebuild $rebuild */
		$rebuild = \XF::em()->getFinder('TG\Core:Rebuild')->where([
            'rebuild_id' => $json['rebuild_id']
		])->fetchOne();
		if (!$rebuild)
		{
			$rebuild = \XF::em()->create('TG\Core:Rebuild');
		}

		$rebuild = $this->prepareEntityForImport($rebuild, $options);

		return $rebuild;
	}

	public function import($name, $addOnId, $contents, array $metadata, array $options = [])
	{
		$json = json_decode($contents, true);

		$rebuild = $this->getEntityForImport($name, $addOnId, $json, $options);
		
		$rebuild->bulkSet($json);
		$rebuild->addon_id = $addOnId;
		$rebuild->save();
		// this will update the metadata itself

		return $rebuild;
	}

	protected function getFileName(Entity $rebuild, $new = true)
	{
        $rebuildId = $new ? $rebuild->getValue('rebuild_id') : $rebuild->getExistingValue('rebuild_id');
        $rebuildId = ltrim(preg_replace('#[^a-z0-9_-]#i', '-', $rebuildId), '-');
        
		return "{$rebuildId}.json";
	}
}