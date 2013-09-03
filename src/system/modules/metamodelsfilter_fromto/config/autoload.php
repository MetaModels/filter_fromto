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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'MetaModels\Filter\Setting\FromTo' => 'system/modules/metamodelsfilter_fromto/MetaModels/Filter/Setting/FromTo.php',

	'MetaModelFilterSettingFromTo'     => 'system/modules/metamodelsfilter_fromto/deprecated/MetaModelFilterSettingFromTo.php',
));
