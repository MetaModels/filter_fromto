<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use Contao\Config;

// From/To normal.
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'urlparam';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'label';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'hide_label';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'template';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'placeholder';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'moreequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'lessequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'fromfield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'tofield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromto extends _attribute_']['+fefilter'][] =
    'cssID';

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
    'hide_label';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'template';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'placeholder';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'moreequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'lessequal';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'fromfield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'tofield';
$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['metapalettes']['fromtodate extends _attribute_']['+fefilter'][] =
    'cssID';

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['moreequal'] = [
    'label'       => 'moreequal.label',
    'description' => 'moreequal.description',
    'exclude'     => true,
    'default'     => true,
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'w50',
    ],
    'sql'         => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['lessequal'] = [
    'label'       => 'lessequal.label',
    'description' => 'lessequal.description',
    'exclude'     => true,
    'default'     => true,
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'w50',
    ],
    'sql'         => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['fromfield'] = [
    'label'       => 'fromfield.label',
    'description' => 'fromfield.description',
    'exclude'     => true,
    'default'     => true,
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'w50 clr',
    ],
    'sql'         => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['tofield'] = [
    'label'       => 'tofield.label',
    'description' => 'tofield.description',
    'exclude'     => true,
    'default'     => true,
    'inputType'   => 'checkbox',
    'eval'        => [
        'tl_class' => 'w50',
    ],
    'sql'         => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['dateformat'] = [
    'label'       => 'dateformat.label',
    'description' => 'dateformat.description',
    'exclude'     => true,
    'inputType'   => 'text',
    'default'     => Config::get('dateFormat'),
    'eval'        => [
        'mandatory' => true,
        'tl_class'  => 'w50',
    ],
    'sql'         => "char(32) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['timetype'] = [
    'label'       => 'timetype.label',
    'description' => 'timetype.description',
    'exclude'     => true,
    'inputType'   => 'select',
    'options'     => [
        'date',
        'datim',
        'time',
    ],
    'reference'   => [
        'date'  => 'timetypeOptions.date',
        'datim' => 'timetypeOptions.datim',
        'time'  => 'timetypeOptions.time',
    ],
    'eval'        => [
        'doNotSaveEmpty' => true,
        'tl_class'       => 'w50',
    ],
    'sql'         => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_metamodel_filtersetting']['fields']['placeholder'] = [
    'label'       => 'placeholder.label',
    'description' => 'placeholder.description',
    'exclude'     => true,
    'inputType'   => 'text',
    'eval'        => ['tl_class' => 'clr w50'],
    'sql'         => ['type' => 'string', 'length' => 255, 'default' => ''],
];
