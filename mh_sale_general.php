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
 * 销售概况
*/
class mh_sale_general extends ecjia_merchant {
	public function __construct() {
		parent::__construct();
		
		RC_Script::enqueue_script('bootstrap-placeholder');
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-uniform');
		RC_Style::enqueue_style('uniform-aristo');
		
		RC_Loader::load_app_func('global', 'quickpay');
        
        RC_Script::enqueue_script('acharts-min', RC_App::apps_url('statics/js/acharts-min.js', __FILE__), array('ecjia-merchant'), false, 1);
        
        RC_Script::enqueue_script('mh_sale_general', RC_App::apps_url('statics/js/mh_sale_general.js', __FILE__), array('ecjia-merchant'), false, 1);
        RC_Script::localize_script('mh_sale_general', 'js_lang', config('app-quickpay::jslang.sale_general_page'));
        
        RC_Script::enqueue_script('mh_sale_general_chart', RC_App::apps_url('statics/js/mh_sale_general_chart.js', __FILE__), array('ecjia-merchant'), false, 1);
        RC_Script::localize_script('mh_sale_general_chart', 'js_lang', config('app-quickpay::jslang.sale_general_page'));
        
        RC_Style::enqueue_style('mh_stats', RC_App::apps_url('statics/css/mh_stats.css', __FILE__));
        
        ecjia_merchant_screen::get_current_screen()->set_parentage('quickpay', 'quickpay/mh_sale_general.php');
        
        ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('买单管理', 'quickpay'), RC_Uri::url('quickpay/mh_order/init')));
       
        $data_count = RC_DB::table('quickpay_orders')
        	->select(RC_DB::raw('COUNT(DISTINCT order_sn) AS order_count'),
        		RC_DB::raw('SUM(goods_amount) AS goods_amount'),
        		RC_DB::raw('SUM(IF(pay_code = "pay_balance", surplus , order_amount)) AS order_amount'),
        		RC_DB::raw('SUM(goods_amount - (IF(pay_code = "pay_balance", surplus, order_amount))) AS favorable_amount'))
        		->where('store_id', $_SESSION['store_id'])
        		->where('pay_status',1)
        		->first();
        $data_count['order_count'] = ecjia_price_format($data_count['order_count']);
        $data_count['goods_amount'] = ecjia_price_format($data_count['goods_amount']);
        $data_count['order_amount'] = ecjia_price_format($data_count['order_amount']);
        $data_count['favorable_amount'] = ecjia_price_format($data_count['favorable_amount']);
        $this->assign('data_count', $data_count);
	}
	
	/**
	 * 显示统计信息
	 */
	public function init() {
		$this->admin_priv('mh_quickpay_sale_general_stats');

		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('买单订单统计', 'quickpay')));
		
		$this->assign('ur_here', __('订单统计', 'quickpay'));
		$this->assign('action_link', array('text' => __('买单订单统计报表下载', 'quickpay'), 'href' => RC_Uri::url('quickpay/mh_sale_general/download')));

        $this->assign('page', 'init');
        $this->assign('form_action', RC_Uri::url('quickpay/mh_sale_general/init'));
		
        $order_type = !empty($_GET['order_type']) ? intval($_GET['order_type']) : 1;
        $data = $this->get_order_status($order_type);

        $this->assign('data', $data['item']);
        $this->assign('filter', $data['filter']);

        return $this->display('quickpay_sale_general.dwt');
	}
	
	/**
	 * 显示销售额走势
	 */
	public function sales_trends() {
		$this->admin_priv('mh_quickpay_sale_general_stats');
		
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('销售概况', 'quickpay')));
	
		$this->assign('ur_here', __('销售概况', 'quickpay'));
		$this->assign('action_link',array('text' => __('销售概况报表下载', 'quickpay'),'href' => RC_Uri::url('quickpay/mh_sale_general/download')));
		
		$this->assign('page', 'sales_trends');
		$this->assign('form_action', RC_Uri::url('quickpay/mh_sale_general/sales_trends'));
		
		$order_type = !empty($_GET['order_type']) ? intval($_GET['order_type']) : 0;
		$data = $this->get_order_status($order_type);
		
        $this->assign('data', $data['item']);
        $this->assign('filter', $data['filter']);

        return $this->display('quickpay_sale_general.dwt');
	}
	
	
	/**
	 * 下载EXCEL报表
	 */
	public function download() {
		$this->admin_priv('mh_quickpay_sale_general_stats');
		
		$db_quickpay_order = RC_DB::table('quickpay_orders');
		
		//默认查询时间
		$query_type = 'month';
		$start_year = RC_Time::local_date('Y')-3;
		$end_year = RC_Time::local_date('Y');
		$start_month = '';
		$end_month = '';
		//按年查询
		if ($_GET['query_by_year']) {
			$query_type = 'year';
			$start_year = intval($_GET['year_beginYear']);
			$end_year = intval($_GET['year_endYear']);
			$start_month = '';
			$end_month = '';
			//按月查询
		} elseif ($_GET['query_by_month']) {
			$start_year = intval($_GET['month_beginYear']);
			$end_year = intval($_GET['month_endYear']);
			$start_month = intval($_GET['month_beginMonth']);
			$end_month = intval($_GET['month_endMonth']);
		}
		$start_time = getTimestamp($start_year, $start_month)['start'];
		$end_time = getTimestamp($end_year, $end_month)['end'];
		
		$format = ($query_type == 'year') ? '%Y' : '%Y-%m';
		if ($start_time < 0 || $end_time < 0) {
			return $this->showmessage(__('参数错误', 'quickpay'), ecjia::MSGTYPE_HTML | ecjia::MSGSTAT_ERROR);
		}
		
		$db_quickpay_order->where('store_id', $_SESSION['store_id']);
		$db_quickpay_order->where('pay_time', '>=', $start_time);
		$db_quickpay_order->where('pay_time', '<=', $end_time);
		$data_list = $db_quickpay_order
    		->select(RC_DB::raw("DATE_FORMAT(FROM_UNIXTIME(pay_time), '". $format ."') AS period,COUNT(DISTINCT order_sn) AS order_count, SUM(order_amount + surplus) AS order_amount"))
    		->groupby('period')
    		->get();
		
		$filetitle = __('商家买单订单统计概况报表', 'quickpay');
		$totext = __('至', 'quickpay');
		$start_time = RC_Time::local_date('Y-m-d', $start_time);
		$end_time = RC_Time::local_date('Y-m-d', $end_time);
		$filename = mb_convert_encoding($filetitle . '_' . $start_time . $totext . $end_time, "GBK", "UTF-8");
	
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		$title1 = __('商家买单订单统计', 'quickpay');
		$title2 = __('时间段', 'quickpay');
		$title3 = __('订单数（单位:个）', 'quickpay');
		$title4 = __('营业额（单位:元）', 'quickpay');
		echo mb_convert_encoding($title1,'UTF-8', 'UTF-8') . "\t\n";
		echo mb_convert_encoding($title2,'UTF-8', 'UTF-8') . "\t";
		echo mb_convert_encoding($title3,'UTF-8', 'UTF-8') . "\t";
		echo mb_convert_encoding($title4,'UTF-8', 'UTF-8') . "\t\n";
		foreach ($data_list AS $data) {
			echo mb_convert_encoding($data['period'],'UTF-8', 'UTF-8') . "\t";
			echo mb_convert_encoding($data['order_count'],'UTF-8', 'UTF-8') . "\t";
			echo mb_convert_encoding($data['order_amount'],'UTF-8', 'UTF-8') . "\t";
			echo "\n";
		}
		exit;
	}
	
	/**
	 * 获取销售概况图表数据
	 */
	private function get_order_status ($order_type) {
		$db_quickpay_order = RC_DB::table('quickpay_orders');
		
		if (empty($_GET['query_type'])) {
    		$query_type = 'month';
    		$start_year = RC_Time::local_date('Y')-3;
    		$end_year = RC_Time::local_date('Y');
    		$start_month = '';
    		$end_month = '';
    	}
    	if ($_GET['query_by_year']) {
    		$query_type = 'year';
    		$start_year = intval($_GET['year_beginYear']);
    		$end_year = intval($_GET['year_endYear']);
    		$start_month = '';
    		$end_month = '';
    	} elseif ($_GET['query_by_month']) {
    		$start_year = intval($_GET['month_beginYear']);
    		$end_year = intval($_GET['month_endYear']);
    		$start_month = intval($_GET['month_beginMonth']);
    		$end_month = intval($_GET['month_endMonth']);
    	}
    	$filter['start_time'] = $filter['start_month_time'] = getTimestamp($start_year, $start_month)['start'];
    	$filter['end_time'] = $filter['end_month_time'] = getTimestamp($end_year, $end_month)['end'];
    	
        if ($query_type == 'year') {
            /*时间参数*/
            $start_time = $filter['start_time'];
            $end_time = $filter['end_time'];
        } else {
            $start_time = $filter['start_month_time'];
            $end_time = $filter['end_month_time'];
        }
        
        
        
		$format = ($query_type == 'year') ? '%Y' : '%Y-%m';

		$db_quickpay_order->where('store_id', $_SESSION['store_id']);
		$db_quickpay_order->where('pay_time', '>=', $start_time);
		$db_quickpay_order->where('pay_time', '<=', $end_time);
		$templateCount = $db_quickpay_order
    		->select(RC_DB::raw("DATE_FORMAT(FROM_UNIXTIME(pay_time), '". $format ."') AS period,COUNT(DISTINCT order_sn) AS order_count, SUM(order_amount + surplus) AS order_amount"))
    		->groupby('period')
    		->get();
		
		if ($order_type == 1) {
		    if ($templateCount) {
		        foreach ($templateCount as $k => $v) {
		            unset($templateCount[$k]['order_amount']);
		        }
		    } else {
				$templateCount = null;
			}
			
		} else {
		    if ($templateCount) {
		        foreach ($templateCount as $k => $v) {
		            unset($templateCount[$k]['order_count']);
		        }
		    } else {
				$templateCount = null;
			}
		}
		$filter['start_time'] = RC_Time::local_date('Y-m-d', $filter['start_time']);
		$filter['end_time'] = RC_Time::local_date('Y-m-d', $filter['end_time']);
		$filter['start_month_time'] = RC_Time::local_date('Y-m-d', $filter['start_month_time']);
		$filter['end_month_time'] = RC_Time::local_date('Y-m-d', $filter['end_month_time']);
		$filter['query_type'] = $query_type;
		
		return array('item' => json_encode($templateCount), 'filter' => $filter);
	}
}

// end