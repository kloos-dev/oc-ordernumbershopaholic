# Variables
You can use some cool variables to customise the order number format.

Description | Variable
---- | ----
Current year, 4 characters | @y
Incremental number, 4 characters | @n(4)

## Extend
You can extend the formatter with your own variables. Simple register your variable and use it in the formatting string.

You can use the event `codecycler.ordernumberformat.variables_extend`.

``` php
// Listen to event in Plugin.php boot method
Event::listen('codecycler.ordernumberformat.variables_extend', function () {
    return [
        '@customvariable' => [
            \Hendricks\Piedpiper\Classes\OrdernumberVariable::class,
        ],
    ];
});

// File located at $/hendricks/piedpiper/classes/OrdernumberVariable.php
namespace Hendricks\Piedpiper\Classes;

class OrdernumberVariable
{
    public static function getValue($obOrder, $sVariable)
    {
        return 'hello';
    }
}
```

You can use a variable. See table with default variables (`@n(4)`) at the top of this document for understanding how the variable works.