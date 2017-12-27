<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage FilterFromToBundle
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

/**
 * filter types
 */
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['typenames']['fromto']     = 'Value from/to';
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['typenames']['fromtodate'] = 'Value from/to for date';


/**
 * fields
 */
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['dateformat']               = array('Dateformate', 'Here you can add the date format. PHP need this information for the transforming from a string to a unix timestamp.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['moreequal']                = array('Value 1 included', 'Standard: excluded.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['lessequal']                = array('Value 2 included', 'Standard: excluded.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['fromfield']                = array('Field for value 1', 'Show FE field for value no 1.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['tofield']                  = array('Field for value 2', 'Show FE field for value no 2.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetype']                 = array('Schema', 'Here you can select the desired scheme.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetypeOptions']['time']  = 'Time';
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetypeOptions']['date']  = 'Date';
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetypeOptions']['datim'] = 'Date and time';
