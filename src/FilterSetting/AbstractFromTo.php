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

use Contao\StringUtil;
use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\StaticIdList;
use MetaModels\Filter\Setting\Simple;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Filter "value from x to y" for FE-filtering, based on filters by the MetaModels team.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class AbstractFromTo extends Simple
{
    /**
     * Format the value for use in SQL.
     *
     * @param mixed $value The value to format.
     *
     * @return string|false
     */
    abstract protected function formatValue($value);

    /**
     * Create the rule to perform from to filtering on.
     *
     * @param IAttribute $attribute The attribute to filter on.
     *
     * @return \MetaModels\FilterFromToBundle\FilterRule\FromTo
     */
    abstract protected function buildFromToRule($attribute);

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $paramName = $this->getParamName();
        if (null === $paramName || '' === $paramName) {
            return [];
        }

        return [$paramName];
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterFilterNames()
    {
        $parameterName = $this->getParamName();
        assert(\is_string($parameterName));

        if ($this->get('label')) {
            return [$parameterName => $this->get('label')];
        }

        $attribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
        assert($attribute instanceof IAttribute);
        return [
            $parameterName => $attribute->getName()
        ];
    }

    /**
     * Retrieve the parameter value from the filter url.
     *
     * @param array $filterUrl The filter url from which to extract the parameter.
     *
     * @return array|null
     */
    protected function getParameterValue($filterUrl)
    {
        $parameterName = $this->getParamName();
        assert(\is_string($parameterName));

        if (!empty($filterUrl[$parameterName])) {
            if (\is_array($filterUrl[$parameterName])) {
                return \array_values($filterUrl[$parameterName]);
            }

            return \explode(',', $filterUrl[$parameterName]);
        }

        return null;
    }

    /**
     * Retrieve the attribute name that is referenced in this filter setting.
     *
     * @return list<string>
     */
    public function getReferencedAttributes()
    {
        if (0 === ($attrId = (int) $this->get('attr_id'))) {
            return [];
        }

        if (null === ($attribute = $this->getMetaModel()->getAttributeById($attrId))) {
            return [];
        }

        return [$attribute->getColName()];
    }

    /**
     * Retrieve the filter parameter name to react on.
     *
     * @return string|null
     */
    protected function getParamName()
    {
        if ($this->get('urlparam')) {
            return $this->get('urlparam');
        }

        $objAttribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
        if ($objAttribute) {
            return $objAttribute->getColName();
        }

        return null;
    }

    /**
     * Add param filter to global list.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function registerFilterParameter()
    {
        $GLOBALS['MM_FILTER_PARAMS'][] = $this->getParamName();
    }

    /**
     * Prepare the widget label.
     *
     * @param IAttribute $objAttribute The metamodel attribute.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    protected function prepareWidgetLabel($objAttribute)
    {
        $arrLabel = [
            ($this->get('label') ?: $objAttribute->getName()),
            'GET: ' . ($this->getParamName() ?? '')
        ];

        if ($this->get('fromfield') && $this->get('tofield')) {
            $arrLabel[0] .= ' ' . $GLOBALS['TL_LANG']['metamodels_frontendfilter']['fromto'];
        } elseif ($this->get('fromfield') && !$this->get('tofield')) {
            $arrLabel[0] .= ' ' . $GLOBALS['TL_LANG']['metamodels_frontendfilter']['from'];
        } else {
            $arrLabel[0] .= ' ' . $GLOBALS['TL_LANG']['metamodels_frontendfilter']['to'];
        }

        return $arrLabel;
    }

    /**
     * Prepare options for the widget.
     *
     * @param list<string>|null $arrIds       List of ids.
     * @param IAttribute        $objAttribute The metamodel attribute.
     *
     * @return array
     */
    protected function prepareWidgetOptions($arrIds, $objAttribute)
    {
        $arrOptions = $objAttribute->getFilterOptions(
            ($this->get('onlypossible') ? $arrIds : null),
            (bool) $this->get('onlyused')
        );

        // Remove empty values from list.
        foreach ($arrOptions as $mixKeyOption => $mixOption) {
            // Remove html/php tags.
            $mixOption = \strip_tags($mixOption);
            $mixOption = \trim($mixOption);

            if ($mixOption === '') {
                unset($arrOptions[$mixKeyOption]);
            }
        }

        return $arrOptions;
    }

    /**
     * Prepare the widget Param and filter url.
     *
     * @param array $arrFilterUrl The filter url.
     *
     * @return array
     */
    protected function prepareWidgetParamAndFilterUrl($arrFilterUrl)
    {
        // Split up our param so the widgets can use it again.
        $parameterName = $this->getParamName();
        assert(\is_string($parameterName));
        $privateFilterUrl = $arrFilterUrl;
        $parameterValue   = null;

        // If we have a value, we have to explode it by double underscore to have a valid value which the active checks
        // may cope with.
        if (\array_key_exists($parameterName, $arrFilterUrl) && !empty($arrFilterUrl[$parameterName])) {
            if (\is_array($arrFilterUrl[$parameterName])) {
                $parameterValue = $arrFilterUrl[$parameterName];
            } else {
                $parameterValue = \explode(',', $arrFilterUrl[$parameterName], 2);
            }

            if ($parameterValue && ($parameterValue[0] || $parameterValue[1])) {
                $privateFilterUrl[$parameterName] = $parameterValue;

                return [$privateFilterUrl, $parameterValue];
            } else {
                // No values given, clear the array.
                $parameterValue = null;

                return [$privateFilterUrl, $parameterValue];
            }
        }

        return [$privateFilterUrl, $parameterValue];
    }

    /**
     * Get the parameter array for configuring the widget.
     *
     * @param IAttribute        $attribute    The attribute.
     * @param array             $currentValue The current value.
     * @param list<string>|null $ids          The list of ids.
     *
     * @return array
     */
    protected function getFilterWidgetParameters(IAttribute $attribute, $currentValue, $ids)
    {
        $cssID = StringUtil::deserialize($this->get('cssID'), true);

        return [
            'label'     => $this->prepareWidgetLabel($attribute),
            'inputType' => 'multitext',
            'options'   => $this->prepareWidgetOptions($ids, $attribute),
            'eval'      => [
                'multiple'    => true,
                'size'        => ($this->get('fromfield') && $this->get('tofield') ? 2 : 1),
                'urlparam'    => $this->getParamName(),
                'template'    => $this->get('template'),
                'colname'     => $attribute->getColName(),
                'placeholder' => $this->get('placeholder'),
                'cssID'       => !empty($cssID[0]) ? ' id="' . $cssID[0] . '"' : '',
                'class'       => !empty($cssID[1]) ? ' ' . $cssID[1] : '',
            ],
            // We need to implode to have it transported correctly in the frontend filter.
            'urlvalue'  => !empty($currentValue) ? \implode(',', $currentValue) : ''
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function getParameterFilterWidgets(
        $arrIds,
        $arrFilterUrl,
        $arrJumpTo,
        FrontendFilterOptions $objFrontendFilterOptions
    ) {
        $objAttribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
        if (!$objAttribute) {
            return [];
        }

        [$privateFilterUrl, $currentValue] = $this->prepareWidgetParamAndFilterUrl($arrFilterUrl);

        $this->registerFilterParameter();

        $paramName = $this->getParamName();
        assert(\is_string($paramName));

        return [
            $paramName => $this->prepareFrontendFilterWidget(
                $this->getFilterWidgetParameters($objAttribute, $currentValue, $arrIds),
                $privateFilterUrl,
                $arrJumpTo,
                $objFrontendFilterOptions
            )
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LengthException If not both fields are allowed.
     */
    public function prepareRules(IFilter $objFilter, $arrFilterUrl)
    {
        // Check if we can filter on anything.
        if (!$this->get('fromfield') && !$this->get('tofield')) {
            $objFilter->addFilterRule(new StaticIdList(null));

            return;
        }

        // No attribute, get out.
        $attribute = $this->getMetaModel()->getAttributeById((int) $this->get('attr_id'));
        if (!($attribute)) {
            $objFilter->addFilterRule(new StaticIdList(null));

            return;
        }

        // No filter values, get out.
        $value = $this->getParameterValue($arrFilterUrl);
        if (null === $value) {
            $objFilter->addFilterRule(new StaticIdList(null));

            return;
        }

        // Two values, apply filtering for a value range if both fields are allowed.
        if (\count($value) === 2) {
            if (!($this->get('fromfield') && $this->get('tofield'))) {
                throw new \LengthException('Only one value is allowed, please configure fromfield and tofield.');
            }

            // Add rule to the filter.
            $objFilter->addFilterRule(
                $this->createFromToRule($attribute, $this->formatEmpty($value[0]), $this->formatEmpty($value[1]))
            );

            return;
        }

        // Add rule to the filter.
        $objFilter->addFilterRule($this->createFromToRule($attribute, $this->formatEmpty($value[0]), null));
    }

    /**
     * Format the value but return empty if it is empty.
     *
     * Returns false on error.
     *
     * @param string $value The value to format.
     *
     * @return false|string
     */
    private function formatEmpty(string $value): string|bool
    {
        if ('' === $value = \trim($value)) {
            return '';
        }

        if (false === $formatted = $this->formatValue($value)) {
            return false;
        }

        return $formatted;
    }

    /**
     * Create and populate a rule instance.
     *
     * @param IAttribute        $attribute          The attribute to filter on.
     * @param string|false      $formattedValueZero The formatted first value.
     * @param string|null|false $formattedValueOne  The formatted second value.
     *
     * @return \MetaModels\FilterFromToBundle\FilterRule\FromTo|StaticIdList
     */
    private function createFromToRule(
        IAttribute $attribute,
        string|bool $formattedValueZero,
        string|null|bool $formattedValueOne
    ): \MetaModels\FilterFromToBundle\FilterRule\FromTo|StaticIdList {
        // If something went wrong return an empty list.
        if ($formattedValueZero === false || $formattedValueOne === false) {
            return new StaticIdList([]);
        }

        // Add rule to the filter.
        $rule = $this->buildFromToRule($attribute);

        if (null !== $formattedValueOne) {
            $rule->setLowerBound($formattedValueZero, $this->get('moreequal'))
                ->setUpperBound($formattedValueOne, $this->get('lessequal'));
            return $rule;
        }
        if ($this->get('fromfield')) {
            $rule->setLowerBound($formattedValueZero, $this->get('moreequal'));
            return $rule;
        }
        $rule->setUpperBound($formattedValueZero, $this->get('lessequal'));

        return $rule;
    }
}
