<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/12
 * Time: 14:04
 */

namespace Ecjia\App\Quickpay\StoreDuplicateHandlers;

use Ecjia\App\Store\StoreDuplicate\StoreDuplicateAbstract;
use RC_Uri;
use RC_DB;
use RC_Api;
use ecjia_admin;

/**
 * 复制店铺中的买单规则
 *
 * Class StoreQuickPayDuplicate
 * @package Ecjia\App\Quickpay\StoreDuplicateHandlers
 */
class StoreQuickPayDuplicate extends StoreDuplicateAbstract
{

    /**
     * 代号标识
     * @var string
     */
    protected $code = 'store_quick_pay_duplicate';

    /**
     * 排序
     * @var int
     */
    protected $sort = 8;

    public function __construct($store_id, $source_store_id)
    {
        $this->name = __('优惠买单规则', 'quickpay');

        parent::__construct($store_id, $source_store_id);
    }

    /**
     * 数据描述及输出显示内容
     */
    public function handlePrintData()
    {
        $count     = $this->handleCount();
        $text      = sprintf(__('店铺买单活动总共<span class="ecjiafc-red ecjiaf-fs3">%s</span>个', 'quickpay'), $count);

        return <<<HTML
<span class="controls-info w300">{$text}</span>
HTML;
    }

    /**
     * 获取数据统计条数
     *
     * @return mixed
     */
    public function handleCount()
    {
        $count = RC_DB::table('quickpay_activity')->where('store_id', $this->source_store_id)->count();
        return $count;
    }


    /**
     * 执行复制操作
     *
     * @return mixed
     */
    public function handleDuplicate()
    {
        $item = $this->dependentCheck();
        //判断提示错误
        if (empty($item)){
            //标记处理完成
            $this->markDuplicateFinished();
            //$this->handleAdminLog();
            return TRUE;
        }
    }

    /**
     * 返回操作日志编写
     *
     * @return mixed
     */
    public function handleAdminLog()
    {

        \Ecjia\App\Store\Helper::assign_adminlog_content();

        $store_info = RC_Api::api('store', 'store_info', array('store_id' => $this->store_id));

        $merchants_name = !empty($store_info) ? sprintf(__('店铺名是%s', 'quickpay'), $store_info['merchants_name']) : sprintf(__('店铺ID是%s', 'quickpay'), $this->store_id);

        ecjia_admin::admin_log($merchants_name, 'clean', 'store_quickpay_activity');
    }


}