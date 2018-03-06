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
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\Test\Filter\Rules;

use MetaModels\Filter\Rules\FromTo;

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
        $baseData = array(
            1 => '10',
            2 => '20',
            3 => '30',
            4 => '40',
            5 => '50',
            6 => '60',
        );

        $ruleValues = array(
            'lowerBound'     => null,
            'lowerInclusive' => null,
            'upperBound'     => null,
            'upperInclusive' => null,
        );

        return array(
            array(
                'data'       => $baseData,
                'ruleValues' => null,
                'expected'   => null,
                'message'    => 'empty rule'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive($ruleValues, array('lowerBound' => '30')),
                'expected'   => array(4, 5, 6),
                'message'    => 'start range 30 exclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array('lowerBound' => '30', 'lowerInclusive' => true)
                ),
                'expected'   => array(3, 4, 5, 6),
                'message'    => 'start range 30 inclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive($ruleValues, array('upperBound' => '30')),
                'expected'   => array(1, 2),
                'message'    => 'end range 30 exclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array('upperBound' => '30', 'upperInclusive' => true)
                ),
                'expected'   => array(1, 2, 3),
                'message'    => 'end range 30 inclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive($ruleValues, array('upperBound' => '1')),
                'expected'   => array(),
                'message'    => 'end range 1 - should not match anything'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive($ruleValues, array('lowerBound' => '100')),
                'expected'   => array(),
                'message'    => 'start range 100 - should not match anything'
            ),
        );
    }

    /**
     * Test the functionality.
     *
     * @param array  $data       The data for the attribute.
     *
     * @param array  $ruleValues The url values.
     *
     * @param array  $expected   The expected ids.
     *
     * @param string $message    The assert message.
     *
     * @return void
     *
     * @dataProvider provider
     */
    public function testFunctionality($data, $ruleValues, $expected, $message)
    {
        $rule = new FromTo($this->mockAttribute($this->mockMetaModel(), $data));

        if (isset($ruleValues['lowerBound'])) {
            $rule->setLowerBound($ruleValues['lowerBound'], isset($ruleValues['lowerInclusive']) ?: false);
        }
        if (isset($ruleValues['upperBound'])) {
            $rule->setUpperBound($ruleValues['upperBound'], isset($ruleValues['upperInclusive']) ?: false);
        }

        $this->assertEquals($expected, $rule->getMatchingIds(), $message);
    }
}
