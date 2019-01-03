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
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */


namespace MetaModels\Filter\Helper;

use Contao\Date;
use Contao\Widget;

/**
 * This class is a helper class to provide special date regexp.
 */
class MetaModelsFilterRangeDateRgXp
{
    /**
     * Process a custom date regexp on a widget.
     *
     * @param string $rgxp   The rgxp being evaluated.
     *
     * @param string $value  The value to check.
     *
     * @param Widget $widget The widget to process.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public static function processCustomDateRegexp($rgxp, $value, $widget)
    {
        if ('MetaModelsFilterRangeDateRgXp' !== $rgxp) {
            return;
        }
        $format = $widget->dateformat;

        if (!\preg_match('~^'. Date::getRegexp($format) .'$~i', $value)) {
            $widget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['date'], Date::getInputFormat($format)));
        } else {
            // Validate the date (see https://github.com/contao/core/issues/5086)
            try {
                new Date($value, $format);
            } catch (\OutOfBoundsException $e) {
                $widget->addError(\sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $value));
            }
        }
    }
}
