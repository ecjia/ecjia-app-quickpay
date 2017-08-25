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
 * 闪惠购物流检查订单
 * @author 
 */
class checkOrder_module extends api_front implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {

    	$this->authSession();
    	if ($_SESSION['user_id'] <= 0) {
    		return new ecjia_error(100, 'Invalid session');
    	}

    	$store_id	 		= $this->requestData('store_id', 0);
		$activity_id		= $this->requestData('activity_id', 0);
		$goods_amount 		= $this->requestData('goods_amount', '0.00');
		$exclude_amount  	= $this->requestData('exclude_amount', '0.00');
		
		if (empty($store_id) || empty($activity_id) || empty($goods_amount)) {
			return new ecjia_error('invalid_parameter', RC_Lang::get('system::system.invalid_parameter'));
		}
		
		/*获取闪惠活动信息*/
		$quickpay_activity_info = RC_DB::table('quickpay_activity')->where('store_id', $store_id)->where('id', $activity_id)->first();
		
		if (empty($quickpay_activity_info)) {
			return new ecjia_error('activity_not_exists', '活动信息不存在');
		}
		
		/*活动时间限制处理*/
		$time = RC_Time::gmtime();
		if (($time > $quickpay_activity_info['end_time']) || ($quickpay_activity_info['start_time'] > $time)) {
			return new ecjia_error('activity_error', '活动还未开始或已结束');
		}
		/*自定义时间限制处理*/
		if ($quickpay_activity_info['limit_time_type'] == 'customize') {
			/*每周限制时间*/
			if (!empty($quickpay_activity_info['limit_time_weekly'])){
				$w = date('w');
				$current_week = current_week($w);
				$limit_time_weekly = Ecjia\App\Quickpay\Weekly::weeks($quickpay_activity_info['limit_time_weekly']);
				$weeks_str = get_weeks_str($limit_time_weekly);
				 
				if (!in_array($current_week, $limit_time_weekly)){
					return new ecjia_error('limit_time_weekly_error', '此活动只限'.$weeks_str.'可使用');
				}
			}
			
			/*每天限制时间段*/
// 			if (!empty($quickpay_activity_info['limit_time_daily'])) {
// 				$limit_time_daily = unserialize($quickpay_activity_info['limit_time_daily']);
// 				$time = RC_Time::local_date('H:i', $time);
// 				foreach ($limit_time_daily as $val) {
// 					if (($val['start'] < $time) && ($val['end'] > $time)) {
						
// 					} else {
// 						return new ecjia_error('limit_time_daily_error', '此活动当前时间段不可用');
// 					}
// 				}
// 			}
			/*活动限制日期*/
			if (!empty($quickpay_activity_info['limit_time_exclude'])) {
				$limit_time_exclude = explode(',', $quickpay_activity_info['limit_time_exclude']);
				$current_date = RC_Time::local_date(ecjia::config('date_format'), time);
				$current_date = array($current_date);
				if (in_array($current_date, $limit_time_exclude)) {
					return new ecjia_error('limit_time_daily_error', '此活动当前日期不可用！');
				}
			}
			
		}
		/*活动是否允许使用积分处理*/
		if ($quickpay_activity_info['use_integral'] == 'nolimit') {
			//无积分限制；最多可用积分按商品价格兑换
			$allow_use_integral = 1;
			$scale = floatval(ecjia::config('integral_scale'));
			$integral = (($goods_amount - $exclude_amount)/$scale)*100;
			$order_max_integral = intval($integral);
		} elseif (($quickpay_activity_info['use_integral'] != 'nolimit') && ($quickpay_activity_info['use_integral'] != 'close')) {
			//有积分限制
			$allow_use_integral = 1;
			$order_max_integral = $quickpay_activity_info['use_integral'];
		} else{
			//不可用积分
			$allow_use_integral = 0;
			$order_max_integral = 0;
		}
		
		/*会员可用积分数*/
		$user_integral = RC_DB::table('users')->where('user_id', $_SESSION['user_id'])->pluck('pay_points');
		
