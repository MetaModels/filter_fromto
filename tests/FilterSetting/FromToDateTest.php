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

use MetaModels\FilterFromToBundle\FilterSetting\FromToDate;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Test the FromTo class.
 */
class FromToDateTest extends FromToTestCase
{
    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function setUp()
    {
        parent::setUp();

        $GLOBALS['TL_LANG'] = [
            'metamodels_frontendfilter' => [
                'fromto' => '',
                'from'   => '',
                'to'     => '',
            ]
        ];
    }

    /**
     * Provide test data.
     *
     * @return array
     */
    public function provider()
    {
        $baseSettings = [
            'attr_id'    => 1,
            'urlparam'   => 'urlParameter',
            'label'      => 'Test',
            'template'   => '',
            'moreequal'  => 0,
            'lessequal'  => 0,
            'fromfield'  => 1,
            'tofield'    => 1,
            'dateformat' => 'Y-m-d-H-i-s',
            'timetype'   => 'datim'
        ];

        $baseData = [
            1 => \strtotime('1985-01-01T11:00:00+00:00'),
            2 => \strtotime('1990-01-01T11:00:00+00:00'),
            3 => \strtotime('1995-01-01T11:00:00+00:00'),
            4 => \strtotime('2000-01-01T11:00:00+00:00'),
            5 => \strtotime('2010-01-01T01:00:00+00:00'),
            6 => \strtotime('2015-01-01T01:00:00+00:00'),
        ];

        $generateUrlValue = function ($start, $end = null) use ($baseSettings) {
            $value = \is_int($start) ? \date($baseSettings['dateformat'], $start) : $start;
            if ($end) {
                $value .= ',' . (\is_int($end) ? \date($baseSettings['dateformat'], $end) : $end);
            }

            return $value;
        };

        return [
            1 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1], $baseData[4])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1], $baseData[3])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1], $baseData[4])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1], $baseData[2])],
                'expected'      => [1, 2],
                'message'       => 'filtering with start and end of range inclusive.'
            ],
            5 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[4])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[4])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[4])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1], $baseData[2])],
                'expected'      => \LengthException::class,
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1])],
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
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[1])],
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
                'filterValues'  => [
                    'urlParameter' => [
                        $generateUrlValue($baseData[1]),
                        $generateUrlValue($baseData[4])
                    ]
                ],
                'expected'      => [2, 3],
                'message'       => 'filtering exclusive with array values'
            ],
            13 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => $generateUrlValue('broken', $baseData[4])],
                'expected'      => [],
                'message'       => 'filtering exclusive with broken start but valid end.'
            ],
            14 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[4], 'broken')],
                'expected'      => [],
                'message'       => 'filtering exclusive with valid start but broken end.'
            ],
            16 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => $generateUrlValue('', $baseData[4])],
                'expected'      => [1, 2, 3],
                'message'       => 'filtering exclusive with empty start but valid end.'
            ],
            17 => [
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => ['urlParameter' => $generateUrlValue($baseData[4], '')],
                'expected'      => [5, 6],
                'message'       => 'filtering exclusive with valid start but empty end.'
            ],
        ];
    }

    /**
     * Test the functionality.
     *
     * @param array             $filterSettingData The initialization data for the filter setting.
     * @param array             $data              The data for the attribute.
     * @param array             $filterValues      The url values.
     * @param array|string|null $expected          The expected ids or the class name of the expected exception.
     * @param string            $message           The assert message.
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

        $filterSetting = new FromToDate(
            $filterSetting,
            $filterSettingData,
            $this->mockConnection(),
            $this->mockDispatcher(),
            $this->mockUrlBuilder());

        $filter = $metaModel->getEmptyFilter();

        if (!\is_string($expected)) {
            $filterSetting->prepareRules($filter, $filterValues);
            $this->assertEquals(
                $expected,
                $filter->getMatchingIds(),
                $message
            );
        } else {
            $this->expectException($expected);

            $filterSetting->prepareRules($filter, $filterValues);
        }
    }

    /**
     * Test the generating of the widget.
     *
     * @return void
     */
    public function testGetParameterFilterWidgets()
    {
        $that          = $this;
        $filterSetting = $this->mockFilterSetting();
        $urlParameter  = \date('Y-m-d-H-i-s', 473425200) . ',' . \date('Y-m-d-H-i-s', 1420074000);
        $fromTo        = $this
            ->getMockBuilder(FromToDate::class)
            ->setMethods(['prepareFrontendFilterWidget'])
            ->setConstructorArgs([
                $filterSetting,
                [
                    'attr_id'   => 1,
                    'urlparam'  => 'urlParameter',
                    'label'     => 'Test filter',
                    'template'  => '',
                    'moreequal' => 1,
                    'lessequal' => 1,
                    'fromfield' => 1,
                    'tofield'   => 1,
                    'dateformat' => 'Y-m-d-H-i-s',
                    'timetype'   => 'datim'
                ],
                $this->mockConnection(),
                $this->mockDispatcher(),
                $this->mockUrlBuilder()
            ])
            ->getMock();

        $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo->method('prepareFrontendFilterWidget')->willReturnCallback(
            function ($arrWidget, $arrFilterUrl, $arrJumpTo) use ($that, $fromTo, $urlParameter) {
                /** @var FromToDate $fromTo */
                $that->assertCount(2, $arrWidget['label']);
                $that->assertArrayHasKey('timetype', $arrWidget);
                $that->assertEquals($arrWidget['timetype'], $fromTo->get('timetype'));
                $that->assertArrayHasKey('dateformat', $arrWidget);
                $that->assertEquals($arrWidget['dateformat'], $fromTo->get('dateformat'));
                $that->assertEquals($arrWidget['urlvalue'], $urlParameter);

                return [
                    'widget' => $arrWidget,
                    'filterUrl' => $arrFilterUrl,
                    'jumpTo' => $arrJumpTo,
                ];
            }
        );

        /** @var FromToDate $fromTo */

        $result = $fromTo->getParameterFilterWidgets(
            [],
            ['urlParameter' => $urlParameter],
            ['Test jump to'],
            new FrontendFilterOptions()
        );

        $this->assertArrayHasKey('urlParameter', $result);
        $that->assertEquals(1, \count($result));
        $result = $result['urlParameter'];

        $this->assertEquals(
            ['urlParameter' => \explode(',', $urlParameter)],
            $result['filterUrl']
        );
    }
}
