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
 * @author     Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/filter_fromto/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\FilterFromToBundle\FilterSetting;

use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\AbstractFilterSettingTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Attribute type factory for from-to filter settings.
 */
class FromToFilterSettingTypeFactory extends AbstractFilterSettingTypeFactory
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * The filter URL builder.
     *
     * @var FilterUrlBuilder
     */
    private $filterUrlBuilder;

    /**
     * {@inheritDoc}
     *
     * @param EventDispatcherInterface $dispatcher       The event dispatcher.
     * @param FilterUrlBuilder         $filterUrlBuilder The filter URL builder.
     */
    public function __construct(EventDispatcherInterface $dispatcher, FilterUrlBuilder $filterUrlBuilder)
    {
        parent::__construct();

        $this
            ->setTypeName('fromto')
            ->setTypeIcon('bundles/metamodelsfilterfromto/filter_fromto.png')
            ->setTypeClass(FromTo::class)
            ->allowAttributeTypes('numeric', 'decimal');

        $this->dispatcher       = $dispatcher;
        $this->filterUrlBuilder = $filterUrlBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $filterSettings)
    {
        return new FromTo(
            $filterSettings,
            $information,
            $this->dispatcher,
            $this->filterUrlBuilder
        );
    }
}
