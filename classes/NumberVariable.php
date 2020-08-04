<?php namespace Codecycler\OrdernumberShopaholic\Classes;

use Carbon\Carbon;
use Codecycler\OrdernumberShopaholic\Models\Settings;

class NumberVariable
{
    public static function getValue($obOrder, $sVariable)
    {
        $iNumberInt = $sVariable;

        // Generate new number
        $sOrderCount = (string) Settings::get('order_number_count', 1);
        $sNumber = str_pad($sOrderCount, $iNumberInt, "0", STR_PAD_LEFT);

        return $sNumber;
    }
}