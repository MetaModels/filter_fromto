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

namespace MetaModels\Test\Filter\Setting;

use MetaModels\Filter\Setting\FromToDate;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Test the FromTo class.
 */
class FromToDateTest extends FromToTestCase
{
    /**
     * Provide test data.
     *
     * @return array
     */
    public function provider()
    {
        $baseSettings = array(
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
        );

        $baseData = array(
            1 => strtotime('1985-01-01T11:00:00+00:00'),
            2 => strtotime('1990-01-01T11:00:00+00:00'),
            3 => strtotime('1995-01-01T11:00:00+00:00'),
            4 => strtotime('2000-01-01T11:00:00+00:00'),
            5 => strtotime('2010-01-01T01:00:00+00:00'),
            6 => strtotime('2015-01-01T01:00:00+00:00'),
        );

        $generateUrlValue = function ($start, $end = null) use ($baseSettings) {
            $value = is_int($start) ? date($baseSettings['dateformat'], $start) : $start;
            if ($end) {
                $value .= '__' . (is_int($end) ? date($baseSettings['dateformat'], $end) : $end);
            }

            return $value;
        };

        return array(
            1 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1], $baseData[4])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1], $baseData[3])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1], $baseData[4])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1], $baseData[2])),
                'expected'      => array(1, 2),
                'message'       => 'filtering with start and end of range inclusive.'
            ),
            5 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[4])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[4])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[4])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1], $baseData[2])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1])),
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
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[1])),
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
                'filterValues'  => array(
                    'urlParameter' => array(
                        $generateUrlValue($baseData[1]),
                        $generateUrlValue($baseData[4])
                    )
                ),
                'expected'      => array(2, 3),
                'message'       => 'filtering exclusive with array values'
            ),
            13 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => $generateUrlValue('broken', $baseData[4])),
                'expected'      => array(1, 2, 3),
                'message'       => 'filtering exclusive with broken start but valid end.'
            ),
            14 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[4], 'broken')),
                'expected'      => array(5, 6),
                'message'       => 'filtering exclusive with valid start but broken end.'
            ),
            16 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => $generateUrlValue('', $baseData[4])),
                'expected'      => array(5, 6),
                'message'       => 'filtering exclusive with empty start but valid end.'
            ),
            17 => array(
                'filterSetting' => $baseSettings,
                'data'          => $baseData,
                'filterValues'  => array('urlParameter' => $generateUrlValue($baseData[4], '')),
                'expected'      => array(5, 6),
                'message'       => 'filtering exclusive with valid start but empty end.'
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

        $filterSetting = new FromToDate($filterSetting, $filterSettingData);

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

    /**
     * Test the generating of the widget.
     *
     * @return void
     */
    public function testGetParameterFilterWidgets()
    {
        $that          = $this;
        $filterSetting = $this->mockFilterSetting();
        $urlParameter  = date('Y-m-d-H-i-s', 473425200) . '__' . date('Y-m-d-H-i-s', 1420074000);
        $fromTo        = $this->getMock(
            'MetaModels\Filter\Setting\FromToDate',
            array('prepareFrontendFilterWidget'),
            array($filterSetting, array(
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
            ))
        );

        $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo->expects($this->any())->method('prepareFrontendFilterWidget')->will($this->returnCallback(
            function ($arrWidget, $arrFilterUrl, $arrJumpTo) use ($that, $fromTo, $urlParameter) {
                /** @var FromToDate $fromTo */
                $that->assertEquals(2, count($arrWidget['label']));
                $that->assertArrayHasKey('timetype', $arrWidget);
                $that->assertEquals($arrWidget['timetype'], $fromTo->get('timetype'));
                $that->assertArrayHasKey('dateformat', $arrWidget);
                $that->assertEquals($arrWidget['dateformat'], $fromTo->get('dateformat'));
                $that->assertEquals($arrWidget['urlvalue'], $urlParameter);

                return array(
                    'widget' => $arrWidget,
                    'filterUrl' => $arrFilterUrl,
                    'jumpTo' => $arrJumpTo,
                );
            }
        ));

        /** @var FromToDate $fromTo */

        include_once __DIR__ . '/../../../../../contao/languages/en/default.php';

        $result = $fromTo->getParameterFilterWidgets(
            array(),
            array('urlParameter' => $urlParameter),
            array('Test jump to'),
            new FrontendFilterOptions()
        );

        $this->assertArrayHasKey('urlParameter', $result);
        $that->assertEquals(1, count($result));
        $result = $result['urlParameter'];

        $this->assertEquals(
            array('urlParameter' => explode('__', $urlParameter)),
            $result['filterUrl']
        );
    }
}
