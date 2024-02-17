<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2024 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/filter_fromto
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\FilterRule;

use Contao\System;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttribute;
use MetaModels\Attribute\ISimple;
use MetaModels\Filter\Rules\SimpleQuery;

/**
 * FromTo filter rule for date values.
 */
class FromToDate extends FromTo
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * The date time format to apply.
     *
     * @var string
     */
    private string $dateType = '';

    /**
     * Create a new instance.
     *
     * @param IAttribute      $attribute  The attribute to perform filtering on.
     * @param Connection|null $connection The database connection.
     */
    public function __construct($attribute, Connection $connection = null)
    {
        parent::__construct($attribute);

        if (null === $connection) {
            // @codingStandardsIgnoreStart
            @trigger_error(
                'Connection is missing. It has to be passed in the constructor. Fallback will be dropped.',
                E_USER_DEPRECATED
            );
            // @codingStandardsIgnoreEnd
            $connection = System::getContainer()->get('database_connection');
            assert($connection instanceof Connection);
        }
        $this->connection = $connection;
    }

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
            \sprintf(
                'SELECT t.id FROM %s AS t WHERE TIME(FROM_UNIXTIME(t.%s)) %s ?)',
                $attribute->getMetaModel()->getTableName(),
                $attribute->getColName(),
                $operation
            ),
            [$value],
            'id',
            $this->connection
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function evaluateLowerBound()
    {
        // If we are using time filtering, we have to handle it differently.
        if ($this->dateType === 'time') {
            if ($this->getLowerBound()) {
                return $this->runSimpleQuery(
                    $this->isLowerInclusive() ? '>=' : '>',
                    \date('H:i:s', $this->getLowerBound())
                );
            }

            return null;
        }

        // Set the time to 0h 0m 0s.
        if ($this->dateType === 'date') {
            if ($this->getLowerBound()) {
                $date = new \DateTime('@' . $this->getLowerBound());
                $date->setTime(0, 0, 0);
                $this->setLowerBound($date->getTimestamp(), $this->isLowerInclusive());
            }
        }

        return parent::evaluateLowerBound();
    }

    /**
     * {@inheritDoc}
     */
    protected function evaluateUpperBound()
    {
        // If we are using time filtering, we have to handle it differently.
        if ($this->dateType === 'time') {
            if ($this->getUpperBound()) {
                return $this->runSimpleQuery(
                    $this->isUpperInclusive() ? '<=' : '<',
                    \date('H:i:s', $this->getUpperBound())
                );
            }

            return null;
        }

         // Set the time to 23h 59m 59s.
        if ($this->dateType === 'date') {
            if ($this->getUpperBound()) {
                $date = new \DateTime('@' . $this->getUpperBound());
                $date->setTime(23, 59, 59);
                $this->setUpperBound($date->getTimestamp(), $this->isUpperInclusive());
            }
        }

        return parent::evaluateUpperBound();
    }
}
