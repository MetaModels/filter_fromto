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

use MetaModels\FilterFromToBundle\FilterSetting\FromTo;

/**
 * Test the FromTo class.
 */
class FromToTest extends FromToTestCase
{
    /**
     * Provide test data.
     *
     * @return array
     */
    public function provider()
    {
        $baseSettings = array(
            'attr_id'   => 1,
            'urlparam'  => 'urlParameter',
            'label'     => 'Test',
            'template'  => '',
            'moreequal' => 0,
            'lessequal' => 0,
            'fromfield' => 1,
            'tofield'   => 1,
        );

        $baseData = array(
            1 => '10',
            2 => '20',
            3 => '30',
            4 => '40',
            5 => '50',
            6 => '60',
        );

        return array(
            1 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '10__40'),
                'expected'      => array(2, 3),
                'message'       => 'filtering with exclusive'
            ),
            2 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'lessequal' => 1,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '10__30'),
                'expected'      => array(2, 3),
                'message'       => 'filtering with end of range inclusive.'
            ),
            3 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'moreequal' => 1,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '10__40'),
                'expected'      => array(1, 2, 3),
                'message'       => 'filtering with start of range inclusive.'
            ),
            4 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'moreequal' => 1,
                        'lessequal' => 1,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '10__15'),
                'expected'      => array(1),
                'message'       => 'filtering with start and end of range inclusive.'
            ),
            5 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '40'),
                'expected'      => array(5, 6),
                'message'       => 'filtering two fields with exclusive but only one value given'
            ),
            6 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'tofield' => 0,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '40'),
                'expected'      => array(5, 6),
                'message'       => 'filtering only start field with exclusive and one value given'
            ),
            7 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'fromfield' => 0,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '40'),
                'expected'      => array(1, 2, 3),
                'message'       => 'filtering only end field with exclusive and one value given'
            ),
            8 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'fromfield' => 0,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '100__400'),
                'expected'      => '\LengthException',
                'message'       => 'filtering only end field with exclusive and two values given'
            ),
            9 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'attr_id' => 'invalid',
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '1'),
                'expected'      => null,
                'message'       => 'ignore filtering with invalid attribute'
            ),
            10 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'fromfield' => 0,
                        'tofield'   => 0,
                    )
                ),
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '1'),
                'expected'      => null,
                'message'       => 'ignore filtering when neither start nor end are checked.'
            ),
            11 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => null),
                'expected'      => null,
                'message'       => 'ignore filtering when nothing provided in the url.'
            ),
            12 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => array('10', '40')),
                'expected'      => array(2, 3),
                'message'       => 'filtering exclusive with array values'
            ),
            13 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '__40'),
                'expected'      => array(1, 2, 3),
                'message'       => 'filtering only end passed - https://github.com/MetaModels/filter_fromto/issues/13'
            ),
            14 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => '40__'),
                'expected'      => array(5, 6),
                'message'       => 'filtering only start passed - https://github.com/MetaModels/filter_fromto/issues/13'
            ),
            15 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'moreequal' => 1,
                        'lessequal' => 1,
                    )
                ),
                'data'          => array(
                    1 => '1.1',
                    2 => '1.1',
                    3 => '1.2',
                    4 => '1.3',
                    5 => '1',
                    6 => '1.4',
                    7 => '0',
                ),
                'filterValues'  => array('urlParameter' => '1.0__1.2'),
                'expected'      => array(1, 2, 3, 5),
                'message'       => 'filtering decimal - https://github.com/MetaModels/filter_fromto/issues/12'
            ),
            16 => array(
                'filterSetting' => array_replace_recursive(
                    $baseSettings,
                    array(
                        'moreequal' => 1,
                        'lessequal' => 1,
                    )
                ),
                'data'          => array(
                    1 => 1.1,
                    2 => 1.1,
                    3 => 1.2,
                    4 => 1.3,
                    5 => 1,
                    6 => 1.4,
                    7 => 0,
                ),
                'filterValues'  => array('urlParameter' => '1.0__1.2'),
                'expected'      => array(1, 2, 3, 5),
                'message'       => 'filtering decimal - https://github.com/MetaModels/filter_fromto/issues/12'
            ),
        );
    }

    /**
     * Test the functionality.
     *
     * @param array  $filterSettingData The initialization data for the filter setting.
     *
     * @param array  $data              The data for the attribute.
     *
     * @param array  $filterValues      The url values.
     *
     * @param array  $expected          The expected ids.
     *
     * @param string $message           The assert message.
     *
     * @return void
     *
     * @dataProvider provider
     */
    public function testFunctionality($filterSettingData, $data, $filterValues, $expected, $message)
    {
        $filterSetting = $this->mockFilterSetting();
        $metaModel     = $filterSetting->getMetaModel();

        $this->mockAttribute($metaModel, array(), $data);

        $filterSetting = new FromTo($filterSetting, $filterSettingData);

        $filter = $metaModel->getEmptyFilter();

        if (!is_string($expected)) {
            $filterSetting->prepareRules($filter, $filterValues);
            $this->assertEquals(
                $expected,
                $filter->getMatchingIds(),
                $message
            );
        } else {
            try {
                $filterSetting->prepareRules($filter, $filterValues);
            } catch (\Exception $exception) {
                $this->assertInstanceOf($expected, $exception, $message);
                return;
            }

            $this->fail('Expected exception of type: ' . $expected);
        }
    }
}
