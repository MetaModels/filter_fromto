<?php

/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 * @package    MetaModels
 * @subpackage FilterFromTo
 * @author     Christian de la Haye <service@delahaye.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Frontend filter normale
 */

$GLOBALS['METAMODELS']['filters']['fromto']['class']         = 'MetaModels\Filter\Setting\FromTo';
$GLOBALS['METAMODELS']['filters']['fromto']['image']         =
	'system/modules/metamodelsfilter_fromto/html/filter_fromto.png';
$GLOBALS['METAMODELS']['filters']['fromto']['info_callback'] = array(
	'MetaModels\DcGeneral\Events\Table\FilterSetting\DrawSetting',
	'modelToLabelWithAttributeAndUrlParam'
);
$GLOBALS['METAMODELS']['filters']['fromto']['attr_filter'][] = 'numeric';
$GLOBALS['METAMODELS']['filters']['fromto']['attr_filter'][] = 'decimal';

/**
 * Frontend filter date
 */

$GLOBALS['METAMODELS']['filters']['fromtodate']['class']         = 'MetaModels\Filter\Setting\FromToDate';
$GLOBALS['METAMODELS']['filters']['fromtodate']['image']         =
	'system/modules/metamodelsfilter_fromto/html/filter_fromto.png';
$GLOBALS['METAMODELS']['filters']['fromtodate']['info_callback'] = array(
	'MetaModels\DcGeneral\Events\Table\FilterSetting\DrawSetting',
	'modelToLabelWithAttributeAndUrlParam'
);
$GLOBALS['METAMODELS']['filters']['fromtodate']['attr_filter'][] = 'numeric';
$GLOBALS['METAMODELS']['filters']['fromtodate']['attr_filter'][] = 'decimal';
$GLOBALS['METAMODELS']['filters']['fromtodate']['attr_filter'][] = 'timestamp';
