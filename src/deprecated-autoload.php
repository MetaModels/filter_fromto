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
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

// This hack is to load the "old locations" of the classes.

use MetaModels\FilterFromToBundle\FilterRule\FromTo as FromToRule;
use MetaModels\FilterFromToBundle\FilterRule\FromToDate as FromToDateRule;
use MetaModels\FilterFromToBundle\FilterSetting\AbstractFromTo;
use MetaModels\FilterFromToBundle\FilterSetting\FromTo as FromToSetting;
use MetaModels\FilterFromToBundle\FilterSetting\FromToDate as FromToDateSetting;
use MetaModels\FilterFromToBundle\FilterSetting\FromToDateFilterSettingTypeFactory;
use MetaModels\FilterFromToBundle\FilterSetting\FromToFilterSettingTypeFactory;

spl_autoload_register(
    function ($class) {
        static $classes = [
            // FilterSetting
            'MetaModels\Filter\Setting\AbstractFromTo'                     => AbstractFromTo::class,
            'MetaModels\Filter\Setting\FromTo'                             => FromToSetting::class,
            'MetaModels\Filter\Setting\FromToDate'                         => FromToDateSetting::class,
            'MetaModels\Filter\Setting\FromToDateFilterSettingTypeFactory' => FromToDateFilterSettingTypeFactory::class,
            'MetaModels\Filter\Setting\FromToFilterSettingTypeFactory'     => FromToFilterSettingTypeFactory::class,
            // FilterRule
            'MetaModels\Filter\Rules\FromTo'                               => FromToRule::class,
            'MetaModels\Filter\Rules\FromToDate'                           => FromToDateRule::class,
        ];

        if (isset($classes[$class])) {
            // @codingStandardsIgnoreStart Silencing errors is discouraged
            @trigger_error('Class "'.$class.'" has been renamed to "'.$classes[$class].'"', E_USER_DEPRECATED);
            // @codingStandardsIgnoreEnd

            if (!class_exists($classes[$class])) {
                spl_autoload_call($class);
            }

            class_alias($classes[$class], $class);
        }
    }
);
