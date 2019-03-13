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
 * 资金流水买单列表（支付完成的买单订单）
 * @author zrl
 */
class admin_cashier_quickpay_summary_records_module extends api_admin implements api_interface
{
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request)
    {

        $this->authadminSession();
        if ($_SESSION['staff_id'] <= 0) {
            return new ecjia_error(100, __('Invalid session', 'orders'));
        }

        $device        = $this->device;
        $device_code   = isset($device['code']) ? $device['code'] : '';
		
        $codes         = RC_Loader::load_app_config('cashier_device_code', 'cashier');

        if (!in_array($device_code, $codes)) {
            return new ecjia_error('caskdesk_error', __('非收银台请求！', 'quickpay'));
        }

        $size = $this->requestData('pagination.count', 15);
        $page = $this->requestData('pagination.page', 1);

        $start_date = $this->requestData('start_date');
        $end_date   = $this->requestData('end_date');
     
        //日期筛选条件
        if (!empty($start_date) && !empty($end_date)) {
            $start_date = RC_Time::local_strtotime($start_date);
            $end_date   = RC_Time::local_strtotime($end_date) + 86399;
        }

        $dbview  = RC_DB::table('cashier_record as cr')->leftJoin('quickpay_orders as qo', RC_DB::raw('cr.order_id'), '=', RC_DB::raw('qo.order_id'));

        $order_list = [];

        $dbview->where(RC_DB::raw('cr.store_id'), $_SESSION['store_id'])
        		->where(RC_DB::raw('cr.action'), 'receipt')
        		->where(RC_DB::raw('qo.order_type'), 'cashdesk-receipt')
        		->where(RC_DB::raw('qo.referer'), '=', 'ecjia-cashdesk')
				->where(RC_DB::raw('qo.pay_status'), \Ecjia\App\Quickpay\Enums\QuickpayPayEnum::PAID);

        $device_type = Ecjia\App\Cashier\CashierDevice::get_device_type($device['code']);
        //收银台和收银POS区分设备，收银通不区分设备；
        if ($device_code == Ecjia\App\Cashier\CashierDevice::CASHIERCODE) {
            $dbview->where(RC_DB::raw('cr.device_type'), $device_type);
        } else {
            $dbview->where(RC_DB::raw('cr.mobile_device_id'), $_SESSION['device_id']);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $where['cr.create_at'] = array('egt' => $start_date, 'elt' => $end_date);
            $dbview->where(RC_DB::raw('cr.create_at'), '>=', $start_date);
            $dbview->where(RC_DB::raw('cr.create_at'), '<=', $end_date);
        }

        $record_count = $dbview->count(RC_DB::raw('DISTINCT qo.order_id'));
        $page_row     = new ecjia_page($record_count, $size, 6, '', $page);
        $total_fee    = "(qo.goods_amount - qo.discount - qo.integral_money - qo.bonus) as total_fee";
        $field        = 'qo.order_id, qo.surplus, qo.goods_amount, qo.order_amount, qo.store_id, qo.integral, qo.integral_money, qo.bonus, qo.order_sn, qo.pay_name, ' . $total_fee . ', qo.discount, qo.add_time';

        $order_list = [];
        $data = $dbview->take($size)->skip($page_row->start_id - 1)->select(RC_DB::raw($field))->orderBy(RC_DB::raw('cr.create_at'), 'desc')->groupBy(RC_DB::raw('qo.order_id'))->get();

        $data       = $this->formated_order_list($data);
        $order_list = $data;

        $pager = array(
            'total' => $page_row->total_records,
            'count' => $page_row->total_records,
            'more'  => $page_row->total_pages <= $page ? 0 : 1,
        );
        return array('data' => $order_list, 'pager' => $pager);
    }

    /**
     * 订单列表数据处理
     * @param array $data
     * return array
     */
    private function formated_order_list($data = array())
    {
    	$record_list = [];
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $record_list[] = array(
                		'order_id' 			=> intval($val['order_id']),
                		'order_sn' 			=> trim($val['order_sn']),
                		'total_fee'			=> $val['total_fee'],
                		'formated_total_fee'=> ecjia_price_format($val['total_fee'], false),
                		'pay_name'			=> $val['pay_name'],
                		'add_time'			=> RC_Time::local_date('Y-m-d H:i:s', $val['add_time']),
                );
            }
        }
        return $record_list;
    }
}

// end