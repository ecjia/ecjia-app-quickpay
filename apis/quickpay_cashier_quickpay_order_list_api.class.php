<?php
defined('IN_ECJIA') or exit('No permission resources.');
/**
 * 收银台收款订单记录
 * @author zrl
 *
 */
class quickpay_cashier_quickpay_order_list_api extends Component_Event_Api {
    /**
     * @param  array $options['store_id']	店铺id
     * @return array
     */
	public function call(&$options) {
		if (!is_array($options)) {
			return new ecjia_error('invalid_parameter', '调用api文件,cashier_quickpay_order_list,参数无效');
		}
		return $this->cashier_quickpay_order_list($options);
	}
	
	
	/**
	 * 买单订单列表
	 * @param   array $options	条件参数
	 * @return  array   买单订单列表
	 */
	
	private function cashier_quickpay_order_list($options) {
		RC_Loader::load_app_class('quickpay_activity', 'quickpay', false);
		
		$dbview = RC_DB::table('quickpay_orders as qo')->leftJoin('cashier_record as cr', RC_DB::raw('qo.order_id'), '=', RC_DB::raw('cr.order_id'));
		
		if (!empty($options['store_id'])) {
			$dbview->where(RC_DB::raw('qo.store_id'), $options['store_id']);
		}
		
		if (!empty($options['order_type'])) {
			if ($options['order_type'] == 'user') {
				$dbview->where((RC_DB::raw('qo.order_type')), 'quickpay');
			} elseif ($options['order_type'] == 'cashdesk') {
				$dbview->where(RC_DB::raw('qo.order_type'), 'cashdesk-receipt');
			}
		}
		
		$size  	  = empty($options['size']) 		? 15 : intval($options['size']);
		$page 	  = empty($options['page']) 		? 1 : intval($options['page']);
		
		$deleted_status = Ecjia\App\Quickpay\Status::DELETED;
		$canceled_status = Ecjia\App\Quickpay\Status::CANCELED;
		
		$dbview->where(RC_DB::raw('qo.order_status'), '<>', $deleted_status);
		$dbview->where(RC_DB::raw('qo.order_status'), '<>', $canceled_status);
		$dbview->where(RC_DB::raw('cr.action'), 'receipt');
		
		
		if (!empty($options['start_date']) && !empty($options['end_date'])) {
			$start_date = RC_Time::local_strtotime($start_date);
			$end_date = RC_Time::local_strtotime($end_date) + 86399;
			$dbview->where(RC_DB::raw('cr.create_at'), '>=', $start_date);
			$dbview->where(RC_DB::raw('cr.create_at'), '<=', $end_date);
		}
		
		if (!empty($options['mobile_device_id'])) {
			$dbview->where(RC_DB::raw('cr.mobile_device_id'), $options['mobile_device_id']);
		}
		$count = $dbview->count(RC_DB::raw('DISTINCT cr.order_id'));
		
		$page_row = new ecjia_page($count, $size, 6, '', $page);
		
		$list = $dbview->take($size)->skip($page_row->start_id - 1)->select(RC_DB::raw('qo.*'))->orderBy(RC_DB::raw('cr.create_at'), 'desc')->get();
		
		$pager = array(
			'total' => $page_row->total_records,
			'count' => $page_row->total_records,
			'more'	=> $page_row->total_pages <= $page ? 0 : 1,
		);
		
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$list[$key]['store_name'] 				= $val['store_id'] > 0 ? RC_DB::table('store_franchisee')->where('store_id', $val['store_id'])->pluck('merchants_name') : '';
				$list[$key]['store_logo'] 				= $val['store_id'] > 0 ? RC_DB::table('merchants_config')->where('store_id', $val['store_id'])->where('code', 'shop_logo')->pluck('value') : '';
				$status 								= quickpay_activity::get_label_order_status($val['order_status'], $val['pay_status'], $val['verification_status']);
				$list[$key]['order_status_str'] 		= $status['order_status_str'];
				$list[$key]['label_order_status'] 		= $status['label_order_status'];
				$total_discount 						= $val['integral_money'] + $val['bonus'] + $val['discount'];
				$list[$key]['total_discount'] 			= $total_discount;
				$list[$key]['formated_total_discount'] 	= price_format($total_discount);
			}
		}
		
		return array('list' => $list, 'page' => $pager);
	}
}

// end