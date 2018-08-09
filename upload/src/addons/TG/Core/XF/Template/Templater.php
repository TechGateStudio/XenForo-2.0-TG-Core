<?php

namespace TG\Core\XF\Template;

class Templater extends XFCP_Templater
{
	public function fnUserBlurb($templater, &$escape, $user, $attributes = [])
	{
		$reply = parent::fnUserBlurb($templater, $escape, $user, $attributes);

		$blurbParts = [];

		if ($user->tgc_gender != 'none')
		{
			$blurbParts[] = \XF::phrase('tgc_gender_' . $user->tgc_gender);
		}

		if ($blurbParts)
		{
			$tag = $this->processAttributeToRaw($attributes, 'tag');
			if (!$tag)
			{
				$tag = 'div';
			}

			$reply = str_ireplace("</{$tag}>", '', $reply);
			$reply .= ' <span role="presentation" aria-hidden="true">&middot;</span> ';
			$reply .= implode(' <span role="presentation" aria-hidden="true">&middot;</span> ', $blurbParts);
			$reply .= "</{$tag}>";
		}

		return $reply;
	}

	public function formDateTimeInput(array $controlOptions)
	{
		$this->processDynamicAttributes($controlOptions);

		$class = $this->processAttributeToRaw($controlOptions, 'class', ' %s', true);
		$xfInit = $this->processAttributeToRaw($controlOptions, 'data-xf-init', ' %s', true);
		$xfDateInitAttr = " data-xf-init=\"date-input$xfInit\"";
		$xfTimeInitAttr = " data-xf-init=\"time-input$xfInit\"";
		$weekStart = $this->processAttributeToRaw($controlOptions, 'week-start', '', true);
		if (!$weekStart)
		{
			$weekStart = $this->language['week_start'];
		}
		$weekStartAttr = " data-week-start=\"$weekStart\"";
		$readOnly = $this->processAttributeToRaw($controlOptions, 'readonly');
		$readOnlyAttr = $readOnly ? ' readonly="readonly"' : '';

		$dateUnhandledAttrs = $this->processUnhandledAttributes(array_merge($controlOptions, [
			'name' => $controlOptions['name'] . '[date]',
			'value' => $controlOptions['value']['date']
		]));
		$timeUnhandledAttrs = $this->processUnhandledAttributes(array_merge($controlOptions, [
			'name' => $controlOptions['name'] . '[time]',
			'value' => $controlOptions['value']['time']
		]));

		return "<div class=\"inputGroup inputGroup--auto\">" . 
				"<div class=\"inputGroup inputGroup--date inputGroup--joined inputDate\"><input type=\"text\" class=\"input input--date {$class}\"{$xfDateInitAttr}{$weekStartAttr}{$readOnlyAttr}{$dateUnhandledAttrs} /><span class=\"inputGroup-text inputDate-icon\"></span></div>&#160;&#160;" . 
				"<div class=\"inputGroup inputGroup--date inputGroup--joined inputTime\"><input type=\"text\" class=\"input input--time {$class}\"{$xfTimeInitAttr}{$timeUnhandledAttrs} /><span class=\"inputGroup-text inputTime-icon\"></span></div>" .
				"</div>";
	}

	public function formDateTimeInputRow(array $controlOptions, array $rowOptions)
	{
		$this->addToClassAttribute($rowOptions, 'formRow--input', 'rowclass');

		$controlId = $this->assignFormControlId($controlOptions);
		$controlHtml = $this->formDateTimeInput($controlOptions);
		return $this->formRow($controlHtml, $rowOptions, $controlId);
	}
}