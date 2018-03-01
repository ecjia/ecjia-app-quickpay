<?php

namespace Ecjia\App\Quickpay;

class CollectMoneyPdf
{
    /**
     * 生成收款码PDF
     *
     * @param string $merchants_name
     * @param string $store_logo    url
     * @param string $collectmoney_qrcode   url
     * @return string
     */
    public function make($merchants_name, $store_logo, $collectmoney_qrcode)
    {
        $html = $this->formatHtml($merchants_name, $store_logo, $collectmoney_qrcode);
        $this->createPDF($html);
    }
    
    public function createPDF($html)
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
        $pdf->writeHTML($html);
        
        //输出PDF
        $pdf->Output('ecjia_collect_money.pdf', 'I');
    }
    
    /**
     * 生成收款码HTML页面
     * 
     * @param string $merchants_name
     * @param string $store_logo    url
     * @param string $collectmoney_qrcode   url
     * @return string
     */
    public function formatHtml($merchants_name, $store_logo, $collectmoney_qrcode)
    {
        $html = <<<EOL
        <div class="left-side" style="width: 300px;height: 430px;border: 1px solid #eee;float: left;background-color: #379ED8;border-radius: 8px;text-align: center;">
            <div class="store-logo">
                <img src="'+ $store_logo +'" style="width: 60px;height: 60px;border-radius: 50%;margin: 20px 0 10px 0;">
            </div>
            <div class="store-name" style="font-size: 18px;color: #fff;margin: 10px 0;">'+ $merchants_name +'</div>
            <div class="qrcode" style="margin-top: 20px;">
                <img src="'+ $collectmoney_qrcode +'" style="width: 220px;height: 220px;">
            </div>
            <div class="info" style="color: #fff;font-size: 15px;margin-top: 20px;">微信扫描二维码进行买单</div>
        </div>
EOL;
        return $html;
    }
 
}
