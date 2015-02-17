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
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Setting;

use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilter;
use MetaModels\Filter\Rules\SimpleQuery;
use MetaModels\Filter\Rules\StaticIdList;
use MetaModels\FrontendIntegration\FrontendFilterOptions;

/**
 * Filter "value from x to y" for FE-filtering, based on filters by the meta models team.
 *
 * @package       MetaModels
 * @subpackage    FilterFromTo
 * @author        Christian de la Haye <service@delahaye.de>
 */
class FromToDate extends FromTo
{
    /**
     * Retrieve the parameter value from the filter url.
     *
     * @param array $filterUrl The filter url from which to extract the parameter.
     *
     * @return null|string|array
     */
    protected function getParameterValue($filterUrl)
    {
        $parameterName = $this->getParamName();
        if (isset($filterUrl[$parameterName]) && !empty($filterUrl[$parameterName])) {
            if (is_array($filterUrl[$parameterName])) {
                return $filterUrl[$parameterName];
            }

            return explode('__', $filterUrl[$parameterName]);
        }

        return null;
    }

    /**
     * Format the date time object accordingly to the format.
     *
     * @param string           $format The format to use.
     * @param \DateTime|string $value  The value to format.
     *
     * @return string
     */
    protected function formatValue($format, $value)
    {
        if (is_string($value)) {
            return $value;
        }

        $string = $value->format($format);

        if ($string) {
            return $string;
        }

        return $value->getTimestamp();
    }

    /**
     * Detect the SQL mask to use.
     *
     * @return string|false
     */
    protected function getMask()
    {
        // Get the right format for the field.
        switch ($this->get('timetype')) {
            case 'time':
                return 'time(FROM_UNIXTIME(%s)) %s STR_TO_DATE(?, \'%%%%H:%%%%i:%%%%s\')';

            case 'date':
                return 'date(FROM_UNIXTIME(%s)) %s STR_TO_DATE(?, \'%%%%d.%%%%m.%%%%Y\')';

            case 'datim':
                return 'FROM_UNIXTIME(%s) %s STR_TO_DATE(?,\'%%%%d.%%%%m.%%%%Y %%%%H:%%%%i:%%%%s\')';

            default:
        }

        return '(%s%s?)';
    }

