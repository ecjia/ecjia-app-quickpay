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
 * 买单购物流检查订单
 * @author 
 */
class quickpay_flow_checkOrder_module extends api_front implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {

    	$this->authSession();
    	if ($_SESSION['user_id'] <= 0) {
    		return new ecjia_error(100, 'Invalid session');
    	}
    	
    	RC_Loader::load_app_class('quickpay_activity', 'quickpay', false);
    	
    	$store_id	 		= $this->requestData('store_id', 0);
		//$activity_id		= $this->requestData('activity_id', 0);
		$goods_amount 		= $this->requestData('goods_amount', '0.00');
		$is_exclude_amount  = $this->requestData('is_exclude_amount', 0);
		$exclude_amount  	= $this->requestData('exclude_amount', '0.00');
		
		//if (empty($is_exclude_amount)) {
		//	$exclude_amount = '0.00';
		//}
		
		if ($goods_amount > 0 && $exclude_amount > 0) {
			if ($exclude_amount > $goods_amount) {
				return new ecjia_error('exclude_amount_error', '不可参与活动金额不能大于消费金额！');
			}
		}
		
		if (empty($store_id) || empty($goods_amount)) {
			return new ecjia_error('invalid_parameter', __('参数无效', 'quickpay'));
		}
		
		//店铺有没锁定
		if (!empty($store_id)) {
			$store_status 	= Ecjia\App\Cart\StoreStatus::GetStoreStatus($store_id);
			if ($store_status == Ecjia\App\Cart\StoreStatus::LOCKED) {
				return new ecjia_error('store_locked', '对不起，该店铺已锁定！');
			}
		}
		
		/*商家买单功能是否开启*/
		$quickpay_enabled = RC_DB::table('merchants_config')->where('store_id', $store_id)->where('code', 'quickpay_enabled')->pluck('value');
		if (empty($quickpay_enabled)) {
			return new ecjia_error('quickpay_enabled_error', '此商家未开启优惠买单功能！');
		}
		
		/*会员可用积分数*/
		$user_integral = RC_DB::table('users')->where('user_id', $_SESSION['user_id'])->pluck('pay_points');
		
		/*获取商家所有满足条件的可用优惠活动，数组*/
		$activitys = quickpay_activity::max_discount_activitys(array('goods_amount' => $goods_amount, 'store_id' => $store_id, 'exclude_amount' => $exclude_amount, 'user_id' => $_SESSION['user_id']));
		if (!empty($activitys)) {	
			foreach ($activitys as $k1 => $v1) {
				/*无优惠过滤*/
				if ($v1['activity_type'] == 'normal') {
					$activitys[$k1]['is_allow_use'] = 0;
				}
				if ($v1['total_act_discount'] == '0') {
					$activitys[$k1]['is_allow_use'] = 0;
				}
					
				/*自定义时间的活动，当前时间段不可用的过滤掉*/
				if ($v1['limit_time_type'] == 'customize') {
					/*每周限制时间*/
					if (!empty($v1['limit_time_weekly'])){
						$w = date('w');
						$current_week = quickpay_activity::current_week($w);
						$limit_time_weekly = Ecjia\App\Quickpay\Weekly::weeks($v1['limit_time_weekly']);
						$weeks_str = quickpay_activity::get_weeks_str($limit_time_weekly);
						if (!in_array($current_week, $limit_time_weekly)){
							$activitys[$k1]['is_allow_use'] = 0;
						}
					}
					/*每天限制时间段*/
					if (!empty($v1['limit_time_daily'])) {
						$limit_time_daily = unserialize($v1['limit_time_daily']);
						foreach ($limit_time_daily as $val1) {
							$arr[] = quickpay_activity::is_in_timelimit(array('start' => $val1['start'], 'end' => $val1['end']));
						}
						if (!in_array(0, $arr)) {
							$activitys[$k1]['is_allow_use'] = 0;
						}
					}
					/*活动限制日期*/
					if (!empty($v1['limit_time_exclude'])) {
						$limit_time_exclude = explode(',', $v1['limit_time_exclude']);
						$current_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::gmtime());
						$current_date = array($current_date);
						if (in_array($current_date, $limit_time_exclude) || $current_date == $limit_time_exclude) {
							$activitys[$k1]['is_allow_use'] = 0;
						}
					}
				}
					
			}
		}

		if (!empty($activitys)) {
			$final = array();
			foreach ($activitys as $k2 => $v2) {
				$final[$v2['id']] = array(
						'id'	=> $v2['id'],
						'final_discount' => $v2['total_act_discount'],
				);
			}
			
			$final_discounts = array();
			foreach ($final as $kk => $vv) {
				$final_discounts[$vv['id']] = $vv['final_discount'];
			}
			
			if (!empty($final_discounts)) {
				/*获取最优惠的活动id*/
				if (count($final_discounts) > 1) {
					$favorable_activity_id = array_keys($final_discounts, max($final_discounts));
					$favorable_activity_id = $favorable_activity_id['0'];
				} else {
					foreach ($final_discounts as $a2 => $b2) {
						$favorable_activity_id = $a2;
					}
				}
			}
		}
	
		if (!empty($activitys) && !empty($favorable_activity_id)) {
			foreach ($activitys as $k3 => $v3) {
				if ($favorable_activity_id == $v3['id']) {
					$activitys[$k3]['is_favorable'] = 1;
					if ($v3['total_act_discount'] == '0') {
						$activitys[$k3]['is_favorable'] = 0;
					}
				} else{
					$activitys[$k3]['is_favorable'] = 0;
				}
			}
		}
		
		//==== 获取商家所有活动优惠信息end  === 
		

		if (!empty($activitys)) {
			$available_activity_list = array();
			foreach ($activitys as $val) {
				$available_activity_list[] = array(
						'is_allow_use'			=> $val['is_allow_use'], 
						'is_favorable'			=> $val['is_favorable'],
						'activity_id' 			=> $val['id'],
						'activity_type' 		=> $val['activity_type'],
						'title' 				=> $val['title'],
						'allow_use_bonus' 		=> $val['allow_use_bonus'],
						'allow_use_integral' 	=> $val['allow_use_integral'],
						'order_max_integral' 	=> $val['order_max_integral'],
						'bonus_list' 			=> $val['act_bonus_list'],
						'discount'				=> sprintf("%.2f", $val['total_act_discount']),
						'formated_discount'     => price_format($val['total_act_discount'], false)
				);
			}
		} else {
			$available_activity_list = array();
		}
		
		/*返回数据*/
		$data = array(
				'goods_amount'				=> $goods_amount,
				'exclude_amount'			=> $exclude_amount,
				'user_integral'				=> $user_integral,
				'activity_list'				=> $available_activity_list,
		);
		
		return $data;
	}
}
// end