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

class done_module extends api_front implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {
		/**
         * goods_amount    //消费金额
         * exclude_amount  //不可参与优惠金额
         * activity_id     //活动id
         * discount   	   //活动优惠金额
         * bonus_id  	   //红包id
         * bonus           //红包金额
         * integral        //积分数量
         * inv_type       //integral_money
         * order_amount   //实付金额
         * pay_id    	  //支付方式id
         * surplus		  //余额
         */
    	
    	$this->authSession();
    	if ($_SESSION['user_id'] <= 0) {
    		return new ecjia_error(100, 'Invalid session');
    	}
    	
    	RC_Loader::load_app_class('quickpay_activity', 'quickpay', false);
    	
    	$user_id = $_SESSION['user_id'];
    	$device = $this->device;
    	
    	$activity_id	= $this->requestData('activity_id', 0);
    	$goods_amount 	= $this->requestData('goods_amount', '0.00');
    	$exclude_amount = $this->requestData('exclude_amount', '0.00');
    	$bonus_id		= $this->requestData('bonus_id', 0);
    	$integral		= $this->requestData('integral', 0);
    	//$pay_id			= $this->requestData('pay_id', 0);
    	$store_id		= $this->requestData('store_id', 0);
    	
		if (empty($goods_amount) || empty($store_id)) {
			return new ecjia_error( 'invalid_parameter', RC_Lang::get ('system::system.invalid_parameter'));
		}
    	
    	/*商家闪惠功能是否开启*/
		$quickpay_enabled = RC_DB::table('merchants_config')->where('store_id', $store_id)->where('code', 'quickpay_enabled')->pluck('value');
		if (empty($quickpay_enabled)) {
			return new ecjia_error('quickpay_enabled_error', '此商家未开启优惠买单功能！');
		}
		
		/*初始化订单信息*/
		$order = array();
		$order = array(
				'goods_amount'		=> $goods_amount,
				'surplus'   		=> $this->requestData('surplus', 0.00),
				'integral'     		=> $integral,
				'bonus_id'     		=> $bonus_id,
				'user_id'      		=> $_SESSION['user_id'],
				'add_time'     		=> RC_Time::gmtime(),
				'order_status'  	=> Ecjia\App\Quickpay\Status::UNCONFIRMED,
		);

		if (!empty($activity_id) && $activity_id > 0) {
			/*获取闪惠活动信息*/
			$quickpay_activity_info = RC_DB::table('quickpay_activity')->where('id', $activity_id)->first();
			if (empty($quickpay_activity_info)) {
				return new ecjia_error('activity_not_exists', '活动信息不存在');
			}
			
			if ($quickpay_activity_info['enabled'] == '0') {
				return new ecjia_error('activity_closed', '此活动已关闭！');
			}
			$order['activity_type'] = $quickpay_activity_info['activity_type'];
			$order['activity_id'] = $quickpay_activity_info['id'];
			$order['store_id'] = $quickpay_activity_info['store_id'];
			
			/*活动时间限制处理*/
			$time = RC_Time::gmtime();
			if (($time > $quickpay_activity_info['end_time']) || ($quickpay_activity_info['start_time'] > $time)) {
				return new ecjia_error('activity_error', '活动还未开始或已结束');
			}
			
			/*红包是否可用*/
			if ($bonus_id > 0) {
				$bonus_info = RC_Api::api('bonus', 'bonus_info', array('bonus_id' => $bonus_id));
				if (is_ecjia_error($bonus_info)) {
					return $bonus_info;
				}
				if (empty($bonus_info)){
					return new ecjia_error('bonus_error', '红包信息不存在！');
				}
				$time = RC_Time::gmtime();
				if (($time < $bonus_info['use_start_date']) || ($bonus_info['use_end_date'] < $time) || ($bonus_info['store_id'] != 0 && $bonus_info['store_id'] != $quickpay_activity_info['store_id']) || $bonus_info['user_id'] != $user_id || $bonus_info['order_id'] > 0) {
					$order['bonus_id'] = 0;
					$order['bonus'] = 0.00;
				} else{
					$order['bonus_id'] = $bonus_id;
					$order['bonus'] = $bonus_info['type_money'];
				}
					
			}
			
			if ($integral > 0) {
				/*会员可用积分数*/
				$user_integral = RC_DB::table('users')->where('user_id', $_SESSION['user_id'])->pluck('pay_points');
				if ($integral > $user_integral) {
					return new ecjia_error('integral_error', '使用积分不可超过会员总积分数！');
				}
				$order['integral_money'] = quickpay_activity::integral_of_value($integral);
			}
			
			/*活动可优惠金额获取*/
			$discount = quickpay_activity::get_quickpay_discount(array('activity_type' => $quickpay_activity_info['activity_type'], 'activity_value' => $quickpay_activity_info['activity_value'], 'goods_amount' => $goods_amount, 'exclude_amount' => $exclude_amount));
			
			/*自定义时间限制处理，当前时间不可用时，订单可正常提交,只是优惠金额是0；红包和积分也为0*/
			if ($quickpay_activity_info['limit_time_type'] == 'customize') {
				/*每周限制时间*/
				if (!empty($quickpay_activity_info['limit_time_weekly'])){
					$w = date('w');
					$current_week = quickpay_activity::current_week($w);
					$limit_time_weekly = Ecjia\App\Quickpay\Weekly::weeks($quickpay_activity_info['limit_time_weekly']);
					$weeks_str = quickpay_activity::get_weeks_str($limit_time_weekly);
						
					if (!in_array($current_week, $limit_time_weekly)){
						//return new ecjia_error('limit_time_weekly_error', '此活动只限'.$weeks_str.'可使用');
						$discount = 0.00;
						$order['integral_money'] = 0.00;
						$order['bonus'] = 0.00;
					}
				}
			
				/*每天限制时间段*/
				if (!empty($quickpay_activity_info['limit_time_daily'])) {
					$limit_time_daily = unserialize($quickpay_activity_info['limit_time_daily']);
					foreach ($limit_time_daily as $val) {
						$arr[] = quickpay_activity::is_in_timelimit(array('start' => $val['start'], 'end' => $val['end']));
					}
					if (!in_array(0, $arr)) {
						//return new ecjia_error('limit_time_daily_error', '此活动当前时间段不可用');
						$discount = 0.00;
						$order['integral_money'] = 0.00;
						$order['bonus'] = 0.00;
					}
				}
				/*活动限制日期*/
				if (!empty($quickpay_activity_info['limit_time_exclude'])) {
					$limit_time_exclude = explode(',', $quickpay_activity_info['limit_time_exclude']);
					$current_date = RC_Time::local_date(ecjia::config('date_format'), time);
					$current_date = array($current_date);
					if (in_array($current_date, $limit_time_exclude) || $current_date == $limit_time_exclude) {
						//return new ecjia_error('limit_time_daily_error', '此活动当前日期不可用！');
						$discount = 0.00;
						$order['integral_money'] = 0.00;
						$order['bonus'] = 0.00;
					}
				}
			
			}
		} else {
			$order['activity_type'] = 'normal';
			$order['activity_id'] = 0;
			$order['bonus_id'] = 0;
			$order['bonus'] = 0.00;
			$order['integral_money'] = 0.00;
			$discount = 0.00;
		}
		
