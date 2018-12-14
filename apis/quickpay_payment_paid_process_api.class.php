<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/14
 * Time: 15:46
 */

class quickpay_payment_paid_process_api
{

    public function call(& $option)
    {
        $record_model = array_get($option, 'record_model');

        return new \Ecjia\App\Quickpay\PaidOrder\PaidOrderProcess($record_model);
    }

}