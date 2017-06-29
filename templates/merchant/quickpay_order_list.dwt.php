<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!--{extends file="ecjia-merchant.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
ecjia.merchant.order_list.init();
</script>
<!-- {/block} -->

<!-- {block name="home-content"} -->
<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">
			<!-- {if $ur_here}{$ur_here}{/if} -->
			<div class="pull-right">
				{if $action_link}
		  			<a href="{$action_link.href}" class="btn btn-primary data-pjax"><i class="fa fa-search"></i> {$action_link.text}</a>
				{/if}
			</div>
		</h2>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
	    <div class="panel">
	     	<div class="panel-body panel-body-small">
        		<ul class="nav nav-pills pull-left">
        			<li class="{if $smarty.get.check_type eq ''}active{/if}"><a class="data-pjax" href='{url path="quickpay/mh_order/init" args="{if $filter.keywords}&keywords={$filter.keywords}{/if}"}'>全部 <span class="badge badge-info">{if $order_list.count.count}{$order_list.count.count}{else}0{/if}</span> </a></li>
        			<li class="{if $smarty.get.check_type eq 'check_ok'}active{/if}"><a class="data-pjax" href='{url path="quickpay/mh_order/init" args="check_type=check_ok{if $filter.keywords}&keywords={$filter.keywords}{/if}"}'>已审核<span class="badge badge-info">{if $order_list.count.check_ok}{$order_list.count.check_ok}{else}0{/if}</span> </a></li>
        			<li class="{if $smarty.get.check_type eq 'check_no'}active{/if}"><a class="data-pjax" href='{url path="quickpay/mh_order/init" args="check_type=check_no{if $filter.keywords}&keywords={$filter.keywords}{/if}"}'>未审核<span class="badge badge-info">{if $order_list.count.check_no}{$order_list.count.check_no}{else}0{/if}</span> </a></li>
        		</ul>
            </div>
            
			<div class='col-lg-12 panel-heading form-inline'>
        		<div class="btn-group form-group">
        			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cogs"></i> 批量操作 <span class="caret"></span></button>
        			<ul class="dropdown-menu operate_note" data-url='{url path="orders/merchant/operate_note"}'>
        				<li><a class="batch-del-btn" data-toggle="ecjiabatch" data-name="order_id" data-idClass=".checkbox:checked" data-url="{$form_action}&operation=remove" data-msg="{lang key='orders::order.remove_confirm'}" data-noSelectMsg="{lang key='orders::order.pls_select_order'}" href="javascript:;"><i class="fa fa-trash-o"></i> {lang key='system::system.remove'}</a></li>
                   	</ul>
        		</div>
        		
        		<div class="form-group">
        			<select class="w200" name='activity_type'>
						<option value="0">{t}闪惠类型{/t}</option>
						<!-- {foreach from=$type_list item=list key=key} -->
						<option value="{$key}" {if $key eq $smarty.get.activity_type}selected="selected"{/if}>{$list}</option>
						<!-- {/foreach} -->
					</select>
        		</div>
        		<button class="btn btn-primary screen-btn" type="button"><i class="fa fa-search"></i> 筛选</button>
        		
        		<form class="form-inline pull-right" name="searchForm" method="post" action="{$search_action}">
					<div class="form-group">
						<!-- 关键字 -->
						<input type="text" class="form-control" name="keywords" value="{$smarty.get.keywords}" placeholder="请输入订单号或者购买者名称"/> 
						<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</form>
			</div>

			<div class="panel-body panel-body-small">
				<section class="panel">
					 <table class="table table-striped table-hide-edit">
				        <thead>
        					<tr>
        						<th class="table_checkbox check-list w30">
        							<div class="check-item">
        								<input id="checkall" type="checkbox" name="select_rows" data-toggle="selectall" data-children=".checkbox"/>
        								<label for="checkall"></label>
        							</div>
						        </th>
        						<th class="w150">订单号</th>
        						<th>购买者信息</th>
        						<th class="w150">闪惠类型</th>
        						<th class="w200">买单时间</th>
        						<th class="w100">消费金额</th>
        						<th class="w100">实付金额</th>
        					</tr>
				        </thead>
				        <tbody>
					    <!-- {foreach from=$order_list.list item=order} -->
    					<tr>
    						<td class="check-list">
    							<div class="check-item">
    								<input id="check_{$order.order_id}" class="checkbox" type="checkbox" name="checkboxes[]" value="{$order.order_id}"/>
    								<label for="check_{$order.order_id}"></label>
    							</div>
				            </td>	
    						<td class="hide-edit-area">
    							{$order.order_sn}
    							<div class="edit-list">
    								<a href='{url path="quickpay/mh_order/info" args="order_id={$order.order_id}"}' class="data-pjax" title="查看详情">{t}查看详情{/t}</a>&nbsp;|&nbsp;
    							</div>
    						</td>
    						<td align="left">
    							{$order.user_name} [TEL：{$order.user_mobile}]
    						</td>
    						<td>
    							{if $order.activity_type eq 'normal'}无优惠{elseif $order.activity_type eq 'discount'}价格折扣{elseif $order.activity_type eq 'everyreduced'}每满多少减多少，最高减多少{else $order.activity_type eq 'reduced'}满多少减多少{/if}
    						</td>
    						<td>{$order.add_time}</td>
    						<td>{$order.goods_amount}</td>
    						<td>{$order.order_amount}</td>
    					</tr>
    					<!-- {foreachelse}-->
    					<tr><td class="no-records" colspan="7">{lang key='system::system.no_records'}</td></tr>
    					<!-- {/foreach} -->
				        </tbody>
			         </table>
					<br/>
					<a href="{$action_link.href}" class="btn btn-primary data-pjax"><i class="fa fa-plus"></i> {$action_link.text}</a>
					<br/><br/>
				</section>
				<!-- {$order_list.page} -->
			</div>
	     </div>
     </div>
</div>

<form action="{$form_action}" name="orderpostForm" id="listForm" data-pjax-url="{$search_action}" method="post"></form>
<!-- {/block} -->