		/*活动可优惠金额处理*/
		$order['discount'] = sprintf("%.2f", $discount);
		$formated_discount = price_format($order['discount'], false);
		
		$order['store_id'] = $store_id;
		/*订单编号*/
		$order['order_sn'] = quickpay_activity::get_order_sn();
		$order['order_type'] = 'quickpay';
		
		/*支付方式信息*/
		//if ($pay_id > 0) {
		//	$payment_method = RC_Loader::load_app_class('payment_method','payment');
		//	$payment_info = $payment_method->payment_info_by_id($pay_id);
		//	$order['pay_code'] = $payment_info['pay_code'];
		//	$order['pay_name'] = $payment_info['pay_name'];
		//}
		
		/*会员信息*/
		$user_id = $_SESSION['user_id'];
		if ($user_id > 0){
			$user_info = RC_Api::api('user', 'user_info', array('user_id' => $user_id));
			if (is_ecjia_error($user_info)) {
				return $user_info;
			}
			$order['user_name'] 	= $user_info['user_name'];
			$order['user_mobile'] 	= $user_info['mobile_phone'];
			$order['user_type'] 	= 'user';
		}
		
		/*订单来源*/
		$order['from_ad'] = ! empty($_SESSION['from_ad']) ? $_SESSION['from_ad'] : '0';
		$order['referer'] = ! empty($device['device']['client']) ? $device['device']['client'] : 'mobile';
		
    	/*实付金额*/
		$order['order_amount'] = $goods_amount - ($discount + $order['integral_money'] + $order['bonus']);
    	
    	/* 如果订单金额为0（使用余额或积分或红包支付），修改订单状态为已确认、已付款 */
    	if ($order['order_amount'] <= 0) {
    		$order['order_status']	= Ecjia\App\Quickpay\Status::PAID;
    		$order['pay_time']		= RC_Time::gmtime();
    		$order['order_amount']	= 0;
    	}
    	
    	/*插入订单数据*/
    	$db_order_info = RC_DB::table('quickpay_orders');
    	$new_order_id	= $db_order_info->insertGetId($order);
    	
    	$order['order_id'] = $new_order_id;
    	
    	/* 处理积分、红包 */
    	if ($order['user_id'] > 0 && $order['integral'] > 0) {
    		$params = array(
    				'user_id'		=> $order['user_id'],
    				'pay_points'	=> $order['integral'] * (- 1),
    				'change_desc'	=> sprintf(RC_Lang::get('cart::shopping_flow.pay_order'), $order['order_sn'])
    		);
    		$result = RC_Api::api('user', 'account_change_log', $params);
    		if (is_ecjia_error($result)) {
    			return new ecjia_error('integral_error', '积分使用失败！');
    		}
    	}
    	
