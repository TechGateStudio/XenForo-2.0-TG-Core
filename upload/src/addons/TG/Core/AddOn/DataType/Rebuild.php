<?php

namespace TG\Core\AddOn\DataType;

class Rebuild extends \XF\AddOn\DataType\AbstractDataType
{
	public function getShortName()
	{
		return 'TG\Core:Rebuild';
	}

	public function getContainerTag()
	{
		return 'rebuilds';
	}

	public function getChildTag()
	{
		return 'rebuild';
	}

	public function exportAddOnData($addOnId, \DOMElement $container)
	{
		$entries = $this->finder()
			->where('addon_id', $addOnId)
			->fetch();
		foreach ($entries AS $entry)
		{
			$node = $container->ownerDocument->createElement($this->getChildTag());

			$this->exportMappedAttributes($node, $entry);

			$this->exportCdataToNewNode($node, 'template', $entry);

			$container->appendChild($node);
		}

		return $entries->count() ? true : false;
	}

	public function importAddOnData($addOnId, \SimpleXMLElement $container, $start = 0, $maxRunTime = 0)
	{
		$startTime = microtime(true);

		$entries = $this->getEntries($container, $start);
		if (!$entries)
		{
			return false;
		}

		$i = 0;
		$last = 0;
		foreach ($entries AS $entry)
		{
			$i++;

			if ($i <= $start)
			{
				continue;
			}

			$entity = $this->create();
			$this->importMappedAttributes($entry, $entity);

			$entity->template = $this->getCdataValue($entry->template);
			$entity->addon_id = $addOnId;
			$entity->save(true, false);

			if ($this->resume($maxRunTime, $startTime))
			{
				$last = $i;
				break;
			}
		}
		return ($last ?: false);
	}

	public function deleteOrphanedAddOnData($addOnId, \SimpleXMLElement $container)
	{
        $existing = $this->findAllForType($addOnId)
            ->fetch();
        if (!$existing)
		{
			return;
		}
        
        $entries = $this->getEntries($container) ?: [];
        
        array_walk_recursive($existing, function($entity)
		{
			if ($entity instanceof \TG\Core\Entity\Rebuild)
			{
				$entity->delete();
			}
		});
	}

	public function rebuildActiveChange(\XF\Entity\AddOn $addOn, array &$jobList)
	{
        /*
		\XF::runOnce('rebuild_active_' . $this->getContainerTag(), function()
		{
			$repo = $this->em->getRepository('XF:ClassExtension');
			$repo->rebuildExtensionCache();
		});
        */
	}

	protected function getMappedAttributes()
	{
		return [
            'rebuild_id',
			'class'
		];
	}
}