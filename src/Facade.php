<?php namespace Sebastienheyd\Systempay;

/**
 * @method static \Sebastienheyd\Systempay\Systempay config(string $config = 'default')
 * @method static \Sebastienheyd\Systempay\Systempay set($param, $value = null)
 * @method static string render(string $button = '')
 *
 * @see \Sebastienheyd\Systempay\Systempay
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return Systempay::class;
    }
}
