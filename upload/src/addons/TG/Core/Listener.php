<?php

namespace TG\Core;

class Listener
{
	public static function appSetup(\XF\App $app)
	{
		$templateCompiler = $app->templateCompiler();
		$templateCompiler->setTags([
			'datetimeinput' => '\TG\Core\Template\Compiler\Tag\DateTimeInputRow',
			'datetimeinputrow' => '\TG\Core\Template\Compiler\Tag\DateTimeInputRow'
		]);
	}
}