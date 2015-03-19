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
 * @copyright  The MetaModels team.
 * @license    LGPL.
 * @filesource
 */

namespace MetaModels\Filter\Rules;

use MetaModels\Attribute\IAttribute;
use MetaModels\Filter\IFilterRule;
use MetaModels\Filter\Rules\Comparing\GreaterThan;
use MetaModels\Filter\Rules\Comparing\LessThan;

/**
 * Test the FromTo class.
 */
class FromTo implements IFilterRule
{
    /**
     * The start value.
     *
     * @var mixed
     */
    private $lowerBound;

    /**
     * Flag if the start is inclusive or exclusive.
     *
     * @var bool
     */
    private $lowerInclusive;

    /**
     * The start value.
     *
     * @var mixed
     */
    private $upperBound;

    /**
     * Flag if the start is inclusive or exclusive.
     *
     * @var bool
     */
    private $upperInclusive;

    /**
     * The attribute to filter on.
     *
     * @var IAttribute
     */
    private $attribute;

    /**
     * Create a new instance.
     *
     * @param IAttribute $attribute The attribute to perform filtering on.
     */
    public function __construct($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Mark the lower bound of the range to search.
     *
     * @param mixed $value     The value to use for the lower bound.
     *
     * @param bool  $inclusive Flag if the value shall also be included in the result.
     *
     * @return FromTo
     */
    public function setLowerBound($value, $inclusive)
    {
        $this->lowerBound     = $value;
        $this->lowerInclusive = (bool) $inclusive;

        return $this;
    }

    /**
     * Retrieve the lower bounding.
     *
     * @return mixed
     */
    public function getLowerBound()
    {
        return $this->lowerBound;
    }

    /**
     * Check if the lower bounding is inclusive.
     *
     * @return boolean
     */
    public function isLowerInclusive()
    {
        return $this->lowerInclusive;
    }

    /**
     * Mark the lower bound of the range to search.
     *
     * @param mixed $value     The value to use for the lower bound.
     *
     * @param bool  $inclusive Flag if the value shall also be included in the result.
     *
     * @return FromTo
     */
    public function setUpperBound($value, $inclusive)
    {
        $this->upperBound     = $value;
        $this->upperInclusive = (bool) $inclusive;

        return $this;
    }

    /**
     * Retrieve the upper bounding.
     *
     * @return mixed
     */
    public function getUpperBound()
    {
        return $this->upperBound;
    }

    /**
     * Retrieve upper bounding is inclusive.
     *
     * @return boolean
     */
    public function isUpperInclusive()
    {
        return $this->upperInclusive;
    }

    /**
     * Retrieve the attribute.
     *
     * @return IAttribute
     */
    protected function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Execute a filter rule and return the ids.
     *
     * @param IFilterRule $rule The filter rule to execute.
     *
     * @return null|\string[]
     */
    protected function executeRule($rule)
    {
        return $rule->getMatchingIds();
    }

    /**
     * Evaluate the lower bounding of the range.
     *
     * @return null|\string[]
     */
    protected function evaluateLowerBound()
    {
        if (empty($this->lowerBound)) {
            return null;
        }

        return $this->executeRule(
            new GreaterThan($this->getAttribute(), $this->getLowerBound(), $this->isLowerInclusive())
        );
    }

    /**
     * Evaluate the lower bounding of the range.
     *
     * @return null|\string[]
     */
    protected function evaluateUpperBound()
    {
        if (empty($this->upperBound)) {
            return null;
        }

        return $this->executeRule(
            new LessThan($this->getAttribute(), $this->getUpperBound(), $this->isUpperInclusive())
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMatchingIds()
    {
        $lower = $this->evaluateLowerBound();

        // Early exit - no matches.
        if ($lower === array()) {
            return array();
        }

        $upper = $this->evaluateUpperBound();
        // Early exit - no matches.
        if ($upper === array()) {
            return array();
        }

        // If both are null - return it as all items match.
        if ($lower === null && $upper === null) {
            return null;
        }

        if (is_array($upper) && is_array($lower)) {
            return array_intersect($lower, $upper);
        }

        // Return the non null array otherwise.
        if ($lower === null) {
            return $upper;
        }

        return $lower;
    }
}
