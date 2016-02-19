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

namespace MetaModels\Filter\Rules;

use MetaModels\Attribute\ISimple;

/**
 * Test the FromTo class.
 */
class FromToDate extends FromTo
{
    /**
     * The date time format to apply.
     *
     * @var string
     */
    private $dateType;

    /**
     * Set the date format to use.
     *
     * @param string $dateType The date format, either 'date', 'time' or 'datim'.
     *
     * @return FromToDate
     */
    public function setDateType($dateType)
    {
        $this->dateType = $dateType;

        return $this;
    }

    /**
     * Run a simple query against the attribute when using time only filtering.
     *
     * @param string $operation The mathematical operation to use for evaluating.
     *
     * @param string $value     The value to match against.
     *
     * @return array|null
     *
     * @throws \RuntimeException When the attribute is not a simple one.
     */
    private function runSimpleQuery($operation, $value)
    {
        $attribute = $this->getAttribute();

        if (!$attribute instanceof ISimple) {
            throw new \RuntimeException('Filtering for time ranges is only possible on simple attributes.');
        }

        return $this->executeRule(new SimpleQuery(
            sprintf(
                'SELECT id FROM %s WHERE TIME(FROM_UNIXTIME(%s)) %s ?)',
                $attribute->getMetaModel()->getTableName(),
                $attribute->getColName(),
                $operation
            ),
            array($value)
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function evaluateLowerBound()
    {
        // If we are using time filtering, we have to handle it differently.
        if ($this->dateType == 'time') {
            if ($this->getLowerBound()) {
                return $this->runSimpleQuery(
                    $this->isLowerInclusive() ? '>=' : '>',
                    date('H:i:s', $this->getLowerBound())
                );
            }

            return null;
        }

        return parent::evaluateLowerBound();
    }

    /**
     * {@inheritDoc}
     */
    protected function evaluateUpperBound()
    {
        // If we are using time filtering, we have to handle it differently.
        if ($this->dateType == 'time') {
            if ($this->getUpperBound()) {
                return $this->runSimpleQuery(
                    $this->isUpperInclusive() ? '<=' : '<',
                    date('H:i:s', $this->getUpperBound())
                );
            }

            return null;
        }

        return parent::evaluateUpperBound();
    }
}
