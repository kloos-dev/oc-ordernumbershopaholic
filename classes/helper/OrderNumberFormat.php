<?php namespace Codecycler\OrdernumberShopaholic\Classes\Helper;

use Codecycler\OrdernumberShopaholic\Classes\NumberVariable;
use Codecycler\OrdernumberShopaholic\Classes\YearVariable;
use Event;
use Lovata\OrdersShopaholic\Models\Order;
use October\Rain\Support\Traits\Singleton;
use Codecycler\OrdernumberShopaholic\Models\Settings;

class OrderNumberFormat
{
    use Singleton;

    protected $obOrder;

    protected $sFormat;

    protected $obLastOrder;

    const EVENT_EXTEND_VARIABLES = 'codecycler.ordernumberformat.variables_extend';

    protected $arVariables = [
        '@y' => [
            YearVariable::class,
        ],
        '@n' => [
            NumberVariable::class,
        ],
    ];

    protected function init()
    {
        // Register all the variables
        $arVariablesExtend = Event::fire(self::EVENT_EXTEND_VARIABLES, []);

        foreach ($arVariablesExtend as $iIndex => $arExtend)
        {
            foreach ($arExtend as $sKey => $arVariable) {
                if (in_array($sKey, $this->arVariables)) {
                    continue;
                }

                // Add to variables
                $this->arVariables[$sKey] = $arVariable;
            }
        }
    }

    public function format($obOrder)
    {
        if (!Settings::get('order_number_custom', false)) {
            return $obOrder->order_number;
        }

        $this->obOrder = $obOrder;
        $this->sFormat = Settings::get('order_number_format', '@y-@n(6)');

        $this->obLastOrder = Order::orderBy('id', 'desc')->get();

        $this->iOrderNumberCount = Settings::get('order_number_count', 1);

        foreach ($this->arVariables as $sKey => $arVariable) {
            // Parse format string with each of the variables
            $bMatch = $this->findMatch($sKey);

            if (!$bMatch) {
                continue;
            }

            // Get method and value
            if (isset($this->arVariables[$sKey]['matches'][2])) {
                $sVariable = preg_replace('/([\(\)])/i', '', $this->arVariables[$sKey]['matches'][2]);

                if (is_numeric($sVariable)) {
                    $sVariable = (int) $sVariable;
                }
            } else {
                $sVariable = null;
            }

            $sValue = $arVariable[0]::getValue($this->obOrder, $sVariable);

            // Replace with value
            $this->sFormat = preg_replace('/(' . $sKey . ')(\(\d\))?/', $sValue, $this->sFormat);
        }

        // Update the order count index
        Settings::set('order_number_count', $this->iOrderNumberCount + 1);

        return $this->sFormat;
    }

    public function findMatch($sKey)
    {
        $bMatch = preg_match('/(' . $sKey . ')(\(\d\))?/i', $this->sFormat, $arMatches);

        $this->arVariables[$sKey]['matches'] = $arMatches;

        return $bMatch;
    }
}