<?php

namespace Ecjia\App\Quickpay;

use Royalcms\Component\App\AppServiceProvider;

class QuickpayServiceProvider extends  AppServiceProvider
{
    
    public function boot()
    {
        $this->package('ecjia/app-quickpay');
    }
    
    public function register()
    {
        
    }
    
    
    
}