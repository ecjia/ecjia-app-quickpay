<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia-merchant.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.merchant.quickpay_list.init();
</script>
<!-- {/block} -->


<!-- {block name="home-content"} -->

<div class="row">
	<div class="col-lg-12">
		<h2 class="page-header">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		<div class="pull-right">
			{if $quickpay_enabled}
				<a id="ajaxclose" data-msg ="你确定关闭闪惠吗？" data-href='{RC_Uri::url("quickpay/merchant/close")}'  class="btn btn-primary" title="关闭闪惠"><i class="fa fa-minus-square"></i> 关闭闪惠</a>
			{else}
				<a id="ajaxopen" href='{RC_Uri::url("quickpay/merchant/open")}'  class="btn btn-primary" title="开启闪惠"><i class="fa fa-check-square-o"></i> 开启闪惠</a>
			{/if}	
		</div>
		</h2>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-body panel-body-small">
				<form class="form-inline pull-left" name="siftForm" action="{$search_action}" method="post">
					<div class="screen f_l">
						<div class="form-group">
							<select class="w200" name='act_type'>
								<option value="0">{t}闪惠类型{/t}</option>
								<!-- {foreach from=$type_list item=list key=key} -->
								<option value="{$key}" {if $key eq $smarty.get.user_id}selected="selected"{/if}>{$list}</option>
								<!-- {/foreach} -->
							</select>
						</div>
						<button class="btn btn-primary screen-btn" type="submit"><i class="fa fa-search"></i>筛选</button>
					</div>
				</form>
				
				<form class="form-inline pull-right" name="searchForm" method="post" action="{$search_action}">
					<div class="form-group">
						<!-- 关键字 -->
						<input type="text" class="form-control" name="keywords" value="{$smarty.get.keywords}" placeholder="请输入闪惠名称"/> 
						<button class="btn btn-primary" type="submit">搜索</button>
					</div>
				</form>
			</div>
			<div class="panel-body panel-body-small">
				<section class="panel">
					<table class="table table-striped table-hover table-hide-edit">
						<thead>
							<tr>
								<th class="w150">闪惠标题</th>
								<th class="w100">闪惠类型</th>
								<th class="w150">开始时间</th>
								<th class="w150">结束时间</th>
								<th class="w50">状态</th>
							</tr>
						</thead>
						<tbody>
							<!-- {foreach from=$quickpay_list.list item=list} -->
							<tr>
								<td class="hide-edit-area">
		                           {$list.title}
		                           <div class="edit-list">
		                               <a class="data-pjax" href='{url path="quickpay/merchant/edit" args="id={$list.id}"}' title="编辑">编辑</a>&nbsp;|&nbsp;
		                               <a class="ajaxremove ecjiafc-red" data-toggle="ajaxremove" data-msg="你确定要删除该闪惠规则吗？" href='{url path="quickpay/merchant/remove" args="id={$list.id}"}' title="删除">删除</a>
		                           </div>
		                        </td>
								<td>{if $list.act_type eq 'normal'}无优惠{elseif $list.act_type eq 'discount'}价格折扣{elseif $list.act_type eq 'everyreduced'}每满多少减多少，最高减多少{else $list.act_type eq 'reduced'}满多少减多少{/if}</td>
								<td>{$list.start_time}</td>
								<td>{$list.end_time}</td>
								<td>{if $now lt $list.start_time}未开始{elseif $now gt $list.end_time}已结束{else}进行中{/if}</td>
							</tr>
							<!-- {foreachelse} -->
							   <tr><td class="no-records" colspan="5">{lang key='system::system.no_records'}</td></tr>
							<!-- {/foreach} -->
							
						</tbody>
						
					</table>
					<br/>
					<a href="{$action_link.href}" class="btn btn-primary data-pjax"><i class="fa fa-plus"></i> {$action_link.text}</a>
					<br/><br/>
				</section>
			<!-- {$quickpay_list.page} -->
			</div>
		</div>
	</div>
</div>
<!-- {/block} -->