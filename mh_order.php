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
 * 闪惠订单管理
 */
class mh_order extends ecjia_merchant {
	public function __construct() {
		parent::__construct();
		
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');
		RC_Style::enqueue_style('uniform-aristo');
		
		RC_Script::enqueue_script('bootstrap-editable-script', dirname(RC_App::app_dir_url(__FILE__)) . '/merchant/statics/assets/bootstrap-fileupload/bootstrap-fileupload.js', array());
		RC_Style::enqueue_style('bootstrap-fileupload', dirname(RC_App::app_dir_url(__FILE__)) . '/merchant/statics/assets/bootstrap-fileupload/bootstrap-fileupload.css', array(), false, false);
		
		//时间控件
		RC_Style::enqueue_style('datepicker', RC_Uri::admin_url('statics/lib/datepicker/datepicker.css'));
		RC_Style::enqueue_style('datetimepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datetimepicker.min.css'));
		RC_Script::enqueue_script('bootstrap-datepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datepicker.min.js'));
		RC_Script::enqueue_script('bootstrap-datetimepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datetimepicker.js'));
		
		RC_Script::enqueue_script('mh_order', RC_App::apps_url('statics/js/mh_order.js', __FILE__));
		RC_Style::enqueue_style('mh_orders', RC_App::apps_url('statics/css/mh_orders.css', __FILE__), array(), false, false);
		
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠管理', RC_Uri::url('quickpay/mh_order/init')));
		ecjia_merchant_screen::get_current_screen()->set_parentage('quickpay', 'quickpay/mh_order.php');
	}

	/**
	 * 闪惠订单列表页面
	 */
	public function init() {
	    $this->admin_priv('mh_quickpay_order_manage');
	    
	    ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠订单'));
	    $this->assign('ur_here', '闪惠订单列表');
	    
	    $this->assign('action_link', array('text' => '订单查询', 'href' => RC_Uri::url('quickpay/mh_order/search_order')));
	    	    
	    $type_list = $this->get_quickpay_type();
	    $this->assign('type_list', $type_list);
	    
	    $status_list = $this->get_status_list();
	    $this->assign('status_list', $status_list);
	    
	    $order_list = $this->order_list($_SESSION['store_id']);
	    $this->assign('order_list', $order_list);
	    $this->assign('filter', $order_list['filter']);
	    
	    $this->assign('search_action', RC_Uri::url('quickpay/mh_order/init'));
	    
	    $this->display('quickpay_order_list.dwt');
	}
	
	/**
	 * 闪惠订单详情页面
	 */
	public function order_info() {
		$this->admin_priv('mh_quickpay_order_manage');
		 
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠订单信息'));
		$this->assign('ur_here', '闪惠订单信息');

		$order_id = intval($_GET['order_id']);
		$order_info = RC_DB::table('quickpay_orders')->where('order_id', $order_id)->first();
		$order_info['pay_time'] = RC_Time::local_date(ecjia::config('time_format'), $order_info['pay_time']);
		if ($order_info['activity_type'] == 'normal') {
			$order_info['activity_name'] = '无优惠';
		} elseif ($order_info['activity_type'] == 'discount') { 
			$order_info['activity_name'] = '价格折扣';
		} elseif ($order_info['activity_type'] == 'everyreduced') { 
			$order_info['activity_name'] = '每满多少减多少,最高减多少';
		} elseif ($order_info['activity_type'] == 'reduced') { 
			$order_info['activity_name'] = '满多少减多少';
		}
		
		$this->assign('order_info', $order_info);
		
		$act_list = array();
		$data = RC_DB::table('quickpay_order_action')->where('order_id', $order_id)->orderby('order_id', 'asc')->get();
		foreach ($data as $key => $row) {
			$row['add_time']	= RC_Time::local_date(ecjia::config('time_format'), $row['add_time']);
			$act_list[]			= $row;
		}
	
		$this->assign('action_list', $act_list);
		
		$this->assign('form_action', RC_Uri::url('quickpay/mh_order/order_action', array('type' => 'order_info')));
		
		$this->display('quickpay_order_info.dwt');
	}
	
	/**
	 * 审核处理
	 */
	public function order_action() {
		$this->admin_priv('mh_quickpay_order_update');
		
		$action_note = trim($_POST['action_note']);
		$order_id    = intval($_POST['order_id']);
		$type		 = trim($_GET['type']);
		
		$data = array(
			'order_id'			=> $order_id,
			'action_user_id'	=> $_SESSION['staff_id'],
			'action_user_name'	=> $_SESSION['staff_name'],
			'action_user_type'	=> 'merchant',
			'order_status'		=> 0,
			'action_note'		=> $action_note,
			'add_time'			=> RC_Time::gmtime(),
		);
		RC_DB::table('quickpay_order_action')->insertGetId($data);
		if($type) {
			return $this->showmessage('核实成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/mh_order/order_info', array('order_id' => $order_id))));
		} else {
			return $this->showmessage('核实成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/mh_order/init')));
		}
		
	}

	/**
	 * 批量操作闪惠订单
	 */
	public function batch() {
		$this->admin_priv('mh_quickpay_order_delete');
	
		$ids  = explode(',', $_POST['order_id']);
		RC_DB::table('quickpay_orders')->whereIn('order_id', $ids)->delete();
	
		return $this->showmessage('批量删除成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/mh_order/init')));
	}
	
	
	/**
	 * 闪惠订单查询
	 */
	public function search_order() {
		$this->admin_priv('mh_quickpay_order_search');
		 
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠订单查询'));
		$this->assign('ur_here', '闪惠订单查询');
		 
		$this->assign('action_link', array('text' => '闪惠订单列表', 'href' => RC_Uri::url('quickpay/mh_order/init')));
	
		$type_list = $this->get_quickpay_type();
		$this->assign('type_list', $type_list);
		 
// 		$status_list = $this->get_status_list();
// 		$this->assign('status_list', $status_list);
		 
		$order_list = $this->order_list($_SESSION['store_id']);
		$this->assign('order_list', $order_list);
		$this->assign('filter', $order_list['filter']);
		 
		$this->assign('form_action', RC_Uri::url('quickpay/mh_order/init'));
		 
		$this->display('quickpay_order_search.dwt');
	}

	/**
	 * 获取闪惠规则列表
	 */
	private function order_list($store_id) {
		$db_quickpay_order = RC_DB::table('quickpay_orders');
		$db_quickpay_order->where('store_id', $store_id);
		
		$filter = $_GET;
		$where = array();
		if ($filter['order_sn']) {
			$db_quickpay_order->where('order_sn', 'like', '%'.mysql_like_quote($filter['order_sn']).'%');
		}
		
		if ($filter['user_name']) {
			$db_quickpay_order->where('user_name', 'like', '%'.mysql_like_quote($filter['user_name']).'%');
		}
		
		if ($filter['user_mobile']) {
			$db_quickpay_order->where('mobile', 'like', '%'.mysql_like_quote($filter['user_mobile']).'%');
		}
		
		if ($filter['start_time']) {
			$start_time = RC_Time::local_strtotime($filter['start_time']);
			$db_quickpay_order->where(RC_DB::raw('pay_time'), '>=', $start_time);
		}
		
		if ($filter['end_time']) {
			$end_time = RC_Time::local_strtotime($_GET['end_time']);
			$db_quickpay_order->where(RC_DB::raw('pay_time'), '<=', $end_time);
		}

		$filter['keywords'] = empty($_GET['keywords']) ? '' : trim($_GET['keywords']);
		if ($filter['keywords']) {
			$db_quickpay_order->where('order_sn', 'like', '%'.mysql_like_quote($filter['keywords']).'%')->orWhere('user_name', 'like', '%' . mysql_like_quote($filter['keywords']) . '%');
		}	
		
		$filter['activity_type'] = empty($_GET['activity_type']) ? '' : trim($_GET['activity_type']);
		if ($filter['activity_type']) {
			$db_quickpay_order->where('activity_type', $filter['activity_type']);
		}
		
		$check_type = trim($_GET['check_type']);
// 		$order_count = $db_quickpay_order->select(RC_DB::raw('count(*) as count'),
// 				RC_DB::raw('SUM(IF(check_status != 0, 1, 0)) as check_ok'),
// 				RC_DB::raw('SUM(IF(check_status = 0, 1, 0)) as check_no'))->first();
		
// 		if ($check_type == 'check_ok') {
// 			$db_quickpay_order->where('check_status', '>', 0);
// 		}
		
// 		if ($check_type == 'check_no') {
// 			$db_quickpay_order->where('check_status', 0);
// 		}

		$count = $db_quickpay_order->count();
		$page = new ecjia_merchant_page($count,10, 5);
		$data = $db_quickpay_order
		->selectRaw('order_id,order_sn,activity_type,user_mobile,user_name,add_time,goods_amount,order_amount')
		->orderby('order_id', 'asc')
		->take(10)
		->skip($page->start_id-1)
		->get();
		$res = array();
		if (!empty($data)) {
			foreach ($data as $row) {
				$row['add_time'] = RC_Time::local_date(ecjia::config('time_format'), $row['add_time']);
				$res[] = $row;
			}
		}

		return array('list' => $res, 'filter' => $filter, 'page' => $page->show(2), 'desc' => $page->page_desc(), 'count' => $order_count);
	}
	


	/**
	 * 获取闪惠类型
	 */
	private function get_quickpay_type(){
		$type_list = array(
			'normal' 	=> '无优惠',
			'discount'	=> '价格折扣',
			'reduced'   => '满多少减多少',
			'everyreduced' 	 => '每满多少减多少,最高减多少'
		);
		return $type_list;
	}
	
	/**
	 * 获取订单状态
	 */
	private function get_status_list(){
		$status_list = array(
// 				'未确认'	=> Ecjia\App\Quickpay\Status::UNCONFIRMED,
// 				'已确认'	=> Ecjia\App\Quickpay\Status::CONFIRMED,
// 				'未支付'	=> Ecjia\App\Quickpay\Status::UNPAYED,
// 				'已支付' => Ecjia\App\Quickpay\Status::PAYED,
				'未核实' => Ecjia\App\Quickpay\Status::UNCHECKED,
				'已核实' => Ecjia\App\Quickpay\Status::CHECKED,
				'无效' => Ecjia\App\Quickpay\Status::INVALID,
		);
		return $status_list;
	}

}

//end