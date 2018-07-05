<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2018 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage FilterFromTo
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

use MetaModels\Filter\Setting\Events\CreateFilterSettingFactoryEvent;
use MetaModels\Filter\Setting\FromToDateFilterSettingTypeFactory;
use MetaModels\Filter\Setting\FromToFilterSettingTypeFactory;
use MetaModels\MetaModelsEvents;

return [
    MetaModelsEvents::FILTER_SETTING_FACTORY_CREATE => [
        function (CreateFilterSettingFactoryEvent $event) {
            $event->getFactory()
                ->addTypeFactory(new FromToFilterSettingTypeFactory())
                ->addTypeFactory(new FromToDateFilterSettingTypeFactory());
        }
    ]
];
