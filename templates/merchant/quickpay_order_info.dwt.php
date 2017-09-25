<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!--{extends file="ecjia-merchant.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
ecjia.merchant.order_info.init();
</script>
<!-- {/block} -->

<!-- {block name="home-content"} -->

<!-- #BeginLibraryItem "/library/quickpay_order_step.lbi" --><!-- #EndLibraryItem -->

<div class="page-header">
	<div class="pull-left">
		<h2><!-- {if $ur_here}{$ur_here}{/if} --></h2>
  	</div>
  	<div class="pull-right">
  		{if $action_link}
		<a href="{$action_link.href}" class="btn btn-primary data-pjax">
			<i class="fa fa-reply"></i> {$action_link.text}
		</a>
		{/if}
  	</div>
  	<div class="clearfix"></div>
</div>

<div class="row-fluid">
	<div class="span12">
		<div id="accordion2" class="panel panel-default">
			<div class="panel-heading">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                    <h4 class="panel-title"><strong>基本信息</strong></h4>
                </a>
            </div>
			<div class="accordion-body in collapse" id="collapseOne">
				<table class="table table-oddtd m_b0">
					<tbody class="first-td-no-leftbd">
						<tr>
							<td><div align="right"><strong>订单编号：</strong></div></td>
							<td>
								{$order_info.order_sn}
							</td>
							<td><div align="right"><strong>订单状态：</strong></div></td>
							<td>
							{if $order_status eq 'UNCONFIRMED'}
								未确认
							{elseif $order_status eq 'CONFIRMED'}
								已确认
							{elseif $order_status eq 'UNPAYED' }
								未支付
							{elseif $order_status eq 'PAYED' }
								已支付
							{elseif $order_status eq 'UNCHECKED' }
								未核实
							{elseif $order_status eq 'CHECKED' }
								已核实
							{else}
								无效
							{/if}
							</td>
						</tr>
						<tr>
							<td><div align="right"><strong>购买人姓名：</strong></div></td>
							<td>
								{$order_info.user_name}
							</td>
							<td><div align="right"><strong>购买人手机号：</strong></div></td>
							<td>{$order_info.user_mobile}</td>
						</tr>
						<tr>
							<td><div align="right"><strong>支付方式：</strong></div></td>
							<td>
								{$order_info.pay_name}
							</td>
							<td><div align="right"><strong>支付时间：</strong></div></td>
							<td>{$order_info.pay_time}</td>
						</tr>
						
						<tr>
							<td><div align="right"><strong>闪惠类型：</strong></div></td>
							<td>
								{$order_info.activity_name}
							</td>
							<td><div align="right"><strong>买单来源：</strong></div></td>
							<td>{$order_info.referer}</td>
						</tr>
						
						<tr>
							<td><div align="right"><strong>下单时间：</strong></div></td>
							<td colspan="3">{$order_info.add_time}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="accordion-group panel panel-default">
			<div class="panel-heading accordion-group-heading-relative">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                    <h4 class="panel-title">
                        <strong>费用信息</strong>
                    </h4>
                </a>
            </div>
            <div class="accordion-body in collapse " id="collapseSix">
            	<table class="table m_b0">
					<tr>
						<td>
							<div align="right">
								<strong>买单消费总金额：</strong>¥{if $order_info.goods_amount}{$order_info.goods_amount}{else}0{/if}
								- <strong>闪惠：</strong>¥{if $order_info.discount}{$order_info.discount}{else}0{/if}
								- <strong>使用积分抵扣：</strong>¥{if $order_info.integral_money}{$order_info.integral_money}{else}0{/if}
								- <strong>使用红包抵扣：</strong>¥{if $order_info.bonus}{$order_info.bonus}{else}0{/if}
							</div>
						</td>
					</tr>
					<tr>
						<td><div align="right"> = <strong>买单实付金额：</strong>{$order_info.order_amount}</div></td>
					</tr>
				</table>
            </div>
		</div>
		<div class="accordion-group panel panel-default">
			<div class="panel-heading">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">
                    <h4 class="panel-title">
                        <strong>操作记录</strong>
                    </h4>
                </a>
            </div>
            <div class="accordion-body in collapse" id="collapseSeven">
            	<table class="table table-striped m_b0">
					<thead>
						<tr>
							<th class="w150"><strong>操作者</strong></th>
							<th class="w180"><strong>操作时间</strong></th>
							<th class="w130"><strong>订单状态</strong></th>
							<th class="ecjiafc-pre t_c"><strong>操作备注</strong></th>
						</tr>
					</thead>
					<tbody>
						<!-- {foreach from=$action_list item=action} -->
						<tr>
							<td>{$action.action_user_name}</td>
							<td>{$action.add_time}</td>
							<td>
								{if $action.order_status_name eq 'UNCONFIRMED'}
									未确认
								{elseif $action.order_status_name eq 'CONFIRMED'}
									已确认
								{elseif $action.order_status_name eq 'UNPAYED' }
									未支付
								{elseif $action.order_status_name eq 'PAYED' }
									已支付
								{elseif $action.order_status_name eq 'UNCHECKED' }
									未核实
								{elseif $action.order_status_name eq 'CHECKED' }
									已核实
								{else}
									无效
								{/if}
							</td>
							<td class="t_c">{$action.action_note|nl2br}</td>
						</tr>
						<!-- {foreachelse} -->
						<tr>
							<td class="no-records w200" colspan="4">{t}该订单暂无操作记录{/t}</td>
						</tr>
						<!-- {/foreach} -->
					</tbody>
				</table>
            </div>
		</div>
		<div class="accordion-group panel panel-default">
			<div class="panel-heading">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight">
                    <h4 class="panel-title">
                        <strong>{t}订单操作{/t}</strong>
                    </h4>
                </a>
            </div>
            <div class="accordion-body in collapse " id="collapseEight">
            	<table class="table table-oddtd m_b0">
					<tbody class="first-td-no-leftbd">
						<tr>
							<td width="15%"><div align="right"><span class="input-must">*</span> <strong>{lang key='orders::order.label_action_note'}</strong></div></td>
							<td colspan="3"><textarea name="action_note" class="span12 action_note form-control" cols="60" rows="3"></textarea></td>
						</tr>
						<tr>
							<td><div align="right"><strong>当前可执行操作：</strong></div></td>
							<td colspan="3">
								<a class="change_status" data-href='{url path="quickpay/mh_order/order_action_pass"}'>
									<button class="btn operatesubmit btn-info" type="button" >确认核实</button>
								</a>
								
								<a class="change_status" data-href='{url path="quickpay/mh_order/order_action_invalid"}'>
									<button class="btn operatesubmit btn-info" type="button" >无效</button>
								</a>
								<input type="hidden" value="{$order_info.order_id}" name="order_id">
								<input type="hidden" value="type_info" name="type_info">
							</td>
						</tr>
					</tbody>
				</table>
            </div>
		</div>
	</div>
</div>
<!-- {/block} -->