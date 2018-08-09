<?php

namespace TG\Core\Template\Compiler\Tag;

use XF\Template\Compiler\Tag\AbstractFormElement;
use XF\Template\Compiler\Syntax\Tag;
use XF\Template\Compiler;

class DateTimeInputRow extends AbstractFormElement
{
	public function compile(Tag $tag, Compiler $compiler, array $context, $inlineExpected)
	{
		return $this->compileTextInput('DateTimeInput', $tag->name == 'datetimeinputrow', $tag, $compiler, $context);
	}
}