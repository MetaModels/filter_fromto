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
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <mail@netzmacht.de>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Setting;

use MetaModels\Filter\Rules\FromTo as FromToRule;

/**
 * Filter "value from x to y" for FE-filtering, based on filters by the meta models team.
 */
class FromTo extends AbstractFromTo
{
    /**
     * {@inheritDoc}
     */
    protected function formatValue($value)
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildFromToRule($attribute)
    {
        return new FromToRule($attribute);
    }
}
