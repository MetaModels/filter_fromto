<?php

/**
 * This file is part of MetaModels/filter_fromto.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage FilterFromToBundle
 * @author     Christian de la Haye <service@delahaye.de>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <mail@netzmacht.de>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\FilterSetting;

use MetaModels\FilterFromToBundle\FilterRule\FromTo as FromToRule;

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
