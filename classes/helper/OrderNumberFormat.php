<?php namespace Codecycler\OrdernumberShopaholic\Classes\Helper;

use Carbon\Carbon;
use Lovata\OrdersShopaholic\Models\Order;
use October\Rain\Support\Traits\Singleton;
use Codecycler\OrdernumberShopaholic\Models\Settings;

class OrderNumberFormat
{
    use Singleton;

    public $obOrder;

    public $sFormat;

    public $obLastOrder;

    public function format($obOrder)
    {
        if (!Settings::get('order_number_custom', false)) {
            return $obOrder->order_number;
        }

        $this->obOrder = $obOrder;
        $this->sFormat = Settings::get('order_number_format', '@y-@n(6)');

        $this->obLastOrder = Order::orderBy('id', 'desc')->get();

        $this->iOrderNumberCount = Settings::get('order_number_count', 1);

        $this->replaceYear();
        $this->replaceNumber();

        // Update the order count index
        Settings::set('order_number_count', $this->iOrderNumberCount + 1);

        return $this->sFormat;
    }

    public function replaceNumber()
    {
        $bMatch = preg_match('/(@n)(\(\d\))/i', $this->sFormat, $arMatches);

        if ($bMatch) {
            $iNumberInt = preg_replace('/[\(\)]+/', '', $arMatches[2]);

            // Generate new number
            $sOrderCount = (string) $this->iOrderNumberCount;
            $sNumber = str_pad($sOrderCount, $iNumberInt, "0", STR_PAD_LEFT);

            $this->sFormat = preg_replace('/@n\([\d]\)/', $sNumber, $this->sFormat);
        }
    }

    public function replaceYear()
    {
        $sThisYear = Carbon::now()->format('Y');

        $this->sFormat = str_replace('@y', $sThisYear, $this->sFormat);
    }
}