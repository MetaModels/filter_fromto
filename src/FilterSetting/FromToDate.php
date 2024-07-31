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
 * @author     David Molineus <mail@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\FilterSetting;

use Contao\Date;
use Contao\System;
use Doctrine\DBAL\Connection;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\ICollection;
use MetaModels\FilterFromToBundle\FilterRule\FromToDate as FromToRule;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Filter "value from x to y" for FE-filtering, regarding date and time representations.
 */
class FromToDate extends AbstractFromTo
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * Create a new instance.
     *
     * @param ICollection                   $collection       The parenting filter settings object.
     * @param array                         $data             The attributes for this filter setting.
     * @param Connection|null               $connection       The database connection.
     * @param EventDispatcherInterface|null $eventDispatcher  The event dispatcher.
     * @param FilterUrlBuilder|null         $filterUrlBuilder The filter URL builder.
     * @param TranslatorInterface|null      $translator       The translator.
     */
    public function __construct(
        ICollection $collection,
        array $data,
        Connection $connection = null,
        EventDispatcherInterface $eventDispatcher = null,
        FilterUrlBuilder $filterUrlBuilder = null,
        TranslatorInterface $translator = null
    ) {
        parent::__construct($collection, $data, $eventDispatcher, $filterUrlBuilder, $translator);

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
     * {@inheritDoc}
     */
    protected function getFilterWidgetParameters(IAttribute $attribute, $currentValue, $ids)
    {
        $parameters               = parent::getFilterWidgetParameters($attribute, $currentValue, $ids);
        $parameters['timetype']   = $this->get('timetype');
        $parameters['dateformat'] = $this->determineDateFormat();
        // Add eval values that shall get passed to the widget instance.
        $parameters['eval']['rgxp']       = 'MetaModelsFilterRangeDateRgXp';
        $parameters['eval']['dateformat'] = $this->determineDateFormat();

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    protected function formatValue($value)
    {
        // Try to make a date from a string.
        $date = \DateTime::createFromFormat($this->determineDateFormat(), $value);

        // Check if we have a date, if not return false to indicate error.
        if ($date === false) {
            return false;
        }

        // Make a unix timestamp from the string.
        return (string) $date->getTimestamp();
    }

    /**
     * {@inheritDoc}
     */
    protected function buildFromToRule($attribute)
    {
        $rule = new FromToRule($attribute, $this->connection);
        $rule->setDateType($this->get('timetype'));

        return $rule;
    }

    /**
     * Obtain the correct date/time string.
     *
     * @return string
     */
    private function determineDateFormat()
    {
        if ($format = \trim($this->get('dateformat'))) {
            return $format;
        }

        return Date::getFormatFromRgxp($this->get('timetype'));
    }
}
