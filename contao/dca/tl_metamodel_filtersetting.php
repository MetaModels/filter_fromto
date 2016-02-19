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
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <mail@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

// From/To normal.
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+config'][]   =
    'urlparam';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'label';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'template';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'moreequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'lessequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'fromfield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'tofield';

// From/To for date.
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+config'][]   =
    'urlparam';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'dateformat';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'timetype';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'label';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'template';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'moreequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'lessequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'fromfield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'tofield';

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['moreequal'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['moreequal'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class' => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['lessequal'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['lessequal'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class' => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['fromfield'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['fromfield'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class' => 'w50 clr'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['tofield'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['tofield'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => array
    (
        'tl_class' => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['dateformat'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['dateformat'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => array
    (
        'tl_class' => 'w50'
    )
);

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['timetype'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetype'],
    'exclude'   => true,
    'inputType' => 'select',
    'reference' => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetypeOptions'],
    'options'   => array
    (
        'time',
        'date',
        'datim'
    ),
    'eval'      => array
    (
        'doNotSaveEmpty' => true,
        'tl_class'       => 'w50'
    )
);
