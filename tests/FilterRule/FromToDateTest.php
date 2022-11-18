<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2022 The MetaModels team.
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
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\Test\FilterRule;

use MetaModels\Attribute\IAttribute;
use MetaModels\FilterFromToBundle\FilterRule\FromToDate;
use MetaModels\Filter\Rules\SimpleQuery;

/**
 * Test the FromTo class.
 *
 * @covers \MetaModels\FilterFromToBundle\FilterRule\FromToDate
 */
class FromToDateTest extends FromToTestCase
{
    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function setUp(): void
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
     * Test that an exception is thrown when the attribute does not implement ISimple but we want to filter on time.
     *
     * @return void
     */
    public function testTimeRaisesExceptionForNonSimpleAttribute(): void
    {
        $attribute = $this->getMockForAbstractClass(IAttribute::class);

        $rule = new FromToDate($attribute, $this->mockConnection());
        $rule
            ->setDateType('time')
            ->setLowerBound(10, true)
            ->setUpperBound(20, true);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Filtering for time ranges is only possible on simple attributes.');

        $rule->getMatchingIds();
    }

    /**
     * Provide test data.
     *
     * @return array
     */
    public function provider()
    {
        $baseData = [
            1 => \strtotime('1985-01-01T11:00:00+00:00'),
            2 => \strtotime('1990-01-01T11:00:00+00:00'),
            3 => \strtotime('1995-01-01T11:00:00+00:00'),
            4 => \strtotime('2000-01-01T11:00:00+00:00'),
            5 => \strtotime('2010-01-01T01:00:00+00:00'),
            6 => \strtotime('2015-01-01T01:00:00+00:00'),
        ];

        $ruleValues = [
            'lowerBound'     => null,
            'lowerInclusive' => null,
            'upperBound'     => null,
            'upperInclusive' => null,
            'dateType'       => 'datim',
            'simpleQuery'    => [
                '>=' => null,
                '>'  => null,
                '<=' => null,
                '<'  => null,
            ],
        ];

        return [
            [
                'data'       => $baseData,
                'ruleValues' => null,
                'expected'   => null,
                'message'    => 'empty rule'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'lowerBound' => $baseData[3]
                    ]
                ),
                'expected'   => [4, 5, 6],
                'message'    => 'start range 30 exclusive'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'lowerBound' => $baseData[3],
                        'lowerInclusive' => true
                    ]
                ),
                'expected'   => [3, 4, 5, 6],
                'message'    => 'start range 30 inclusive'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'upperBound' => $baseData[3]
                    ]
                ),
                'expected'   => [1, 2],
                'message'    => 'end range exclusive'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'upperBound' => $baseData[3],
                        'upperInclusive' => true
                    ]
                ),
                'expected'   => [1, 2, 3],
                'message'    => 'end range inclusive'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'upperBound' => 1
                    ]
                ),
                'expected'   => [],
                'message'    => 'end range before first - should not match anything'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'lowerBound' => $baseData[6] + 4000
                    ]
                ),
                'expected'   => [],
                'message'    => 'start range beyond max - should not match anything'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'lowerBound' => 0,
                        'upperBound' => 0,
                        'dateType' => 'time',
                        'simpleQuery' => [
                        ]
                    ]
                ),
                'expected'   => null,
                'message'    => 'no range for time should match everything'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'lowerBound' => $baseData[1],
                        'dateType' => 'time',
                        'simpleQuery' => [
                            '>' => [1]
                        ]
                    ]
                ),
                'expected'   => [1],
                'message'    => 'start range for time should match'
            ],
            [
                'data'       => $baseData,
                'ruleValues' => \array_replace_recursive(
                    $ruleValues,
                    [
                        'upperBound' => $baseData[1],
                        'dateType' => 'time',
                        'simpleQuery' => [
                            '<' => [6]
                        ]
                    ]
                ),
                'expected'   => [6],
                'message'    => 'end range for time should match'
            ],
        ];
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
        $rule = $this
            ->getMockBuilder(FromToDate::class)
            ->setMethods(['executeRule'])
            ->setConstructorArgs([$this->mockAttribute($this->mockMetaModel(), $data), $this->mockConnection()])
            ->getMock();

        $that = $this;

        $rule->method('executeRule')->willReturnCallback(
            function ($executedRule) use ($that, $ruleValues, $rule) {
                /** @var FromToDate $rule */
                $simpleQuery = isset($ruleValues['simpleQuery']) ? \array_filter($ruleValues['simpleQuery']) : null;
                $that->assertTrue(
                    empty($simpleQuery) || ($executedRule instanceof SimpleQuery),
                    'Rule must be a simple Query'
                );

                if ($executedRule instanceof SimpleQuery) {
                    // Now examine the query.
                    $params = new \ReflectionProperty($executedRule, 'params');
                    $query  = new \ReflectionProperty($executedRule, 'queryString');
                    $params->setAccessible(true);
                    $query->setAccessible(true);

                    $queryString = $query->getValue($executedRule);
                    $parameters  = $params->getValue($executedRule);

                    $that->assertEquals(1, \count($parameters));

                    if ($rule->getUpperBound()) {
                        if (\strstr($queryString, '<')) {
                            $that->assertTrue(
                                (bool) ($rule->isUpperInclusive() ^ !\strstr($queryString, '>='))
                            );
                        }
                    } else {
                        $that->assertFalse(strstr($queryString, '<'), 'No upper bound defined, must not check for it.');
                    }

                    if ($rule->getLowerBound()) {
                        if (\strstr($queryString, '>')) {
                            $that->assertTrue(
                                (bool) ($rule->isLowerInclusive() ^ !\strstr($queryString, '<='))
                            );
                        }
                    } else {
                        $that->assertFalse(\strstr($queryString, '>'), 'No lower bound defined, must not check for it.');
                    }

                    foreach (['<=', '<'] as $operator) {
                        if (\strstr($queryString, $operator)) {
                            $that->assertArrayHasKey($operator, $simpleQuery, 'No value provided for operator');
                            $value = $simpleQuery[$operator];
                            $that->assertEquals(
                                \date('H:i:s', $rule->getUpperBound()),
                                $parameters[0],
                                'parameter value should be as specified.'
                            );

                            return $value;
                        }
                    }
                    foreach (['>=', '>'] as $operator) {
                        if (\strstr($queryString, $operator)) {
                            $that->assertArrayHasKey($operator, $simpleQuery, 'No value provided for operator');
                            $value = $simpleQuery[$operator];
                            $that->assertEquals(
                                \date('H:i:s', $rule->getLowerBound()),
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
        );

        return $rule;
    }

    /**
     * Test the functionality.
     *
     * @param array  $data       The data for the attribute.
     * @param array  $ruleValues The url values.
     * @param array  $expected   The expected ids.
     * @param string $message    The assert message.
     *
     * @return void
     *
     * @dataProvider provider
     */
    public function testFunctionality($data, $ruleValues, $expected, $message): void
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
