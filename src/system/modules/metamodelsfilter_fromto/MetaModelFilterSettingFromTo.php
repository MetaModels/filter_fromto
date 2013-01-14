<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage FrontendFilter
 * @author     Christian de la Haye <service@delahaye.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */
if (!defined('TL_ROOT'))
{
	die('You cannot access this file directly!');
}


/**
 * Filter "value from x to y" for FE-filtering, based on filters by the meta models team.
 *
 * @package	   MetaModels
 * @subpackage FrontendFilter
 * @author     Christian de la Haye <service@delahaye.de>
 */
class MetaModelFilterSettingFromTo extends MetaModelFilterSetting
{
	/**
	 * {@inheritdoc}
	 */
	protected function getParamName()
	{
		if ($this->get('urlparam'))
		{
			return $this->get('urlparam');
		}

		$objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
		if ($objAttribute)
		{
			return $objAttribute->getColName();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function prepareRules(IMetaModelFilter $objFilter, $arrFilterUrl)
	{
		$objMetaModel = $this->getMetaModel();
		$objAttribute = $objMetaModel->getAttributeById($this->get('attr_id'));
		$strParamName = $this->getParamName();
		$arrParamValue = $arrFilterUrl[$strParamName];
		$strColname = $objAttribute->getColName();

		if ($objAttribute && $strParamName && $arrParamValue)
		{
			$strMore = $this->get('moreequal') ? '>=' : '>';
			$strLess = $this->get('lessequal') ? '<=' : '<';

			if($arrParamValue[0] > 0 && $arrParamValue[1] > 0)
			{
				// from to
				$strWhere = $strColname.' '.$strMore.'? AND '.$strColname.' '.$strLess.'?';
				$arrSearch = array($arrParamValue[0], $arrParamValue[1]);
			}
			elseif($arrParamValue[0] > 0)
			{
				// from
				$strWhere = $strColname.' '.$strMore.'?';
				$arrSearch = array($arrParamValue[0]);
			}
			elseif($arrParamValue[1] > 0)
			{
				// to
				$strWhere = $strColname.' '.$strLess.'?';
				$arrSearch = array($arrParamValue[1]);
			}
			else
			{
				// nothing
				$strWhere = '';
			}

			if($strWhere)
			{
				$objQuery = Database::getInstance()->prepare(sprintf(
					'SELECT id FROM %s WHERE (%s)',
					$this->getMetaModel()->getTableName(),
					$strWhere
					))
					->execute($arrSearch);

				$arrIds = $objQuery->fetchEach('id');

				$objFilter->addFilterRule(new MetaModelFilterRuleStaticIdList($arrIds));
				return;
			}

			$objFilter->addFilterRule(new MetaModelFilterRuleStaticIdList(NULL));
		}

		$objFilter->addFilterRule(new MetaModelFilterRuleStaticIdList(NULL));
	}


	/**
	 * {@inheritdoc}
	 */
	public function getParameters()
	{
		return ($strParamName = $this->getParamName()) ? array($strParamName) : array();
	}


	/**
	 * {@inheritdoc}
	 */
	public function getParameterDCA()
	{
		$objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));

		$arrOptions = $objAttribute->getFilterOptions();

		$arrLabel = array(
			($this->get('label') ? $this->get('label') : $objAttribute->getName()),
			'GET: '.$this->get('urlparam')
			);

		if($this->get('fromfield') && $this->get('tofield'))
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

		return array(
			$this->getParamName() => array
			(
				'label'     => $arrLabel,
				'inputType' => 'multitext',
				'eval'      => array(
					'multiple'  => true,
					'size'      => 2,
					'urlparam'  => $this->get('urlparam'),
					'fromfield' => ($this->get('fromfield')? true:false), 
					'tofield'   => ($this->get('tofield')? true:false)),
					'template'  => $this->get('template')
			)
		);
	}
}

?>