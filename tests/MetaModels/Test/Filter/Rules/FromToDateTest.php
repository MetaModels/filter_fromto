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

use MetaModels\Filter\Rules\FromToDate;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Test\Contao\Database;

/**
 * Test the FromTo class.
 */
class FromToDateTest extends FromToTestCase
{
    /**
     * Test that an exception is thrown when the attribute does not implement ISimple but we want to filter on time.
     *
     * @return void
     */
    public function testTimeRaisesExceptionForNonSimpleAttribute()
    {
        $attribute = $this->getMockForAbstractClass(
            '\MetaModels\Attribute\IAttribute',
            array(
                $this->mockMetaModel(),
                array()
            )
        );

        $rule = new FromToDate($attribute);
        $rule
            ->setDateType('time')
            ->setLowerBound(10, true)
            ->setUpperBound(20, true);

        $this->setExpectedException('RuntimeException');
        $rule->getMatchingIds();
    }

    /**
     * Provide test data.
     *
     * @return array
     */
    public function provider()
    {
        $baseData = array(
            1 => strtotime('1985-01-01T11:00:00+00:00'),
            2 => strtotime('1990-01-01T11:00:00+00:00'),
            3 => strtotime('1995-01-01T11:00:00+00:00'),
            4 => strtotime('2000-01-01T11:00:00+00:00'),
            5 => strtotime('2010-01-01T01:00:00+00:00'),
            6 => strtotime('2015-01-01T01:00:00+00:00'),
        );

        $ruleValues = array(
            'lowerBound'     => null,
            'lowerInclusive' => null,
            'upperBound'     => null,
            'upperInclusive' => null,
            'dateType'       => 'datim',
            'simpleQuery'    => array(
                '>=' => null,
                '>'  => null,
                '<=' => null,
                '<'  => null,
            ),
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
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'lowerBound' => $baseData[3]
                    )
                ),
                'expected'   => array(4, 5, 6),
                'message'    => 'start range 30 exclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'lowerBound' => $baseData[3],
                        'lowerInclusive' => true
                    )
                ),
                'expected'   => array(3, 4, 5, 6),
                'message'    => 'start range 30 inclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'upperBound' => $baseData[3]
                    )
                ),
                'expected'   => array(1, 2),
                'message'    => 'end range exclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'upperBound' => $baseData[3],
                        'upperInclusive' => true
                    )
                ),
                'expected'   => array(1, 2, 3),
                'message'    => 'end range inclusive'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'upperBound' => 1
                    )
                ),
                'expected'   => array(),
                'message'    => 'end range before first - should not match anything'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'lowerBound' => $baseData[6] + 4000
                    )
                ),
                'expected'   => array(),
                'message'    => 'start range beyond max - should not match anything'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'lowerBound' => 0,
                        'upperBound' => 0,
                        'dateType' => 'time',
                        'simpleQuery' => array(
                        )
                    )
                ),
                'expected'   => null,
                'message'    => 'no range for time should match everything'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'lowerBound' => $baseData[1],
                        'dateType' => 'time',
                        'simpleQuery' => array(
                            '>' => array(1)
                        )
                    )
                ),
                'expected'   => array(1),
                'message'    => 'start range for time should match'
            ),
            array(
                'data'       => $baseData,
                'ruleValues' => array_replace_recursive(
                    $ruleValues,
                    array(
                        'upperBound' => $baseData[1],
                        'dateType' => 'time',
                        'simpleQuery' => array(
                            '<' => array(6)
                        )
                    )
                ),
                'expected'   => array(6),
                'message'    => 'end range for time should match'
            ),
        );
    }

    /**
     * Mock the from to date rule to get hold of the simple queries.
     *
     * @param array $data       The data for the attribute.
     *
     * @param array $ruleValues The url values.
     *
     * @return FromToDate
     */
    protected function getMockedFromToDateRule($data, $ruleValues)
    {
        $rule = $this->getMock(
            'MetaModels\Filter\Rules\FromToDate',
            array('executeRule'),
            array($this->mockAttribute($this->mockMetaModel(), $data))
        );

        $that = $this;

        $rule->expects($this->any())->method('executeRule')->will($this->returnCallback(
            function ($executedRule) use ($that, $ruleValues, $rule) {
                /** @var FromToDate $rule */
                $simpleQuery = isset($ruleValues['simpleQuery']) ? array_filter($ruleValues['simpleQuery']) : null;
                $that->assertTrue(
                    empty($simpleQuery) || ($executedRule instanceof SimpleQuery),
                    'Rule must be a simple Query'
                );

                if ($executedRule instanceof SimpleQuery) {
                    // Now examine the query.
                    $params = new \ReflectionProperty($executedRule, 'arrParams');
                    $query  = new \ReflectionProperty($executedRule, 'strQueryString');
                    $params->setAccessible(true);
                    $query->setAccessible(true);

                    $queryString = $query->getValue($executedRule);
                    $parameters  = $params->getValue($executedRule);

                    $that->assertEquals(1, count($parameters));

                    if ($rule->getUpperBound()) {
                        if (strstr($queryString, '<')) {
                            $that->assertTrue(
                                (bool) ($rule->isUpperInclusive() ^ !strstr($queryString, '>='))
                            );
                        }
                    } else {
                        $that->assertFalse(strstr($queryString, '<'), 'No upper bound defined, must not check for it.');
                    }

                    if ($rule->getLowerBound()) {
                        if (strstr($queryString, '>')) {
                            $that->assertTrue(
                                (bool) ($rule->isLowerInclusive() ^ !strstr($queryString, '<='))
                            );
                        }
                    } else {
                        $that->assertFalse(strstr($queryString, '>'), 'No lower bound defined, must not check for it.');
                    }

                    foreach (array('<=', '<') as $operator) {
                        if (strstr($queryString, $operator)) {
                            $that->assertArrayHasKey($operator, $simpleQuery, 'No value provided for operator');
                            $value = $simpleQuery[$operator];
                            $that->assertEquals(
                                date('H:i:s', $rule->getUpperBound()),
                                $parameters[0],
                                'parameter value should be as specified.'
                            );

                            return $value;
                        }
                    }
                    foreach (array('>=', '>') as $operator) {
                        if (strstr($queryString, $operator)) {
                            $that->assertArrayHasKey($operator, $simpleQuery, 'No value provided for operator');
                            $value = $simpleQuery[$operator];
                            $that->assertEquals(
                                date('H:i:s', $rule->getLowerBound()),
                                $parameters[0],
                                'parameter value should be as specified.'
                            );

                            return $value;
                        }
                    }

                    return null;
                }

                return $executedRule->getMatchingIds();
            }
        ));

        return $rule;
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
        $rule = $this->getMockedFromToDateRule($data, $ruleValues);

        if (isset($ruleValues['lowerBound'])) {
            $rule->setLowerBound(
                $ruleValues['lowerBound'],
                isset($ruleValues['lowerInclusive'])
            );
        }
        if (isset($ruleValues['upperBound'])) {
            $rule->setUpperBound(
                $ruleValues['upperBound'],
                isset($ruleValues['upperInclusive'])
            );
        }
        if (isset($ruleValues['dateType'])) {
            $rule->setDateType($ruleValues['dateType']);
        }

        $this->assertEquals($expected, $rule->getMatchingIds(), $message);
    }
}
