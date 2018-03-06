<?php
/**
 * The MetaModels extension allows the creation of multiple collections of custom items,
 * each with its own unique set of selectable attributes, with attribute extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the
 * data in each collection.
 *
 * PHP version 5
 *
 * @package    MetaModels
 * @subpackage FilterFromTo
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
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
    protected function mockAttribute($metaModel, $values = array())
    {
        $attributeData = array(
                'id'      => 1,
                'colname' => 'testAttribute',
                'name'    => 'Test Attribute'
            );

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
            );
        $attribute
            ->expects($this->any())
            ->method('filterLessThan')
            ->willReturnCallback(
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
            );

        /** @var \MetaModels\Attribute\ISimple $attribute */
        $metaModel->addAttribute($attribute);

        return $attribute;
    }
}
