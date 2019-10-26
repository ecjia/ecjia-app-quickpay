<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 收款二维码
 */
class mh_qrcode extends ecjia_merchant {
    public function __construct() {
        parent::__construct();
        RC_Script::enqueue_script('jquery-form');
        RC_Script::enqueue_script('smoke');
        RC_Style::enqueue_style('uniform-aristo');

        // 页面css样式
        RC_Style::enqueue_style('merchant_qrcode', RC_App::apps_url('statics/css/merchant_qrcode.css', __FILE__), array());
        RC_Script::enqueue_script('merchant_qrcode', RC_App::apps_url('statics/js/merchant_qrcode.js', __FILE__));

        RC_Loader::load_app_func('quickpay');
        Ecjia\App\Quickpay\Helper::assign_adminlog_content();

        ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('我的店铺', 'quickpay'), RC_Uri::url('merchant/merchant/init')));
        ecjia_merchant_screen::get_current_screen()->set_parentage('store', 'store/merchant.php');
    }

    /**
     * 收款二维码
     */
    public function init() {
        $this->admin_priv('quickpay_collectmoney_qrcode');

        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('店铺二维码', 'quickpay')));
        $this->assign('app_url', RC_App::apps_url('statics', __FILE__));

        $this->assign('ur_here', __('店铺二维码', 'quickpay'));

        $merchant_info = RC_Api::api('store', 'store_info', ['store_id' => $_SESSION['store_id']]);
        $merchant_info['merchants_name'] = $_SESSION['store_name'];

        $this->assign('refresh_url', RC_Uri::url('quickpay/mh_qrcode/refresh'));
        $this->assign('download_url', RC_Uri::url('quickpay/mh_qrcode/download'));
        $this->assign('print_url', RC_Uri::url('quickpay/mh_qrcode/print_qrcode'));

        //收款二维码
        $merchant_info['shop_logo'] = RC_Upload::upload_url($merchant_info['shop_logo']);
        $merchant_info['collectmoney_qrcode'] = with(new Ecjia\App\Mobile\Qrcode\GenerateCollectMoney($_SESSION['store_id'],  $merchant_info['shop_logo']))->getQrcodeUrl();

        //推广二维码
        $merchant_info['member_qrcode'] = with(new Ecjia\App\Mobile\Qrcode\GenerateAffiliate($_SESSION['store_id'],  $merchant_info['shop_logo']))->getQrcodeUrl();

        $this->assign('merchant_info', $merchant_info);

        return $this->display('quickpay_qrcode.dwt');
    }

    /**
     * 刷新二维码
     */
    public function refresh() {
        $this->admin_priv('quickpay_collectmoney_qrcode', ecjia::MSGTYPE_JSON);

        $store_id = $_SESSION['store_id'];
        //删除生成的收款二维码
        $type = $_GET['type'];
        if($type == 'affiliate') {
            //删除生成的收款二维码
            with(new Ecjia\App\Mobile\Qrcode\GenerateAffiliate($store_id))->removeQrcode();
        } else {
            //删除生成的收款二维码
            with(new Ecjia\App\Mobile\Qrcode\GenerateCollectMoney($store_id))->removeQrcode();
        }
        ecjia_merchant::admin_log(__('刷新收款二维码', 'quickpay'), 'edit', 'collectmoney_qrcode');

        return $this->showmessage(__('刷新成功', 'quickpay'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/mh_qrcode/init')));
    }

    /**
     * 下载素材
     */
    public function download() {
        $this->admin_priv('quickpay_collectmoney_qrcode', ecjia::MSGTYPE_HTML);

        $merchant_info = RC_Api::api('store', 'store_info', ['store_id' => $_SESSION['store_id']]);
        $merchant_name = $_SESSION['store_name'];
        $merchant_info['shop_logo'] = RC_Upload::upload_url($merchant_info['shop_logo']);

        $type = $_GET['type'];
        if($type == 'affiliate') {
            $merchant_info['affiliate_qrcode'] = with(new Ecjia\App\Mobile\Qrcode\GenerateAffiliate($_SESSION['store_id'],  $merchant_info['shop_logo']))->getQrcodeUrl();

            with(new Ecjia\App\Quickpay\AffiliatePdf($merchant_name, $merchant_info['shop_logo'], $merchant_info['affiliate_qrcode']))->make('D');
        } else {
            $merchant_info['collectmoney_qrcode'] = with(new Ecjia\App\Mobile\Qrcode\GenerateCollectMoney($_SESSION['store_id'],  $merchant_info['shop_logo']))->getQrcodeUrl();

            with(new Ecjia\App\Quickpay\CollectMoneyPdf($merchant_name, $merchant_info['shop_logo'], $merchant_info['collectmoney_qrcode']))->make('D');
        }
    }

    /**
     * 打印素材
     */
    public function print_qrcode() {
        $this->admin_priv('quickpay_collectmoney_qrcode', ecjia::MSGTYPE_JSON);

        $store_id = $_SESSION['store_id'];
        $type = $_GET['type'];
        if($type == 'affiliate') {
            //删除生成的收款二维码
            with(new Ecjia\App\Mobile\Qrcode\GenerateAffiliate($store_id))->removeQrcode();
        } else {
            //删除生成的收款二维码
            with(new Ecjia\App\Mobile\Qrcode\GenerateCollectMoney($store_id))->removeQrcode();
        }

        ecjia_merchant::admin_log(__('刷新收款二维码', 'quickpay'), 'edit', 'collectmoney_qrcode');

        return $this->showmessage(__('刷新成功', 'quickpay'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/mh_qrcode/init')));
    }

}

//end