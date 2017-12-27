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
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\Test\FilterRule;

use MetaModels\Attribute\BaseSimple;
use MetaModels\IMetaModel;
use MetaModels\MetaModel;
use MetaModels\MetaModelsServiceContainer;
use MetaModels\Test\Contao\Database;
use MetaModels\Test\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Test the FromTo class.
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
        $metaModel = $this->getMock(
            MetaModel::class,
            array('getTableName', 'getServiceContainer'),
            array(array())
        );

        $serviceContainer = new MetaModelsServiceContainer();
        $serviceContainer
            ->setDatabase(Database::getNewTestInstance())
            ->setEventDispatcher(new EventDispatcher());

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));
        $metaModel
            ->expects($this->any())
            ->method('getServiceContainer')
            ->will($this->returnValue($serviceContainer));

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
    protected function mockAttribute($metaModel, $values = array())
    {
        $attributeData = array(
                'id'      => 1,
                'colname' => 'testAttribute',
                'name'    => 'Test Attribute'
            );

        $attribute = $this->getMock(
            BaseSimple::class,
            array(
                'filterGreaterThan',
                'filterLessThan',
                'get'
            ),
            array(
                $metaModel,
                $attributeData
            )
        );
        $attribute
            ->expects($this->any())
            ->method('getColName')
            ->will($this->returnValue($attributeData['colname']));
        $attribute
            ->expects($this->any())
            ->method('filterGreaterThan')
            ->will(
                $this->returnCallback(
                    function ($testValue, $inclusive = false) use ($values) {
                        $ids = array();
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
            ->expects($this->any())
            ->method('filterLessThan')
            ->will(
                $this->returnCallback(
                    function ($testValue, $inclusive = false) use ($values) {
                        $ids = array();
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
}