    	if ($order['bonus_id'] > 0 && $order['bonus'] > 0) {
    		RC_Api::api('bonus', 'use_bonus', array('bonus_id' => $order['bonus_id'], 'order_id' => $new_order_id));
    	}
    	
    	/* 给商家发邮件 */
    	/* 增加是否给客服发送邮件选项 */
    	if (ecjia::config('send_service_email') && ecjia::config('service_email') != '') {
    		$tpl_name = 'remind_of_new_order';
    		$tpl   = RC_Api::api('mail', 'mail_template', $tpl_name);
    		$order['consignee'] = $order['user_name'];
    		$order['mobile'] = $order['user_mobile'];
    		ecjia_front::$controller->assign('order', $order);
    		ecjia_front::$controller->assign('shop_name', ecjia::config('shop_name'));
    		ecjia_front::$controller->assign('send_date', date(ecjia::config('time_format')));
    	
    		$content = ecjia_front::$controller->fetch_string($tpl['template_content']);
    		RC_Mail::send_mail(ecjia::config('shop_name'), ecjia::config('service_email'), $tpl['template_subject'], $content, $tpl['is_html']);
    	}
    	
    	/* 如果需要，发短信 */
    	$staff_user = RC_DB::table('staff_user')->where('store_id', $order['store_id'])->where('parent_id', 0)->first();
    	if (!empty($staff_user['mobile'])) {
    		//发送短信
    		$options = array(
    				'mobile' => $staff_user['mobile'],
    				'event'	 => 'sms_order_placed',
    				'value'  =>array(
    						'order_sn'		=> $order['order_sn'],
    						'consignee' 	=> $order['user_name'],
    						'telephone'  	=> $order['user_mobile'],
    						'order_amount'  => $order['order_amount'],
    						'service_phone' => ecjia::config('service_phone'),
    				),
    		);
    		RC_Api::api('sms', 'send_event_sms', $options);
    	}
    	
    	/* 插入支付日志 */
    	//$order['log_id'] = $payment_method->insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);
    	
    	//$payment_info = $payment_method->payment_info_by_id($pay_id);
    	//支付方式列表
    	//$payment_list = RC_Api::api('payment', 'available_payments');
    	//if (is_ecjia_error($payment_list)) {
    	//	return $payment_list;
    	//}
    	$store_name = RC_DB::table('store_franchisee')->where('store_id', $store_id)->pluck('merchants_name');
    	$shop_logo = RC_DB::table('merchants_config')->where('store_id', $store_id)->where('code', 'shop_logo')->pluck('value');
    	
    	$order_info = array(
    			'order_sn'   => $order['order_sn'],
    			'order_id'   => $order['order_id'],
    			'store_id'   => $store_id,
    			'store_name' => $store_name,
    			'store_logo' =>  !empty($shop_logo) ? RC_Upload::upload_url($shop_logo) : '',
    			'order_info' => array(
    					//'pay_code'               => $payment_info['pay_code'],
    					'order_amount'           => $order['order_amount'],
    					'formatted_order_amount' => price_format($order['order_amount']),
    					'order_id'               => $order['order_id'],
    					'order_sn'               => $order['order_sn']
    			)
    	);
    	
    	
    	RC_DB::table('order_status_log')->insert(array(
    	'order_status'	=> RC_Lang::get('cart::shopping_flow.label_place_order'),
    	'order_id'		=> $order['order_id'],
    	'message'		=> '下单成功，订单号：'.$order['order_sn'],
    	'add_time'		=> RC_Time::gmtime(),
    	));
    	
    	if (!$payment_info['is_cod'] && $order['order_amount'] > 0) {
    		RC_DB::table('order_status_log')->insert(array(
    		'order_status'	=> RC_Lang::get('cart::shopping_flow.unpay'),
    		'order_id'		=> $order['order_id'],
    		'message'		=> '请尽快支付该订单，超时将会自动取消订单',
    		'add_time'		=> RC_Time::gmtime(),
    		));
    	}
    	
    	if (!empty($staff_user)) {
    		//新的推送消息方法
    		$options = array(
    		'user_id'   => $staff_user['user_id'],
    		'user_type' => 'merchant',
    		'event'     => 'order_placed',
	    	'value' 	=> array(
					    		'order_sn'     => $order['order_sn'],
					    		'consignee'    => $order['user_name'],
					    		'telephone'    => $order['user_mobile'],
					    		'order_amount' => $order['order_amount'],
					    		'service_phone'=> ecjia::config('service_phone'),
	    					),
	    	'field' 	=> array(
				    			'open_type' => 'admin_message',
				    		),
    		);
    		RC_Api::api('push', 'push_event_send', $options);
    	}
    	
    	return $order_info;
    }
}

// end