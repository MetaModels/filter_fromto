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

namespace MetaModels\FilterFromToBundle\FilterSetting;

use Doctrine\DBAL\Connection;
use MetaModels\Filter\FilterUrlBuilder;
use MetaModels\Filter\Setting\IFilterSettingTypeFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Attribute type factory for from-to filter settings.
 */
class FromToDateFilterSettingTypeFactory implements IFilterSettingTypeFactory
{
    /**
     * The database connection.
     *
     * @var Connection
     */
    private Connection $connection;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $dispatcher;

    /**
     * The filter URL builder.
     *
     * @var FilterUrlBuilder
     */
    private FilterUrlBuilder $filterUrlBuilder;

    /**
     * List of valid attribute types that can be filtered with this filter.
     *
     * @var list<string>
     */
    private $attributeTypes;

    /**
     * {@inheritDoc}
     *
     * @param Connection               $connection       The database connection.
     * @param EventDispatcherInterface $dispatcher       The event dispatcher.
     * @param FilterUrlBuilder         $filterUrlBuilder The filter URL builder.
     */
    public function __construct(
        Connection $connection,
        EventDispatcherInterface $dispatcher,
        FilterUrlBuilder $filterUrlBuilder
    ) {
        $this->connection       = $connection;
        $this->attributeTypes   = ['timestamp'];
        $this->dispatcher       = $dispatcher;
        $this->filterUrlBuilder = $filterUrlBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return 'fromtodate';
    }

    /**
     * {@inheritDoc}
     */
    public function getTypeIcon()
    {
        return 'bundles/metamodelsfilterfromto/filter_fromto.png';
    }

    /**
     * {@inheritDoc}
     */
    public function createInstance($information, $filterSettings)
    {
        return new FromToDate(
            $filterSettings,
            $information,
            $this->connection,
            $this->dispatcher,
            $this->filterUrlBuilder
        );
    }

    /**
     * {@inheritDoc}
     */
    public function isNestedType()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getMaxChildren()
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getKnownAttributeTypes()
    {
        return $this->attributeTypes;
    }

    /**
     * {@inheritDoc}
     */
    public function addKnownAttributeType($typeName)
    {
        $this->attributeTypes[] = $typeName;

        return $this;
    }
}
