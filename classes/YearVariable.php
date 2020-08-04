<?php namespace Codecycler\OrdernumberShopaholic\Classes;

use Carbon\Carbon;

class YearVariable
{
    public static function getValue($obOrder, $sVariable)
    {
        $sThisYear = Carbon::now()->format('Y');

        return $sThisYear;
    }
}