<?php

namespace TG\Core\Cli\Command\Development;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRebuilds extends \XF\Cli\Command\Development\AbstractImportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'rebuilding cache',
			'command' => 'rebuilds',
			'dir' => 'rebuilds',
			'entity' => 'TG\Core:Rebuild'
		];
	}

	protected function getTitleIdMap($typeDir, $addOnId)
	{
		return \XF::db()->fetchPairs("
			SELECT CONCAT('rebuild_id'), rebuild_id
			FROM xf_tgc_rebuild
			WHERE addon_id = ?
		", $addOnId);
	}
}