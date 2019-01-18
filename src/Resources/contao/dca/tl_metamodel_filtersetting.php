<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/filter_fromto
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <mail@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

// From/To normal.
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
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
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
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

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['moreequal'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['moreequal'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['lessequal'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['lessequal'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['fromfield'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['fromfield'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50 clr',
    ],
    'sql'       => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['tofield'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['tofield'],
    'exclude'   => true,
    'default'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['dateformat'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['dateformat'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'tl_class' => 'w50',
    ],
    'sql'       => "char(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['timetype'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetype'],
    'exclude'   => true,
    'inputType' => 'select',
    'reference' => &$GLOBALS['TL_LANG']['tl_metamodel_filtersetting']['timetypeOptions'],
    'options'   => [
        'time',
        'date',
        'datim',
    ],
    'eval'      => [
        'doNotSaveEmpty' => true,
        'tl_class'       => 'w50',
    ],
    'sql'       => "varchar(64) NOT NULL default ''",
];
