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
    protected function mockAbstractFromTo($filterSetting, $data = array(), $mockedMethods = array())
    {
        $data = array_replace_recursive(
            array(
                'attr_id'   => 1,
                'urlparam'  => 'urlParameter',
                'label'     => 'Test filter',
                'template'  => '',
                'moreequal' => 1,
                'lessequal' => 1,
                'fromfield' => 1,
                'tofield'   => 1,
            ),
            $data
        );

        return $this->getMockForAbstractClass(
            'MetaModels\Filter\Setting\AbstractFromTo',
            array($filterSetting, $data),
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
            array('urlParameter'),
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

        $fromTo = $this->mockAbstractFromTo($filterSetting, array('urlparam'  => ''));

        $this->assertEquals(
            array($attribute->getColName()),
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

        $fromTo = $this->mockAbstractFromTo($filterSetting, array('urlparam'  => ''));

        $this->assertEquals(
            array(),
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
        $this->mockAttribute($filterSetting->getMetaModel(), array('id' => 1, 'colname' => 'attributeColumn'));

        $fromTo = $this->mockAbstractFromTo($filterSetting);

        $this->assertEquals(
            array('urlParameter'),
            $fromTo->getParameters()
        );

        $this->assertEquals(
            array('urlParameter' => 'Test filter'),
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

        $fromTo = $this->mockAbstractFromTo($filterSetting, array('label'     => ''));

        $this->assertEquals(
            array('urlParameter'),
            $fromTo->getParameters()
        );

        $this->assertEquals(
            array('urlParameter' => 'Test Attribute'),
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
            array($attribute->getColName()),
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

        $fromTo = $this->mockAbstractFromTo($filterSetting, array('attr_id'   => ($attribute->get('id') + 1)));

        $this->assertEquals(
            array(),
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
        $fromTo        = $this->mockAbstractFromTo($filterSetting, array(), array('prepareFrontendFilterWidget'));
        $this->mockAttribute($filterSetting->getMetaModel());

        $fromTo->expects($this->any())->method('prepareFrontendFilterWidget')->will($this->returnCallback(
            function ($arrWidget, $arrFilterUrl, $arrJumpTo) use ($that, $fromTo) {
                $that->assertEquals(2, count($arrWidget['label']));
                $that->assertArrayHasKey('options', $arrWidget);
                $that->assertArrayHasKey('inputType', $arrWidget);
                $that->assertArrayHasKey('eval', $arrWidget);
                $that->assertArrayHasKey('urlparam', $arrWidget['eval']);
                $that->assertEquals($arrWidget['urlvalue'], '01__20');

                return array(
                    'widget' => $arrWidget,
                    'filterUrl' => $arrFilterUrl,
                    'jumpTo' => $arrJumpTo,
                );
            }
        ));

        include_once __DIR__ . '/../../../../../contao/languages/en/default.php';

        $result = $fromTo->getParameterFilterWidgets(
            array(),
            array('urlParameter' => '01__20'),
            array('Test jump to'),
            new FrontendFilterOptions()
        );

        $this->assertArrayHasKey('urlParameter', $result);
        $that->assertEquals(1, count($result));
        $result = $result['urlParameter'];

        $this->assertEquals(
            array('Test jump to'),
            $result['jumpTo']
        );
        $this->assertEquals(
            array('urlParameter' => array('01', '20')),
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

        $fromTo = $this->mockAbstractFromTo($filterSetting, array('attr_id'   => ($attribute->get('id') + 1)));

        include_once __DIR__ . '/../../../../../contao/languages/en/default.php';

        $this->assertEquals(
            array(),
            $fromTo->getParameterFilterWidgets(array(), array(), array(), new FrontendFilterOptions())
        );

    }
}
