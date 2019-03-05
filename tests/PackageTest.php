<?php namespace Sebastienheyd\Systempay\Tests;

use Illuminate\Support\Facades\Blade;
use View;
use Systempay;

class MyPackageFunctionTest extends TestCase
{
    static $renderSignature = 'c60bedc09fae8040d35faabb9f526244';

    public function testConfigNotFound()
    {
        $this->expectException(\UnexpectedValueException::class);
        Systempay::config('noconfig');
    }

    public function testNoValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        Systempay::set('novalue');
    }

    public function testRender()
    {
        $render = Systempay::set([
            'amount'     => 51.24,
            'trans_date' => '20170129130025',
            'trans_id'   => '123456'
        ])->render();

        $this->assertTrue(md5($render) === self::$renderSignature);
    }

    public function testSignatureSha1()
    {
        $render = Systempay::config('sha1')->set([
            'amount'     => 51.24,
            'trans_date' => '20170129130025',
            'trans_id'   => '123456'
        ])->render();

        $this->assertRegExp('#name="signature" value="59c96b34c74b9375c332b0b6a32e6deeec87de2b"#', $render);
    }

    public function testSignatureSha256()
    {
        $render = Systempay::set([
            'amount'     => 51.24,
            'trans_date' => '20170129130025',
            'trans_id'   => '123456'
        ])->render();

        $this->assertRegExp('#name="signature" value="ycA5Do5tNvsnKdc\/eP1bj2xa19z9q3iWPy9\/rpesfS0\="#', $render);
    }

    public function testBladeExtension()
    {
        $systemPay = Systempay::set([
            'amount'     => 51.24,
            'trans_date' => '20170129130025',
            'trans_id'   => '123456'
        ]);

        $view = Blade::compileString('@systempay<button type="submit">Pay</button>@endsystempay');

        ob_start();
        eval('?>'.$view);
        $render = ob_get_clean();

        $this->assertTrue(md5($render) === self::$renderSignature);
    }

    public function testBladeExtensionWithVar()
    {
        $payment = Systempay::set([
            'amount'     => 51.24,
            'trans_date' => '20170129130025',
            'trans_id'   => '123456'
        ]);

        $view = Blade::compileString('@systempay("payment")<button type="submit">Pay</button>@endsystempay');

        ob_start();
        eval('?>'.$view);
        $render = ob_get_clean();

        $this->assertTrue(md5($render) === self::$renderSignature);
    }

}