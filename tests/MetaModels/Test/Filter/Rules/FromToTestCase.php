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
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Test\Filter\Rules;

use MetaModels\IMetaModel;
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
            'MetaModels\MetaModel',
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
            '\MetaModels\Attribute\BaseSimple',
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