    /**
     * Detect the format to use.
     *
     * @return string|false
     */
    protected function getFormat()
    {
        // Get the right format for the field.
        switch ($this->get('timetype')) {
            case 'time':
                return 'H:i;s';

            case 'date':
                return 'd.m.Y';

            case 'datim':
                return 'd.m.Y H:i;s';

            default:
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRules(IFilter $objFilter, $arrFilterUrl)
    {
        $objMetaModel  = $this->getMetaModel();
        $objAttribute  = $objMetaModel->getAttributeById($this->get('attr_id'));
        $strParamName  = $this->getParamName();
        $arrParamValue = $this->getParameterValue($arrFilterUrl);

        if (!($objAttribute && $strParamName && $arrParamValue && ($arrParamValue[0] || $arrParamValue[1]))) {
            $objFilter->addFilterRule(new StaticIdList(null));
        }

        $arrQuery  = array();
        $arrParams = array();

        // Get form the data a timestamp for the database query.
        $arrParamValue[0] = $this->stringToDateObject($arrParamValue[0]);
        $arrParamValue[1] = $this->stringToDateObject($arrParamValue[1]);

        // Build query and param array.
        list($arrQuery, $arrParams) = $this->prepareRuleParamsAndQuery(
            $arrParamValue,
            $objAttribute,
            $arrQuery,
            $arrParams
        );

        // Check if we have a query if not return here.
        if (empty($arrQuery)) {
            $objFilter->addFilterRule(new StaticIdList(null));

            return;
        }

        // Build sql.
        $strSql  = sprintf('SELECT id FROM %s WHERE ', $this->getMetaModel()->getTableName());
        $strSql .= implode(' AND ', $arrQuery);

        // Add to filter.
        $objFilter->addFilterRule(new SimpleQuery($strSql, $arrParams));
    }

    /**
     * Try to get from a string the timestamp build on the date format.
     *
     * @param string $string The string with the date.
     *
     * @return \DateTime The timestamp.
     */
    protected function stringToDateObject($string)
    {
        // Check if we have a string.
        if (empty($string)) {
            return '';
        }

        // Try to make a date from a string.
        $date = \DateTime::createFromFormat($this->get('dateformat'), $string);

        // Check if we have a data, if not return a empty string.
        if ($date == null) {
            return '';
        }

        // Make a unix timestamp from the string.
        return $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterFilterWidgets(
        $arrIds,
        $arrFilterUrl,
        $arrJumpTo,
        FrontendFilterOptions $objFrontendFilterOptions
    ) {
        $objAttribute = $this->getMetaModel()->getAttributeById($this->get('attr_id'));
        $arrOptions   = $this->prepareWidgetOptions($arrIds, $objAttribute);
        $arrLabel     = $this->prepareWidgetLabel($objAttribute);

        list($arrMyFilterUrl, $arrParamValue) = $this->prepareWidgetParamAndFilterUrl($arrFilterUrl);

        $this->addFilterParam();

        return array(
            $this->getParamName() => $this->prepareFrontendFilterWidget(
                array(
                    'label'         => $arrLabel,
                    'inputType'     => 'multitext',
                    'options'       => $arrOptions,
                    'timetype'      => $this->get('timetype'),
                    'dateformat'    => $this->get('dateformat'),
                    'eval'          => array(
                        'multiple'  => true,
                        'size'      => ($this->get('fromfield') && $this->get('tofield') ? 2 : 1),
                        'urlparam'  => $this->get('urlparam'),
                        'template'  => $this->get('template'),
                    ),
                    // We need to implode to have it transported correctly in the frontend filter.
                    'urlvalue'      => !empty($arrParamValue) ? implode('__', $arrParamValue) : ''
                ),
                $arrMyFilterUrl,
                $arrJumpTo,
                $objFrontendFilterOptions
            )
        );
    }

    /**
     * Prepare params and query for the rule.
     *
     * @param array      $arrParamValue The param value.
     * @param IAttribute $objAttribute  The metamodel attribute.
     * @param array      $arrQuery      The query array.
     * @param array      $arrParams     The params array.
     *
     * @return array
     */
    private function prepareRuleParamsAndQuery(
        $arrParamValue,
        $objAttribute,
        $arrQuery,
        $arrParams
    ) {
        $strMore   = $this->get('moreequal') ? '>=' : '>';
        $strLess   = $this->get('lessequal') ? '<=' : '<';
        $strMask   = $this->getMask();
        $strFormat = $this->getFormat();

        if ($this->get('fromfield')) {
            if ($arrParamValue[0]) {
                $arrQuery[]  = sprintf($strMask, $objAttribute->getColName(), $strMore);
                $arrParams[] = $this->formatValue($strFormat, $arrParamValue[0]);
            }

            if ($arrParamValue[1]) {
                $arrQuery[]  = sprintf($strMask, $objAttribute->getColName(), $strLess);
                $arrParams[] = $this->formatValue($strFormat, $arrParamValue[1]);
            }
        } elseif ($arrParamValue[0]) {
            $arrQuery[]  = sprintf($strMask, $objAttribute->getColName(), $strLess);
            $arrParams[] = $this->formatValue($strFormat, $arrParamValue[0]);
        }

        return array($arrQuery, $arrParams);
    }

    /**
     * Prepare options for the widget.
     *
     * @param array      $arrIds       List of ids.
     * @param IAttribute $objAttribute The metamodel attribute.
     *
     * @return array
     */
    private function prepareWidgetOptions($arrIds, $objAttribute)
    {
        $arrOptions = $objAttribute->getFilterOptions(
            ($this->get('onlypossible') ? $arrIds : null),
            (bool) $this->get('onlyused')
        );

        // Remove empty values from list.
        foreach ($arrOptions as $mixKeyOption => $mixOption) {
            // Remove html/php tags.
            $mixOption = strip_tags($mixOption);
            $mixOption = trim($mixOption);

            if ($mixOption === '' || $mixOption === null) {
                unset($arrOptions[$mixKeyOption]);
            }
        }

        return $arrOptions;
    }

    /**
     * Prepare the widget label.
     *
     * @param IAttribute $objAttribute The metamodel attribute.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function prepareWidgetLabel($objAttribute)
    {
        $arrLabel = array(
            ($this->get('label') ? $this->get('label') : $objAttribute->getName()),
            'GET: ' . $this->get('urlparam')
        );

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
     * Prepare the widget Param and filter url.
     *
     * @param array $arrFilterUrl The filter url.
     *
     * @return array
     */
    private function prepareWidgetParamAndFilterUrl($arrFilterUrl)
    {
        // Split up our param so the widgets can use it again.
        $strParamName   = $this->getParamName();
        $arrMyFilterUrl = $arrFilterUrl;
        $arrParamValue  = null;

        // If we have a value, we have to explode it by double underscore to have a valid value which the active checks
        // may cope with.
        if (array_key_exists($strParamName, $arrFilterUrl) && !empty($arrFilterUrl[$strParamName])) {
            if (is_array($arrFilterUrl[$strParamName])) {
                $arrParamValue = $arrFilterUrl[$strParamName];
            } else {
                $arrParamValue = explode('__', $arrFilterUrl[$strParamName], 2);
            }

            if ($arrParamValue && ($arrParamValue[0] || $arrParamValue[1])) {
                $arrMyFilterUrl[$strParamName] = $arrParamValue;

                return array($arrMyFilterUrl, $arrParamValue);
            } else {
                // No values given, clear the array.
                $arrParamValue = null;

                return array($arrMyFilterUrl, $arrParamValue);
            }
        }

        return array($arrMyFilterUrl, $arrParamValue);
    }

    /**
     * Add filter param to global.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function addFilterParam()
    {
        $GLOBALS['MM_FILTER_PARAMS'][] = $this->getParamName();
    }
}
