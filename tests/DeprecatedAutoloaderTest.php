<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2022 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/filter_fromto
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\Test;

use MetaModels\FilterFromToBundle\FilterRule\FromTo as FromToRule;
use MetaModels\FilterFromToBundle\FilterRule\FromToDate as FromToDateRule;
use MetaModels\FilterFromToBundle\FilterSetting\AbstractFromTo;
use MetaModels\FilterFromToBundle\FilterSetting\FromTo as FromToSetting;
use MetaModels\FilterFromToBundle\FilterSetting\FromToDate as FromToDateSetting;
use MetaModels\FilterFromToBundle\FilterSetting\FromToDateFilterSettingTypeFactory;
use MetaModels\FilterFromToBundle\FilterSetting\FromToFilterSettingTypeFactory;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 *
 * @covers \MetaModels\FilterFromToBundle\DeprecatedAutoloader
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * Selectes of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
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

    /**
     * Provide the alias class map.
     *
     * @return array
     */
    public function provideAliasClassMap()
    {
        $values = [];

        foreach (static::$classes as $select => $class) {
            $values[] = [
                $select,
                $class,
            ];
        }

        return $values;
    }

    /**
     * Test if the deprecated classes are aliased to the new one.
     *
     * @param string $oldClass Old class name.
     * @param string $newClass New class name.
     *
     * @dataProvider provideAliasClassMap
     */
    public function testDeprecatedClassesAreAliased($oldClass, $newClass)
    {
        $this->assertTrue(\class_exists($oldClass), \sprintf('Class select "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
