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

namespace MetaModels\FilterFromToBundle\Test\FilterSetting;

use MetaModels\Attribute\BaseSimple;
use MetaModels\IMetaModel;

/**
 * Some base methods for easy mocking of objects.
 */
class FromToTestCase extends TestCase
{
    /**
     * Create a return callback.
     *
     * @param array $array The array to return values from.
     *
     * @return \PHPUnit_Framework_MockObject_Stub_ReturnCallback
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
     * @return \MetaModels\Attribute\ISimple
     */
    protected function mockAttribute(
        $metaModel,
        $attributeData = array(),
        $values = array()
    ) {
        $attributeData = array_replace_recursive(
            array(
                'id'      => 1,
                'colname' => 'testAttribute',
                'name'    => 'Test Attribute'
            ),
            $attributeData
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
            ->method('get')
            ->will($this->createReturnCallback($attributeData));
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
