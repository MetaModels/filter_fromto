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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Test\Filter\Rules;

use MetaModels\Attribute\BaseSimple;
use MetaModels\IMetaModel;
use MetaModels\IMetaModelsServiceContainer;
use PHPUnit\Framework\TestCase;

/**
 * Test the FromTo class.
 */
class FromToTestCase extends TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function setUp()
    {
        parent::setUp();
        $GLOBALS['container']['metamodels-service-container'] =
            $this->getMockForAbstractClass(IMetaModelsServiceContainer::class);
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
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));
        $metaModel
            ->expects($this->never())
            ->method('getServiceContainer');

        return $metaModel;
    }

    /**
     * Mock an attribute.
     *
     * @param IMetaModel $metaModel The metamodel.
     *
     * @param array      $values    The test values.
     *
     * @return \MetaModels\Attribute\ISimple
     */
    protected function mockAttribute($metaModel, $values = [])
    {
        $attributeData = [
                'id'      => 1,
                'colname' => 'testAttribute',
                'name'    => 'Test Attribute'
        ];

        $attribute = $this
            ->getMockBuilder(BaseSimple::class)
            ->setMethods(['getColName', 'filterGreaterThan', 'filterLessThan', 'get'])
            ->setConstructorArgs([$metaModel, $attributeData])
            ->getMock();
        $attribute
            ->expects($this->any())
            ->method('getColName')
            ->will($this->returnValue($attributeData['colname']));
        $attribute
            ->expects($this->any())
            ->method('filterGreaterThan')
            ->willReturnCallback(
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
            );
        $attribute
            ->expects($this->any())
            ->method('filterLessThan')
            ->willReturnCallback(
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
            );

        /** @var \MetaModels\Attribute\ISimple $attribute */
        $metaModel->addAttribute($attribute);

        return $attribute;
    }
}
