<?php namespace Codecycler\OrdernumberShopaholic;

use Codecycler\OrdernumberShopaholic\Classes\Helper\OrderNumberFormat;
use Lovata\OrdersShopaholic\Models\Order;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = [
        'Lovata.OrdersShopaholic',
    ];

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Additional settings',
                'description' => 'Manage additional settings',
                'category'    => 'Webshop',
                'icon'        => 'icon-star',
                'class'       => 'Codecycler\OrdernumberShopaholic\Models\Settings',
                'order'       => 500,
                'keywords'    => 'order number format orders',
                'permissions' => []
            ]
        ];
    }

    public function boot()
    {
        Order::extend(function ($obModel) {
            $obModel->bindEvent('model.beforeSetAttribute', function ($sKey, $sValue) use ($obModel) {
                if ($sKey == 'order_number') {
                    return OrderNumberFormat::instance()->format($obModel);
                }
            });
        });
    }
}
