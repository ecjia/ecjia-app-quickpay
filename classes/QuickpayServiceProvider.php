<?php

namespace Ecjia\App\Quickpay;

use Royalcms\Component\App\AppParentServiceProvider;

class QuickpayServiceProvider extends  AppParentServiceProvider
{
    
    public function boot()
    {
        $this->package('ecjia/app-quickpay', null, dirname(__DIR__));
    }
    
    public function register()
    {
        
    }
    
    
    
}