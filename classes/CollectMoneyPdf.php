<?php

namespace Ecjia\App\Quickpay;

class CollectMoneyPdf
{
    
    public function xx()
    {
        
        $pdf = royalcms('tcpdf');
        // 设置文档信息
        $pdf->SetCreator('收款二维码');
        $pdf->SetAuthor('ECJia Team');
        $pdf->SetTitle('收款二维码');
        $pdf->SetSubject('收款二维码');
        $pdf->SetKeywords('收款, 二维码, ecjia, 到家');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // 设置默认等宽字体
        $pdf->SetDefaultMonospacedFont('courier');
        
        // 设置间距
        $pdf->SetMargins(15, 15, 15);//页面间隔
        $pdf->SetHeaderMargin(5);//页眉top间隔
        $pdf->SetFooterMargin(10);//页脚bottom间隔
        
        
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        
        //设置字体 stsongstdlight支持中文
        $pdf->SetFont('stsongstdlight', '', 14);
        
        //第一页
        $pdf->AddPage();
        
//         $pdf->writeHTML('<div style="text-align: center"><h1>第一页内容</h1></div>');
//         $pdf->writeHTML('<p>我是第一行内容</p>');
//         $pdf->writeHTML('<p style="color: red">我是第二行内容</p>');
//         $pdf->writeHTML('<p>我是第三行内容</p>');
//         $pdf->Ln(5);//换行符
//         $pdf->writeHTML('<p><img width="500" height="500" src="https://cityo2o.ecjia.com/content/uploads/merchant/60/data/shop_logo/1477948615542668810.png" /></p>');
        
        $pdf->writeHTML('<div class="left-side" style="width: 300px;height: 430px;border: 1px solid #eee;float: left;background-color: #379ED8;border-radius: 8px;text-align: center;">');
        $pdf->writeHTML('<div class="store-logo"><img src="'+ $store_logo +'" style="width: 60px;height: 60px;border-radius: 50%;margin: 20px 0 10px 0;"></div>');
        $pdf->writeHTML('<div class="store-name" style="font-size: 18px;color: #fff;margin: 10px 0;">'+ $merchants_name +'</div>');
        $pdf->writeHTML('<div class="qrcode" style="margin-top: 20px;"><img src="'+ $collectmoney_qrcode +'" style="width: 220px;height: 220px;"></div>');
        $pdf->writeHTML('<div class="info" style="color: #fff;font-size: 15px;margin-top: 20px;">微信扫描二维码进行买单</div>');
        $pdf->writeHTML('</div>');
        
        //输出PDF
        $pdf->Output('t.pdf', 'I');
        


    }
    
    
    
    
    
}