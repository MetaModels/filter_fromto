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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\FilterSetting;

use MetaModels\Filter\Setting\AbstractFilterSettingTypeFactory;

/**
 * Attribute type factory for from-to filter settings.
 */
class FromToDateFilterSettingTypeFactory extends AbstractFilterSettingTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this
            ->setTypeName('fromtodate')
            ->setTypeIcon('bundles/metamodelsfilterfromto/filter_fromto.png')
            ->setTypeClass(FromToDate::class)
            ->allowAttributeTypes('numeric', 'decimal', 'timestamp');
    }
}
