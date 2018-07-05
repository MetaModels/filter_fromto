<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2018 The MetaModels team.
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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
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
        $baseSettings = [
            'attr_id'   => 1,
            'urlparam'  => 'urlParameter',
            'label'     => 'Test',
            'template'  => '',
            'moreequal' => 0,
            'lessequal' => 0,
            'fromfield' => 1,
            'tofield'   => 1,
        ];

        $baseData = [
            1 => '10',
            2 => '20',
            3 => '30',
            4 => '40',
            5 => '50',
            6 => '60',
        ];

        return [
            1 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '10__40'],
                'expected'      => [2, 3],
                'message'       => 'filtering with exclusive'
            ],
            2 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'lessequal' => 1,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '10__30'],
                'expected'      => [2, 3],
                'message'       => 'filtering with end of range inclusive.'
            ],
            3 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'moreequal' => 1,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '10__40'],
                'expected'      => [1, 2, 3],
                'message'       => 'filtering with start of range inclusive.'
            ],
            4 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'moreequal' => 1,
                        'lessequal' => 1,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '10__15'],
                'expected'      => [1],
                'message'       => 'filtering with start and end of range inclusive.'
            ],
            5 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '40'],
                'expected'      => [5, 6],
                'message'       => 'filtering two fields with exclusive but only one value given'
            ],
            6 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'tofield' => 0,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '40'],
                'expected'      => [5, 6],
                'message'       => 'filtering only start field with exclusive and one value given'
            ],
            7 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'fromfield' => 0,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '40'],
                'expected'      => [1, 2, 3],
                'message'       => 'filtering only end field with exclusive and one value given'
            ],
            8 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'fromfield' => 0,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '100__400'],
                'expected'      => '\LengthException',
                'message'       => 'filtering only end field with exclusive and two values given'
            ],
            9 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'attr_id' => 'invalid',
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '1'],
                'expected'      => null,
                'message'       => 'ignore filtering with invalid attribute'
            ],
            10 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'fromfield' => 0,
                        'tofield'   => 0,
                    ]
                ),
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '1'],
                'expected'      => null,
                'message'       => 'ignore filtering when neither start nor end are checked.'
            ],
            11 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => null],
                'expected'      => null,
                'message'       => 'ignore filtering when nothing provided in the url.'
            ],
            12 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => ['10', '40']],
                'expected'      => [2, 3],
                'message'       => 'filtering exclusive with array values'
            ],
            13 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '__40'],
                'expected'      => [1, 2, 3],
                'message'       => 'filtering only end passed - https://github.com/MetaModels/filter_fromto/issues/13'
            ],
            14 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => '40__'],
                'expected'      => [5, 6],
                'message'       => 'filtering only start passed - https://github.com/MetaModels/filter_fromto/issues/13'
            ],
            15 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'moreequal' => 1,
                        'lessequal' => 1,
                    ]
                ),
                'data'          => [
                    1 => '1.1',
                    2 => '1.1',
                    3 => '1.2',
                    4 => '1.3',
                    5 => '1',
                    6 => '1.4',
                    7 => '0',
                ],
                'filterValues'  => ['urlParameter' => '1.0__1.2'],
                'expected'      => [1, 2, 3, 5],
                'message'       => 'filtering decimal - https://github.com/MetaModels/filter_fromto/issues/12'
            ],
            16 => [
                'filterSetting' => \array_replace_recursive(
                    $baseSettings,
                    [
                        'moreequal' => 1,
                        'lessequal' => 1,
                    ]
                ),
                'data'          => [
                    1 => 1.1,
                    2 => 1.1,
                    3 => 1.2,
                    4 => 1.3,
                    5 => 1,
                    6 => 1.4,
                    7 => 0,
                ],
                'filterValues'  => ['urlParameter' => '1.0__1.2'],
                'expected'      => [1, 2, 3, 5],
                'message'       => 'filtering decimal - https://github.com/MetaModels/filter_fromto/issues/12'
            ],
        ];
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

        $this->mockAttribute($metaModel, [], $data);

        $filterSetting = new FromTo($filterSetting, $filterSettingData);

        $filter = $metaModel->getEmptyFilter();

        if (!\is_string($expected)) {
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
