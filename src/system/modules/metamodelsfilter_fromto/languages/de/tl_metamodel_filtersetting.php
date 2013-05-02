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
 * filter types
 */
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['typenames']['fromto']   = 'Wert von-/bis';


/**
 * fields
 */
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['moreequal']    = array('Wert 1 eingeschlossen', 'Standard: nicht eingeschlossen.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['lessequal']    = array('Wert 2 eingeschlossen', 'Standard: nicht eingeschlossen.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['fromfield']    = array('Feld für Wert 1', 'FE-Feld für Wert 1 zeigen.');
$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['tofield']      = array('Feld für Wert 2', 'FE-Feld für Wert 2 zeigen.');