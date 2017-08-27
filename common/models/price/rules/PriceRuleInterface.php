<?php
namespace common\models\price\rules;

/**
 *
 */
interface PriceRuleInterface
{
    public function getFinalPrice();
    public function getDescription();
    public function checkCanUse();
    public static function validate($data);
}