		/*活动是否允许使用红包*/
		if (ecjia::config('use_bonus') == '1') {
			if ($quickpay_activity_info['use_bonus'] == 'nolimit') {
				//无限制红包；获取用户可用红包
				// 取得用户可用红包
				$real_amount = $goods_amount - $exclude_amount;
				$user_bonus = user_bonus($_SESSION['user_id'], $real_amount);
				if (!empty($user_bonus)) {
					foreach ($user_bonus AS $key => $val) {
						$user_bonus[$key]['bonus_money_formated'] = price_format($val['type_money'], false);
					}
					$bonus_list = $user_bonus;
				}
				// 能使用红包
				$allow_use_bonus = 1;
			} elseif (($quickpay_activity_info['use_bonus'] != 'nolimit') && $quickpay_activity_info['use_bonus'] != 'close') {
				$bonus_list = get_acyivity_bonus();
				$allow_use_bonus = 1;
			} else{
				$allow_use_bonus = 0;
				$bonus_list = array();
			}
		}
		
	}
}


/**
 * 获取周
 */
 function get_week_list(){
	$week_list = array(
			'星期一'	=> Ecjia\App\Quickpay\Weekly::Monday,
			'星期二'	=> Ecjia\App\Quickpay\Weekly::Tuesday,
			'星期三'	=> Ecjia\App\Quickpay\Weekly::Wednesday,
			'星期四' => Ecjia\App\Quickpay\Weekly::Thursday,
			'星期五' => Ecjia\App\Quickpay\Weekly::Friday,
			'星期六' => Ecjia\App\Quickpay\Weekly::Saturday,
			'星期日' => Ecjia\App\Quickpay\Weekly::Sunday,
	);
	return $week_list;
}

/**
 * 获取当前天是星期几
 */
function current_week($w){
	if ($w == 1) {
		$current_week = Ecjia\App\Quickpay\Weekly::Monday;
	} elseif ($w == 2) {
		$current_week = Ecjia\App\Quickpay\Weekly::Tuesday;
	} elseif ($w == 3) {
		$current_week = Ecjia\App\Quickpay\Weekly::Wednesday;
	} elseif ($w == 4) {
		$current_week = Ecjia\App\Quickpay\Weekly::Thursday;
	} elseif ($w == 5) {
		$current_week = Ecjia\App\Quickpay\Weekly::Friday;
	} elseif ($w == 6) {
		$current_week = Ecjia\App\Quickpay\Weekly::Saturday;
	} elseif ($w == 0) {
		$current_week = Ecjia\App\Quickpay\Weekly::Sunday;
	}
	
	return $current_week;
}

/**
 * 获取活动可使用的星期
 * 
 */
function get_weeks_str($limit_time_weekly){
	$week_list = get_week_list();
	foreach ($week_list as $k => $v) {
		if (in_array($v, $limit_time_weekly)) {
			$week[] = $k;
		}
	}
	if (!empty($week)) {
		if ($week == array(1,2,4,8,16)) {
			$week = '周一至周五';
		} elseif ($week == array(1,2,4,8,16,32,64)) {
			$week = '周一至周日';
		} else {
			$week = implode(',', $week);
		}
	}
	return $week;
}


/**
 * 取得用户当前可用红包
 * @param   int	 $user_id		用户id
 * @param   float   $goods_amount   订单商品金额
 * @return  array   红包数组
 */
function user_bonus($user_id, $goods_amount = 0) {
	$today = RC_Time::gmtime();
	$dbview = RC_DB::table('user_bonus as ub')
				->leftJoin('bonus_type as bt', RC_DB::raw('bt.type_id'), '=', RC_DB::raw('ub.bonus_type_id'));
	$dbview->where(RC_DB::raw('bt.use_start_date'), '<=', $today)
			->where(RC_DB::raw('bt.use_end_date'), '>=', $today)
			->where(RC_DB::raw('bt.min_goods_amount'), '<=', $goods_amount)
			->where(RC_DB::raw('ub.user_id'), $user_id)
			->where(RC_DB::raw('ub.order_id'), 0);
	$row = $dbview->selectRaw('bt.type_id, bt.type_name, bt.type_money, ub.bonus_id, bt.usebonus_type')->get();
	return $row;
}

// end