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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\Test\FilterSetting;

use MetaModels\Attribute\Base;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\Filter;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\IMetaModel;

/**
 * Some base methods for easy mocking of objects.
 */
class FromToTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Create a return callback.
     *
     * @param array $array The array to return values from.
     *
     * @return \PHPUnit\Framework\MockObject\Stub\ReturnCallback
     *
     * @throws \InvalidArgumentException The returned callback will throw an exception for unknown values.
     */
    private function createReturnCallback($array)
    {
        return $this->returnCallback(
            function ($name) use ($array) {
                if (!isset($array[$name])) {
                    throw new \InvalidArgumentException('Unknown array key ' . $name);
                }

                return $array[$name];
            }
        );
    }

    /**
     * Mock an attribute.
     *
     * @param IMetaModel $metaModel     The metamodel.
     *
     * @param array      $attributeData The attribute data.
     *
     * @param array      $values        The test values.
     *
     * @return \MetaModels\Attribute\IAttribute
     */
    protected function mockAttribute(
        $metaModel,
        $attributeData = [],
        $values = []
    ) {
        $attributeData = \array_replace_recursive(
            [
                'id'      => 1,
                'colname' => 'testAttribute',
                'name'    => 'Test Attribute'
            ],
            $attributeData
        );

        $attribute = $this
            ->getMockBuilder(Base::class)
            ->setMethods(['filterGreaterThan', 'filterLessThan', 'get'])
            ->setConstructorArgs([$metaModel, $attributeData])
            ->getMockForAbstractClass();

        $attribute
            ->method('getFilterOptions')
            ->willReturn([]);

        $attribute
            ->method('get')
            ->will($this->createReturnCallback($attributeData));
        $attribute
            ->method('filterGreaterThan')
            ->will(
                $this->returnCallback(
                    function ($testValue, $inclusive = false) use ($values) {
                        $ids = [];
                        foreach ($values as $itemId => $value) {
                            if ($inclusive) {
                                if ($value >= $testValue) {
                                    $ids[] = $itemId;
                                }
                            } elseif ($value > $testValue) {
                                $ids[] = $itemId;
                            }
                        }

                        return $ids;
                    }
                )
            );
        $attribute
            ->method('filterLessThan')
            ->will(
                $this->returnCallback(
                    function ($testValue, $inclusive = false) use ($values) {
                        $ids = [];
                        foreach ($values as $itemId => $value) {
                            if ($inclusive) {
                                if ($value <= $testValue) {
                                    $ids[] = $itemId;
                                }
                            } elseif ($value < $testValue) {
                                $ids[] = $itemId;
                            }
                        }

                        return $ids;
                    }
                )
            );

        /** @var \MetaModels\Attribute\ISimple $attribute */
        $metaModel->addAttribute(
            $attribute
        );

        return $attribute;
    }

    /**
     * Mock an ICollection.
     *
     * @param string $tableName The table name of the MetaModel to mock (optional, defaults to "mm_unittest").
     *
     * @return ICollection
     */
    protected function mockFilterSetting($tableName = 'mm_unittest')
    {
        $filterSetting = $this->getMockForAbstractClass(ICollection::class);

        $filterSetting
            ->method('getMetaModel')
            ->willReturn($this->mockMetaModel($tableName));

        return $filterSetting;
    }

    /**
     * Mock a MetaModel.
     *
     * @param string $tableName The table name of the MetaModel to mock (optional, defaults to "mm_unittest").
     *
     * @return IMetaModel
     */
    protected function mockMetaModel($tableName = 'mm_unittest')
    {
        $metaModel = $this->getMockForAbstractClass(IMetaModel::class);

        $metaModel
            ->method('getTableName')
            ->willReturn($tableName);
        $metaModel
            ->method('getEmptyFilter')
            ->willReturn(new Filter($metaModel));
        $metaModel
            ->expects($this->never())
            ->method('getServiceContainer');

        /** @var IAttribute[] $attributes */
        $attributes = [];

        $metaModel
            ->method('addAttribute')
            ->willReturnCallback(function ($attribute) use (&$attributes) {
                $attributes[] = $attribute;
            });

        $metaModel
            ->method('getAttributeById')
            ->willReturnCallback(function ($id) use (&$attributes) {
                foreach ($attributes as $attribute) {
                    if ($attribute->get('id') === $id) {
                        return $attribute;
                    }
                }
                return null;
            });

        return $metaModel;
    }
}