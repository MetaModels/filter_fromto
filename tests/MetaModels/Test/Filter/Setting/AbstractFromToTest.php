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
 * @subpackage FilterFromTo
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Test\Filter\Setting;

use MetaModels\Filter\Setting\AbstractFromTo;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Test the FromTo class.
 */
class AbstractFromToTest extends FromToTestCase
{
    /**
     * Mock the abstractFromTo class.
     *
     * @param ICollection $filterSetting The filter setting collection to be used.
     *
     * @param array       $data          The initialization data for the filter setting.
     *
     * @param array       $mockedMethods The methods to mock.
     *
     * @return AbstractFromTo
     */
    protected function mockAbstractFromTo($filterSetting, $data = [], $mockedMethods = [])
    {
        $data = array_replace_recursive(
            [
                'attr_id'   => 1,
                'urlparam'  => 'urlParameter',
                'label'     => 'Test filter',
                'template'  => '',
                'moreequal' => 1,
                'lessequal' => 1,
                'fromfield' => 1,
                'tofield'   => 1,
            ],
            $data
        );

        return $this->getMockForAbstractClass(
            'MetaModels\Filter\Setting\AbstractFromTo',
            [$filterSetting, $data],
            '',
            true,
            true,
            true,
            $mockedMethods
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetParameters()
    {
        $filterSetting = $this->mockFilterSetting();
        $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo = $this->mockAbstractFromTo($filterSetting);

        $this->assertEquals(
            ['urlParameter'],
            $fromTo->getParameters()
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetParametersFallback()
    {
        $filterSetting = $this->mockFilterSetting();
        $attribute     = $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo = $this->mockAbstractFromTo($filterSetting, ['urlparam' => '']);

        $this->assertEquals(
            [$attribute->getColName()],
            $fromTo->getParameters()
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetParametersNoParameterAtAll()
    {
        $filterSetting = $this->mockFilterSetting();

        $fromTo = $this->mockAbstractFromTo($filterSetting, ['urlparam' => '']);

        $this->assertEquals(
            [],
            $fromTo->getParameters()
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetParameterFilterNames()
    {
        $filterSetting = $this->mockFilterSetting();
        $this->mockAttribute($filterSetting->getMetaModel(), ['id' => 1, 'colname' => 'attributeColumn']);

        $fromTo = $this->mockAbstractFromTo($filterSetting);

        $this->assertEquals(
            ['urlParameter'],
            $fromTo->getParameters()
        );

        $this->assertEquals(
            ['urlParameter' => 'Test filter'],
            $fromTo->getParameterFilterNames()
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetParameterFilterNamesFallback()
    {
        $filterSetting = $this->mockFilterSetting();
        $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo = $this->mockAbstractFromTo($filterSetting, ['label' => '']);

        $this->assertEquals(
            ['urlParameter'],
            $fromTo->getParameters()
        );

        $this->assertEquals(
            ['urlParameter' => 'Test Attribute'],
            $fromTo->getParameterFilterNames()
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetReferencedAttributes()
    {
        $filterSetting = $this->mockFilterSetting();
        $attribute     = $this->mockAttribute($filterSetting->getMetaModel());
        $fromTo        = $this->mockAbstractFromTo($filterSetting);

        $this->assertEquals(
            [$attribute->getColName()],
            $fromTo->getReferencedAttributes()
        );
    }

    /**
     * Test the getParameterFilterNames method.
     *
     * @return void
     */
    public function testGetReferencedAttributesFallback()
    {
        $filterSetting = $this->mockFilterSetting();
        $attribute     = $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo = $this->mockAbstractFromTo($filterSetting, ['attr_id' => ($attribute->get('id') + 1)]);

        $this->assertEquals(
            [],
            $fromTo->getReferencedAttributes()
        );
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
        $fromTo        = $this->mockAbstractFromTo($filterSetting, [], ['prepareFrontendFilterWidget']);
        $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo->expects($this->any())->method('prepareFrontendFilterWidget')->will($this->returnCallback(
            function ($arrWidget, $arrFilterUrl, $arrJumpTo) use ($that, $fromTo) {
                $that->assertEquals(2, count($arrWidget['label']));
                $that->assertArrayHasKey('options', $arrWidget);
                $that->assertArrayHasKey('inputType', $arrWidget);
                $that->assertArrayHasKey('eval', $arrWidget);
                $that->assertArrayHasKey('urlparam', $arrWidget['eval']);
                $that->assertEquals($arrWidget['urlvalue'], '01__20');

                return [
                    'widget' => $arrWidget,
                    'filterUrl' => $arrFilterUrl,
                    'jumpTo' => $arrJumpTo,
                ];
            }
        ));

        include_once __DIR__ . '/../../../../../contao/languages/en/default.php';

        $result = $fromTo->getParameterFilterWidgets(
            [],
            ['urlParameter' => '01__20'],
            ['Test jump to'],
            new FrontendFilterOptions()
        );

        $this->assertArrayHasKey('urlParameter', $result);
        $that->assertEquals(1, count($result));
        $result = $result['urlParameter'];

        $this->assertEquals(
            ['Test jump to'],
            $result['jumpTo']
        );
        $this->assertEquals(
            ['urlParameter' => ['01', '20']],
            $result['filterUrl']
        );
    }

    /**
     * Test that the method will return an empty array for an invalid attribute.
     *
     * @return void
     */
    public function testGetParameterFilterWidgetsInvalidAttribute()
    {
        $filterSetting = $this->mockFilterSetting();
        $attribute     = $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo = $this->mockAbstractFromTo($filterSetting, ['attr_id' => ($attribute->get('id') + 1)]);

        include_once __DIR__ . '/../../../../../contao/languages/en/default.php';

        $this->assertEquals(
            [],
            $fromTo->getParameterFilterWidgets([], [], [], new FrontendFilterOptions())
        );
    }
}
