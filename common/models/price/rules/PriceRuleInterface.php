<?php
namespace common\models\price\rules;

/**
 *
 */
interface PriceRuleInterface
{
    public function getNewPrice();
    public function getDescription();
    public function checkCanUse();
    public function validate();
}
