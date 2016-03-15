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
 * @author     David Molineus <mail@netzmacht.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Filter\Setting;

use Contao\Date;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\Rules\FromToDate as FromToRule;

/**
 * Filter "value from x to y" for FE-filtering, regarding date and time representations.
 */
class FromToDate extends AbstractFromTo
{
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

        // Check if we have a date, if not return a empty string.
        if ($date === false) {
            return '';
        }

        // Make a unix timestamp from the string.
        return $date->getTimestamp();
    }

    /**
     * {@inheritDoc}
     */
    protected function buildFromToRule($attribute)
    {
        $rule = new FromToRule($attribute);
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
        if ($format = trim($this->get('dateformat'))) {
            return $format;
        }

        return Date::getFormatFromRgxp($this->get('timetype'));
    }
}
