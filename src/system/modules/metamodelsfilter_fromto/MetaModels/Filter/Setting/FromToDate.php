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

			if ($this->get('fromfield'))
			{
				if ($arrParamValue[0])
				{
					$arrQuery[]  = sprintf('(%s%s?)', $objAttribute->getColName(), $strMore);
					$arrParams[] = $arrParamValue[0];
				}
				if ($arrParamValue[1])
				{
					$arrQuery[]  = sprintf('(%s%s?)', $objAttribute->getColName(), $strLess);
					$arrParams[] = $arrParamValue[1];
				}
			}
			else
			{
				if ($arrParamValue[0])
				{
					$arrQuery[]  = sprintf('(%s%s?)', $objAttribute->getColName(), $strLess);
					$arrParams[] = $arrParamValue[0];
				}
			}

			$objFilter->addFilterRule(new SimpleQuery(
					sprintf('
					SELECT id
					FROM %s
					WHERE ',
						$this->getMetaModel()->getTableName()
					) . implode(' AND ', $arrQuery),
					$arrParams
				)
			);
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
}
