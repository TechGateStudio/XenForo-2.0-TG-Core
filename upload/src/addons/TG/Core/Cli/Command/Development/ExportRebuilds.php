<?php

namespace TG\Core\Cli\Command\Development;

use XF\Mvc\Entity\Finder;

class ExportRebuilds extends \XF\Cli\Command\Development\AbstractExportCommand
{
	protected function getContentTypeDetails()
	{
		return [
			'name' => 'rebuilding cache',
			'command' => 'rebuilds',
			'entity' => 'TG\Core:Rebuild'
		];
	}
}