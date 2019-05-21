<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/12
 * Time: 14:04
 */

namespace Ecjia\App\Quickpay\StoreDuplicateHandlers;

use Ecjia\App\Store\StoreDuplicate\StoreDuplicateAbstract;
use ecjia_error;
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
    protected $sort = 41;

    protected $dependents = [
        'store_bonus_duplicate',
    ];

    public function __construct($store_id, $source_store_id)
    {
        $this->name = __('优惠买单规则', 'quickpay');
        parent::__construct($store_id, $source_store_id);
    }

    /**
     * 获取源店铺数据操作对象
     */
    public function getSourceStoreDataHandler()
    {
        return RC_DB::table('quickpay_activity')->where('store_id', $this->source_store_id)->where('enabled', 1);
    }

    /**
     * 数据描述及输出显示内容
     */
    public function handlePrintData()
    {
        $count = $this->handleCount();
        $text = sprintf(__('店铺买单活动总共<span class="ecjiafc-red ecjiaf-fs3">%s</span>个', 'quickpay'), $count);

        return <<<HTML
<span class="controls-info w300">{$text}</span>
HTML;
    }

    /**
     * 统计数据条数并获取
     *
     * @return mixed
     */
    public function handleCount()
    {
        //如果已经统计过，直接返回统计过的条数
        if ($this->count) {
            return $this->count;
        }

        // 统计数据条数
        $this->count = $this->getSourceStoreDataHandler()->count();
        return $this->count;
    }

    /**
     * 执行复制操作
     *
     * @return mixed
     */
    public function handleDuplicate()
    {
        //检测当前对象是否已复制完成
        if ($this->isCheckFinished()) {
            return true;
        }

        //如果当前对象复制前仍存在依赖，则需要先复制依赖对象才能继续复制
        if (!empty($this->dependents)) { //如果设有依赖对象
            //检测依赖
            $items = $this->dependentCheck();
            if (!empty($items)) {
                return new ecjia_error('handle_duplicate_error', __('复制依赖检测失败！', 'store'), $items);
            }
        }

        //执行具体任务
        $result = $this->startDuplicateProcedure();
        if (is_ecjia_error($result)) {
            return $result;
        }

        //标记处理完成
        $this->markDuplicateFinished();

        //记录日志
        $this->handleAdminLog();

        return true;
    }

    /**
     * 店铺复制操作的具体过程
     */
    protected function startDuplicateProcedure()
    {
        try {
            $replacement_bonus_type = (new \Ecjia\App\Store\StoreDuplicate\ProgressDataStorage($this->store_id))->getDuplicateProgressData()->getReplacementDataByCode('store_bonus_duplicate');

            $this->getSourceStoreDataHandler()->chunk(50, function ($items) use ($replacement_bonus_type) {

                //构造可用于复制的数据
                foreach ($items as &$item) {
                    unset($item['id']);

                    //将源店铺ID设为新店铺的ID
                    $item['store_id'] = $this->store_id;

                    //开辟红包ID数组，用于存储替换后的红包ID
                    $bonus_type_id = [];
                    foreach (explode(',', $item['use_bonus']) as $use_type) {
                        //如果当前使用红包类型不属于close或nolimit则不作替换处理
                        if ($use_type == 'close' OR $use_type == 'nolimit1') {
                            continue;
                        }
                        //将红包ID加入数组，用于拼接
                        isset($replacement_bonus_type[$use_type]) && $bonus_type_id[] = $replacement_bonus_type[$use_type];
                    }

                    //如果红包使用类型存在id类型的值
                    if (!empty($bonus_type_id)) {
                        //设置新的use_bonus
                        $item['use_bonus'] = implode(',', $bonus_type_id);
                    }
                }

                //插入数据到新店铺
                RC_DB::table('quickpay_activity')->insert($items);
            });

            return true;
        } catch (\Royalcms\Component\Repository\Exceptions\RepositoryException $e) {
            return new ecjia_error('duplicate_data_error', $e->getMessage());
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

        ecjia_admin::admin_log($merchants_name, 'duplicate', 'store_quickpay_activity');
    }


}