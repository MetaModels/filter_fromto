<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage FilterFromTo
 * @author     Christian de la Haye <service@delahaye.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Setting;

use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Filter "value from x to y" for FE-filtering, based on filters by the meta models team.
 *
 * @package       MetaModels
 * @subpackage    FilterFromTo
 * @author        Christian de la Haye <service@delahaye.de>
 */
class FromToDate extends FromTo
{
	/**
	 * {@inheritdoc}
	 */
	public function prepareRules(IFilter $objFilter, $arrFilterUrl)
	{
		$objMetaModel = $this->getMetaModel();
		$objAttribute = $objMetaModel->getAttributeById($this->get('attr_id'));
		$strParamName = $this->getParamName();
		$strColname   = $objAttribute->getColName();

		$arrParamValue = null;
		if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName]))
		{
			if (is_array($arrFilterUrl[$strParamName]))
			{
				$arrParamValue = $arrFilterUrl[$strParamName];
			}
			else
			{
				$arrParamValue = explode('__', $arrFilterUrl[$strParamName]);
			}
		}

		if ($objAttribute && $strParamName && $arrParamValue && ($arrParamValue[0] || $arrParamValue[1]))
		{
			$strMore = $this->get('moreequal') ? '>=' : '>';
			$strLess = $this->get('lessequal') ? '<=' : '<';

			$arrQuery  = array();
			$arrParams = array();

			// Get form the data a timestamp for the database query.
			$arrParamValue[0] = $this->stringToDateObject($arrParamValue[0]);
			$arrParamValue[1] = $this->stringToDateObject($arrParamValue[1]);

			// Get the right format for the field.
			switch ($this->get('timetype'))
			{
				case 'time':
					$strMask    = 'time(FROM_UNIXTIME(%s)) %s STR_TO_DATE(?, \'%%%%H:%%%%i:%%%%s\')';
					$strFormate = 'H:i;s';
					break;

				case 'date':
					$strMask    = 'date(FROM_UNIXTIME(%s)) %s STR_TO_DATE(?, \'%%%%d.%%%%m.%%%%Y\')';
					$strFormate = 'd.m.Y';
					break;

				case 'datim':
					$strMask    = 'FROM_UNIXTIME(%s) %s STR_TO_DATE(?,\'%%%%d.%%%%m.%%%%Y %%%%H:%%%%i:%%%%s\')';
					$strFormate = 'd.m.Y H:i;s';
					break;

				default:
					$strMask    = '(%s%s?)';
					$strFormate = false;
					break;
			}

			// Build query and param array.
			if ($this->get('fromfield'))
			{
				if ($arrParamValue[0])
				{
					$arrQuery[]  = sprintf($strMask, $objAttribute->getColName(), $strMore);
					$arrParams[] = ($strFormate !== false)
						? $arrParamValue[0]->format($strFormate)
						:  $arrParamValue[0]->getTimestamp();
				}

				if ($arrParamValue[1])
				{
					$arrQuery[]  = sprintf($strMask, $objAttribute->getColName(), $strLess);
					$arrParams[] = ($strFormate !== false)
						? $arrParamValue[1]->format($strFormate)
						:  $arrParamValue[1]->getTimestamp();
				}
			}
			else
			{
				if ($arrParamValue[0])
				{
					$arrQuery[]  = sprintf($strMask, $objAttribute->getColName(), $strLess);
					$arrParams[] = ($strFormate !== false)
						? $arrParamValue[0]->format($strFormate)
						:  $arrParamValue[0]->getTimestamp();
				}
			}

			// Check if we have a query if not return here.
			if (empty($arrQuery))
			{
				$objFilter->addFilterRule(new StaticIdList(null));
				return;
			}

			// Build sql.
			$strSql  =  sprintf('SELECT id FROM %s WHERE ', $this->getMetaModel()->getTableName());
			$strSql .=  implode(' AND ', $arrQuery);

			// Add to filter.
			$objFilter->addFilterRule(new SimpleQuery($strSql, $arrParams));
			return;
		}

		$objFilter->addFilterRule(new StaticIdList(null));
	}

	/**
	 * Try to get from a string the timestamp build on the dateformate.
	 *
	 * @param string $string The string with the date.
	 *
	 * @return \DateTime The timestamp.
	 */
	protected function stringToDateObject($string)
	{
		// Check if we have a string.
		if (empty($string))
		{
			return '';
		}

		// Try to make a date from a string.
		$date = \DateTime::createFromFormat($this->get('dateformat'), $string);

		// Check if we have a data, if not return a empty string.
		if ($date == null)
		{
			return '';
		}

		// Make a unix timestamp from the string.
		return $date;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParameterFilterWidgets(
		$arrIds,
		$arrFilterUrl,
		$arrJumpTo,
		FrontendFilterOptions $objFrontendFilterOptions
	)
	{
		$objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

		$arrOptions = $objAttribute->getFilterOptions(
			($this->get('onlypossible') ? $arrIds : null),
			(bool)$this->get('onlyused')
		);

		// Remove empty values from list.
		foreach ($arrOptions as $mixKeyOption => $mixOption)
		{
			// Remove html/php tags.
			$mixOption = strip_tags($mixOption);
			$mixOption = trim($mixOption);

			if ($mixOption === '' || $mixOption === null)
			{
				unset($arrOptions[$mixKeyOption]);
			}
		}

		$arrLabel = array(
			($this->get('label') ? $this->get('label') : $objAttribute->getName()),
			'GET: '.$this->get('urlparam')
		);

		if ($this->get('fromfield') && $this->get('tofield'))
		{
			$arrLabel[0] .= ' '.$GLOBALS['TL_LANG']['metamodels_frontendfilter']['fromto'];
		}
		elseif($this->get('fromfield') && !$this->get('tofield'))
		{
			$arrLabel[0] .= ' '.$GLOBALS['TL_LANG']['metamodels_frontendfilter']['from'];
		}
		else
		{
			$arrLabel[0] .= ' '.$GLOBALS['TL_LANG']['metamodels_frontendfilter']['to'];
		}

		// Split up our param so the widgets can use it again.
		$strParamName   = $this->getParamName();
		$arrMyFilterUrl = $arrFilterUrl;
		// If we have a value, we have to explode it by double underscore to have a valid value which the active checks
		// may cope with.
		if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName]))
		{
			if (is_array($arrFilterUrl[$strParamName]))
			{
				$arrParamValue = $arrFilterUrl[$strParamName];
			} else {
				$arrParamValue = explode('__', $arrFilterUrl[$strParamName], 2);
			}

			if ($arrParamValue && ($arrParamValue[0] || $arrParamValue[1]))
			{
				$arrMyFilterUrl[$strParamName] = $arrParamValue;
			} else {
				// No values given, clear the array.
				$arrParamValue = null;
			}
		}

		$GLOBALS['MM_FILTER_PARAMS'][] = $this->getParamName();

		return array(
			$this->getParamName() => $this->prepareFrontendFilterWidget(
					array
					(
						'label'         => $arrLabel,
						'inputType'     => 'multitext',
						'options'       => $arrOptions,
						'timetype'      => $this->get('timetype'),
						'dateformat'    => $this->get('dateformat'),
						'eval'          => array
						(
							'multiple'  => true,
							'size'      => ($this->get('fromfield') && $this->get('tofield') ? 2 : 1),
							'urlparam'  => $this->get('urlparam'),
							'template'  => $this->get('template'),
						),
						// We need to implode to have it transported correctly in the frontend filter.
						'urlvalue'      => !empty($arrParamValue) ? implode('__', $arrParamValue) : ''
					),
					$arrMyFilterUrl,
					$arrJumpTo,
					$objFrontendFilterOptions
				)
		);
	}

}
