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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\Test\FilterSetting;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttribute;
use MetaModels\Attribute\ISimple;
use MetaModels\Filter\Filter;
use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Some base methods for easy mocking of objects.
 *
 * @covers \MetaModels\FilterFromToBundle\FilterSetting\FromToTest
 */
class FromToTestCase extends TestCase
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
            ->getMockBuilder(ISimple::class)
            ->setMethods(['filterGreaterThan', 'filterLessThan', 'get', 'getName', 'getColName', 'getMetaModel'])
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

        $attribute
            ->method('getMetaModel')
            ->willReturn($metaModel);

        $attribute
            ->method('getColName')
            ->willReturn($attributeData['colname']);

        $attribute
            ->method('getName')
            ->willReturn($attributeData['name']);

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

    /**
     * Mock a database connection.
     *
     * @return Connection
     */
    protected function mockConnection()
    {
        return $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Mock an event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    protected function mockDispatcher()
    {
        return $this->getMockForAbstractClass(EventDispatcherInterface::class);
    }

    /**
     * Mock an url builder.
     *
     * @return FilterUrlBuilder
     */
    protected function mockUrlBuilder()
    {
        return $this->getMockBuilder(FilterUrlBuilder::class)->disableOriginalConstructor()->getMock();
    }
}
