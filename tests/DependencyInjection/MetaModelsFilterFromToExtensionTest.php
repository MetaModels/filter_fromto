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
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\Test\DependencyInjection;

use MetaModels\FilterFromToBundle\FilterSetting\FromToFilterSettingTypeFactory;
use MetaModels\FilterFromToBundle\DependencyInjection\MetaModelsFilterFromToExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 *
 * @covers \MetaModels\FilterFromToBundle\DependencyInjection\MetaModelsFilterFromToExtension
 */
class MetaModelsFilterFromToExtensionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $extension = new MetaModelsFilterFromToExtension();

        $this->assertInstanceOf(MetaModelsFilterFromToExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    public function testFactoryIsRegistered(): void
    {
        $container = new ContainerBuilder();

        $extension = new MetaModelsFilterFromToExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasDefinition('metamodels.filter_fromto.factory'));
        $definition = $container->getDefinition('metamodels.filter_fromto.factory');
        self::assertCount(1, $definition->getTag('metamodels.filter_factory'));
    }
}
