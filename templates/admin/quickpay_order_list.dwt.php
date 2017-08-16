<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.order_list.init();
</script>
<!-- {/block} -->

<!-- {block name="main_content"} -->
<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		<!-- {if $action_link} -->
		<a class="btn plus_or_reply data-pjax" href="{$action_link.href}"><i class=" fontello-icon-search"></i>{$action_link.text}</a>
		<!-- {/if} -->
	</h3>
</div>

<ul class="nav nav-pills">
	<li class="{if $filter.type eq ''}active{/if}">
		<a class="data-pjax" href='{url path="orders/admin/init" args="{if $filter.composite_status !== '' && $filter.composite_status != -1}&composite_status={$filter.composite_status}{/if}{if $filter.keywords}&keywords={$filter.keywords}{/if}{if $filter.merchant_keywords}&merchant_keywords={$filter.merchant_keywords}{/if}"}'>全部
			<span class="badge badge-info">{if $count.count}{$count.count}{else}0{/if}</span> 
		</a>
	</li>
	<li class="">
		<a class="data-pjax" href='{url path="orders/admin/init" args="{if $filter.composite_status !== '' && $filter.composite_status != -1}&composite_status={$filter.composite_status}{/if}{if $filter.keywords}&keywords={$filter.keywords}{/if}{if $filter.merchant_keywords}&merchant_keywords={$filter.merchant_keywords}{/if}"}'>已核实
			<span class="badge badge-info">{if $count.count}{$count.count}{else}0{/if}</span> 
		</a>
	</li>
	<li class="{if $filter.type eq 'self'}active{/if}">
		<a class="data-pjax" href='{url path="orders/admin/init" args="type=self{if $filter.composite_status !== '' && $filter.composite_status != -1}&composite_status={$filter.composite_status}{/if}{if $filter.keywords}&keywords={$filter.keywords}{/if}{if $filter.merchant_keywords}&merchant_keywords={$filter.merchant_keywords}{/if}"}'>待核实
			<span class="badge badge-info">{if $count.self}{$count.self}{else}0{/if}</span> 
		</a>
	</li>
</ul>

<div class="row-fluid batch" >
	<form action="{$search_action}{if $filter.type}&type={$filter.type}{/if}" name="searchForm" method="post" >
		<div class="btn-group f_l m_r5">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fontello-icon-cog"></i>批量操作<span class="caret"></span>
			</a>
			<ul class="dropdown-menu operate_note" data-url='{url path="orders/admin/operate_note"}'>
				<li><a class="batch-del-btn" data-toggle="ecjiabatch" data-name="order_id" data-idClass=".checkbox:checked" data-url="{$form_action}&operation=confirm" data-msg="{lang key='orders::order.confirm_approval_order'}" data-noSelectMsg="{lang key='orders::order.pls_select_order'}" href="javascript:;"><i class="fontello-icon-ok"></i>{lang key='orders::order.op_confirm'}</a></li>
				<li><a class="batch-operate batch-operate-invalid" data-operatetype="invalid" data-url="{$form_action}&operation=invalid" data-invalid-msg="{lang key='orders::order.confirm_order_invalid'}" href="javascript:;"><i class="fontello-icon-block"></i>{lang key='orders::order.op_invalid'}</a></li>
				<li><a class="batch-operate batch-operate-cancel" data-operatetype="cancel" data-url="{$form_action}&operation=cancel" data-cancel-msg="{lang key='orders::order.confirm_order_cancel'}" href="javascript:;"><i class="fontello-icon-cancel"></i>{lang key='orders::order.op_cancel'}</a></li>
				<li><a class="batch-del-btn" data-toggle="ecjiabatch" data-name="order_id" data-idClass=".checkbox:checked" data-url="{$form_action}&operation=remove" data-msg="{lang key='orders::order.remove_confirm'}" href="javascript:;"><i class="fontello-icon-trash"></i>{lang key='system::system.remove'}</a></li>
				<li><a class="batch-print" data-url="{$form_action}&print=1" href="javascript:;"><i class="fontello-icon-print"></i>{lang key='orders::order.print_order'}</a></li>
			</ul>
			<input name="batch" type="hidden" value="1" />
		</div>
		<!-- 订单状态-->
		<select class="down-menu w120" name="status" id="select-rank">
			<option value="-1">{lang key='orders::order.all_status'}</option>
			<!-- {html_options options=$status_list selected=$order_list.filter.composite_status } -->
		</select>
		<a class="btn m_l5 screen-btn">筛选</a>
		<div class="choose_list f_r" >
			<input type="text" name="merchant_keywords" value="{$order_list.filter.merchant_keywords}" placeholder="{lang key='orders::order.enter_merchant_keywords'}"/> 
			<input type="text" name="keywords" value="{$order_list.filter.keywords}" placeholder="{lang key='orders::order.pls_consignee'}"/> 
			<button class="btn" type="submit">搜索</button>
		</div>
	</form>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid">
			<table class="table table-striped table-hide-edit">
				<thead>
					<tr>
						<th class="table_checkbox"><input type="checkbox" data-toggle="selectall" data-children=".checkbox" /></th>
						<th class="w100">订单号</th>
						<th class="w120">商家名称</th>
						<th>购买者信息</th>
						<th class="w150">闪惠类型</th>
						<th class="w120">下单时间</th>
						<th class="w100">消费金额</th>
						<th class="w100">实付金额</th>
					</tr>
				</thead>
				<tbody>
					<!-- {foreach from=$order_list.list item=order key=okey} -->
					<tr>
						<td><input type="checkbox" class="checkbox" name="order_id[]"  value="{$order.order_id}" /></td>
						<td class="hide-edit-area">
							{$order.order_sn}
							<div class="edit-list"><a href='{url path="quickpay/admin_order/info" args="order_id={$order.order_id}"}' class="data-pjax" title="查看详情">查看详情</a></div>
						</td>
						<td class="ecjiafc-red">
							{$order.merchants_name}
						</td>
						<td>{$order.user_name} [TEL：{$order.user_mobile}]</td>
						<td>{if $order.activity_type eq 'normal'}无优惠{elseif $order.activity_type eq 'discount'}价格折扣{elseif $order.activity_type eq 'everyreduced'}每满多少减多少，最高减多少{else $order.activity_type eq 'reduced'}满多少减多少{/if}</td>
						<td>{$order.add_time}</td>
						<td>{$order.goods_amount}</td>
						<td>{$order.order_amount}</td>
					</tr>
					<!-- {foreachelse}-->
					<tr><td class="no-records" colspan="8">{lang key='system::system.no_records'}</td></tr>
					<!-- {/foreach} -->
				</tbody>
			</table>
			<!-- {$order_list.page} -->	
		</div>
	</div>
</div>
<!-- {/block} -->