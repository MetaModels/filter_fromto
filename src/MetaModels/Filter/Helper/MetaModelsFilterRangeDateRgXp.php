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

        if (!preg_match('~^'. Date::getRegexp($format) .'$~i', $value)) {
            $widget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['date'], Date::getInputFormat($format)));
        } else {
            // Validate the date (see https://github.com/contao/core/issues/5086)
            try {
                new Date($value, $format);
            } catch (\OutOfBoundsException $e) {
                $widget->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $value));
            }
        }
    }
}
