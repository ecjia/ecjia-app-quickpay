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
        $html = $this->formatTableHtml($merchants_name, $store_logo, $collectmoney_qrcode);
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
        $pdf->writeHTML($html, true, false, true, false, '');
        
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
    public function formatTableHtml($merchants_name, $store_logo, $collectmoney_qrcode)
    {
        $collectHtml = $this->formatHtml($merchants_name, $store_logo, $collectmoney_qrcode);
        
        $tablehtml = <<<EOL
        <table>
            <tr>
                <td>
                    {$collectHtml}
                </td>
                <td>
                    {$collectHtml}
                </td>
            </tr>
            <tr>
                <td>
                    {$collectHtml}
                </td>
                <td>
                    {$collectHtml}
                </td>
            </tr>
        </table>
EOL;
        return $tablehtml;
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
        <div style="border: 2px solid #eee; float: left; background-color: #379ED8; text-align: center;">
            <div><img src="{$store_logo}" style="width: 200px;height: 200px;"></div>
            <div style="font-size: 18pt;color: #fff; width: 500px;">{$merchants_name}</div>
            <div style="margin-top: 20px;"><img src="{$collectmoney_qrcode}" style="width: 530px;height: 530px;"></div>
            <div style="color: #fff;font-size: 12pt; margin-top: 20px;">微信扫描二维码进行买单</div>
        </div>
EOL;
        return $html;
    }
 
}
