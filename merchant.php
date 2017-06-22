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
 * 闪惠管理
 */
class merchant extends ecjia_merchant {
	public function __construct() {
		parent::__construct();
		
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');
		RC_Style::enqueue_style('uniform-aristo');
		
		RC_Script::enqueue_script('bootstrap-editable-script', dirname(RC_App::app_dir_url(__FILE__)) . '/merchant/statics/assets/bootstrap-fileupload/bootstrap-fileupload.js', array());
		RC_Style::enqueue_style('bootstrap-fileupload', dirname(RC_App::app_dir_url(__FILE__)) . '/merchant/statics/assets/bootstrap-fileupload/bootstrap-fileupload.css', array(), false, false);
		//时间控件
		RC_Script::enqueue_script('bootstrap-datetimepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datetimepicker.js'));
		RC_Style::enqueue_style('datetimepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datetimepicker.min.css'));
		
		RC_Script::enqueue_script('mh_quickpay', RC_App::apps_url('statics/js/mh_quickpay.js', __FILE__));
		
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠', RC_Uri::url('quickpay/merchant/init')));
		ecjia_merchant_screen::get_current_screen()->set_parentage('quickpay', 'quickpay/merchant.php');
	}

	
	/**
	 * 闪惠规则列表页面
	 */
	public function init() {
	    $this->admin_priv('quickpay_manage');
	    
	    ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠规则'));
	    $this->assign('ur_here', '闪惠规则列表');
	    
	    $this->assign('action_link', array('text' => '添加闪惠规则', 'href' => RC_Uri::url('quickpay/merchant/add')));
	    
	    $quickpay_enabled = RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'quickpay_enabled')->pluck('value');
	    $this->assign('quickpay_enabled', $quickpay_enabled);
	    
	    $quickpay_list = $this->quickpay_list($_SESSION['store_id']);
	    $this->assign('quickpay_list', $quickpay_list);
	    $type_list = $this->get_quickpay_type();
	    $this->assign('type_list', $type_list);
	    $this->assign('now', RC_Time::local_date(ecjia::config('time_format'), RC_Time::gmtime()));
	    $this->assign('search_action', RC_Uri::url('quickpay/merchant/init'));
	    
	    $this->display('quickpay_list.dwt');
	}
	

	/**
	 * 开启闪惠
	 */
	public function open() {
		$this->admin_priv('quickpay_update');
		
		$count= RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'quickpay_enabled')->count();
		if ($count > 0) {
			RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'quickpay_enabled')->update(array('value' => 1));
		} else {
			RC_DB::table('merchants_config')->insert(array('store_id' => $_SESSION['store_id'], 'code' => 'quickpay_enabled', 'value' => '1'));
		}
		
		return $this->showmessage('开启闪惠成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/merchant/init')));
	}
	
	/**
	 * 关闭闪惠
	 */
	public function close() {
		$this->admin_priv('quickpay_update');
		
		RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'quickpay_enabled')->update(array('value' => 0));
		return $this->showmessage('关闭闪惠成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS,array('pjaxurl' => RC_Uri::url('quickpay/merchant/init')));
	}
	
	/**
	 * 添加员工页面
	 */
	public function add() {
		$this->admin_priv('quickpay_update');
		
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('闪惠规则'));
		$this->assign('ur_here', '添加闪惠规则');
		$this->assign('action_link',array('href' => RC_Uri::url('quickpay/merchant/init'),'text' => '闪惠规则列表'));
		
		$type_list = $this->get_quickpay_type();
		$this->assign('type_list', $type_list);
		$offer_list = $this->get_other_offer();
		$this->assign('offer_list', $offer_list);
		
		$data = array (
			'enabled'       => 1,
			'start_time'    => RC_Time::local_date('Y-m-d H:i', RC_Time::gmtime()),
			'end_time'      => RC_Time::local_date('Y-m-d H:i',RC_Time::local_strtotime("+1 month")),
		);
		$this->assign('data', $data);
		
		$this->assign('form_action', RC_Uri::url('quickpay/merchant/insert'));
		
		$this->display('quickpay_info.dwt');
	}

	/**
	 * 处理添加员工
	 */
	public function insert() {
		
		$this->admin_priv('quickpay_update');
		
		$store_id = $_SESSION['store_id'];
		$title    = trim($_POST['title']);
		$description = trim($_POST['description']);

		if (RC_DB::table('quickpay_activity')->where('title', $title)->where('store_id', $store_id)->count() > 0) {
			return $this->showmessage('当前店铺下已存在该闪惠标题', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}

		$start_time = RC_Time::local_strtotime($_POST['start_time']);
		$end_time   = RC_Time::local_strtotime($_POST['end_time']);
		
		if ($start_time >= $end_time) {
			return $this->showmessage('开始时间不能大于或等于结束时间', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$activity_value = $_POST['activity_value'];
		if (is_array($activity_value)) {
			foreach($activity_value as $row){
				if(empty($row)){
					return $this->showmessage('活动参数不能为空', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
				}
			}
			$activity_value = implode(",", $activity_value);
		} 
		
		$data = array(
			'store_id'		=> $store_id,
			'title'      	=> $title,
			'description'	=> $description,
			'activity_type' => $_POST['activity_type'],
			'activity_value'	=> $activity_value,	
			'limit_time_type'	=> '',
			'limit_time_weekly'	=> '',
			'limit_time_daily'	=> '',	
			'limit_time_exclude'=> '',	
			'start_time'	=> $start_time,
			'end_time'		=> $end_time,		
			'use_integral'	=> '',
			'use_bonus'		=> '',	
			'enabled' 		=> intval($_POST['enabled']),
		);
		
		$id = RC_DB::table('quickpay_activity')->insertGetId($data);
		return $this->showmessage('添加闪惠规则成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/merchant/edit', array('id' => $id))));
	}
	
	/**
	 * 编辑员工页面
	 */
	public function edit() {
		$this->admin_priv('quickpay_update');

		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here('编辑闪惠规则'));
		$this->assign('ur_here', '编辑闪惠规则');
		$this->assign('action_link', array('text' => '闪惠规则列表', 'href' => RC_Uri::url('quickpay/merchant/init')));
		
		$type_list = $this->get_quickpay_type();
		$this->assign('type_list', $type_list);
		$offer_list = $this->get_other_offer();
		$this->assign('offer_list', $offer_list);
		
		$id = intval($_GET['id']);
		$data = RC_DB::table('quickpay_activity')->where('id', $id)->first();
		
		$data['start_time']   = RC_Time::local_date(ecjia::config('time_format'), $data ['start_time']);
		$data['end_time']     = RC_Time::local_date(ecjia::config('time_format'), $data ['end_time']);
		if(strpos($data['activity_value'],',') !== false){
			$data['activity_value']  = explode(",",$data['activity_value']);
		}
		
		$this->assign('data', $data);

		$this->assign('form_action', RC_Uri::url('quickpay/merchant/update'));

		$this->display('quickpay_info.dwt');
	}
	
	/**
	 * 编辑员工信息处理
	 */
	public function update() {
		$this->admin_priv('quickpay_update');
		$id = intval($_POST['id']);
		$title    = trim($_POST['title']);
		$description = trim($_POST['description']);

		if (RC_DB::table('quickpay_activity')->where('title', $title)->where('store_id', $store_id)->count() > 0) {
			return $this->showmessage('当前店铺下已存在该闪惠标题', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}

		$start_time = RC_Time::local_strtotime($_POST['start_time']);
		$end_time   = RC_Time::local_strtotime($_POST['end_time']);
		
		if ($start_time >= $end_time) {
			return $this->showmessage('开始时间不能大于或等于结束时间', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
		}
		
		$activity_value = $_POST['activity_value'];
		if (is_array($activity_value)) {
			foreach($activity_value as $row){
				if(empty($row)){
					return $this->showmessage('活动参数不能为空', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
				}
			}
			$activity_value = implode(",", $activity_value);
		} 
		
		$data = array(
			'title'      	=> $title,
			'description'	=> $description,
			'activity_type' => $_POST['activity_type'],
			'activity_value'	=> $activity_value,	
			'limit_time_type'	=> '',
			'limit_time_weekly'	=> '',
			'limit_time_daily'	=> '',	
			'limit_time_exclude'=> '',	
			'start_time'	=> $start_time,
			'end_time'		=> $end_time,		
			'use_integral'	=> '',
			'use_bonus'		=> '',	
			'enabled' 		=> intval($_POST['enabled']),
		);
		
		RC_DB::table('quickpay_activity')->where('id', $id)->update($data);
		return $this->showmessage('添加闪惠规则成功', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('quickpay/merchant/edit', array('id' => $id))));
	}
	
	/**
	 * 删除员工
	 */
	public function remove() {
    	$this->admin_priv('quickpay_delete');
    	 
    	$id = intval($_GET['id']);
    	RC_DB::table('quickpay_activity')->where('id', $id)->delete();
    	 
    	return $this->showmessage('成功删除该闪惠规则', ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS);
    }
	
	/**
	 * 获取闪惠规则列表
	 */
	private function quickpay_list($store_id) {
		$db_quickpay_activity = RC_DB::table('quickpay_activity');

		$filter['keywords'] = empty($_GET['keywords']) ? '' : trim($_GET['keywords']);
		if ($filter['keywords']) {
			$db_quickpay_activity->where('title', 'like', '%'.mysql_like_quote($filter['keywords']).'%');
		}
		$filter['activity_type'] = empty($_GET['activity_type']) ? '' : trim($_GET['activity_type']);
		if ($filter['activity_type']) {
			$db_quickpay_activity->where('activity_type', $filter['activity_type']);
		}
		
		$db_quickpay_activity->where('store_id', $store_id);
		
		$count = $db_quickpay_activity->count();
		$page = new ecjia_merchant_page($count,10, 5);
		$data = $db_quickpay_activity
		->selectRaw('id,title,activity_type,start_time,end_time')
		->orderby('id', 'asc')
		->take(10)
		->skip($page->start_id-1)
		->get();
		$res = array();
		if (!empty($data)) {
			foreach ($data as $row) {
				$row['start_time'] = RC_Time::local_date(ecjia::config('time_format'), $row['start_time']);
				$row['end_time'] = RC_Time::local_date(ecjia::config('time_format'), $row['end_time']);
				$res[] = $row;
			}
		}
		return array('list' => $res, 'filter' => $filter, 'page' => $page->show(2), 'desc' => $page->page_desc());
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
	 * 获取其他优惠
	 */
	private function get_other_offer(){
		$offer_list = array(
			'0' 	=> '允许同时使用红包抵现',
			'1'	=> '允许同时使用积分抵现',
		);
		return $offer_list;
	}
}

//end