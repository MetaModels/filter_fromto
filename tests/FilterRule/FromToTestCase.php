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

namespace MetaModels\FilterFromToBundle\Test\FilterRule;

use Doctrine\DBAL\Connection;
use MetaModels\Attribute\ISimple;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Test the FromTo class.
 *
 * @covers \MetaModels\FilterFromToBundle\FilterRule\FromTo
 */
class FromToTestCase extends TestCase
{
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
            ->expects($this->never())
            ->method('getServiceContainer');

        return $metaModel;
    }

    /**
     * Mock an attribute.
     *
     * @param IMetaModel $metaModel The metamodel.
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
            ->getMockBuilder(ISimple::class)
            ->setMethods(['getColName', 'filterGreaterThan', 'filterLessThan', 'get', 'getMetaModel'])
            ->getMockForAbstractClass();
        $attribute
            ->method('getColName')
            ->willReturn($attributeData['colname']);
        $attribute
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

        $attribute
            ->method('getMetaModel')
            ->willReturn($metaModel);

        /** @var \MetaModels\Attribute\ISimple $attribute */
        $metaModel->addAttribute($attribute);

        return $attribute;
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
}
