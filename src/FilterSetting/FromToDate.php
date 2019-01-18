<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2019 The MetaModels team.
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
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\FilterSetting;

use Contao\Date;
use MetaModels\Attribute\IAttribute;
use MetaModels\FilterFromToBundle\FilterRule\FromToDate as FromToRule;

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
            return false;
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
        if ($format = \trim($this->get('dateformat'))) {
            return $format;
        }

        return Date::getFormatFromRgxp($this->get('timetype'));
    }
}
