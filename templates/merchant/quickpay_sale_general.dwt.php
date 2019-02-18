<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia-merchant.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
var templateCounts = '{$data}';
	ecjia.merchant.sale_general.init();
{if $page eq 'init'}
	ecjia.merchant.chart.order_count();
{else if $page eq 'sales_trends'}
	ecjia.merchant.chart.order_amount();
{/if}
</script>
<!-- {/block} -->

<!-- {block name="home-content"} -->
<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times" data-original-title="" title=""></i></button>
	<strong>温馨提示：</strong>只有已付款的订单才计入订单统计。
</div>

<div class="page-header">
	<div class="pull-left">
		<h3><!-- {if $ur_here}{$ur_here}{/if} --></h3>
	</div>
	<!-- {if $action_link} -->
	<div class="pull-right">
		<!-- {if $smarty.get.query_by_year} -->
		<a class="btn btn-primary" id="sticky_a" href="{$action_link.href}&query_by_year=1{if $smarty.get.year_beginYear}&year_beginYear={$smarty.get.year_beginYear}{/if}{if $smarty.get.year_endYear}&year_endYear={$smarty.get.year_endYear}{/if}">
		<!-- {else if $smarty.get.query_by_month} -->
		<a class="btn btn-primary" id="sticky_a" href="{$action_link.href}&query_by_month=1{if $smarty.get.month_beginYear}&month_beginYear={$smarty.get.month_beginYear}{/if}{if $smarty.get.month_beginMonth}&month_beginMonth={$smarty.get.month_beginMonth}{/if}{if $smarty.get.month_endYear}&month_endYear={$smarty.get.month_endYear}{/if}{if $smarty.get.month_endMonth}&month_endMonth={$smarty.get.month_endMonth}{/if}">
		<!-- {else} -->
		<a class="btn btn-primary" id="sticky_a" href="{$action_link.href}&start_time={$filter.start_time}&end_time={$filter.end_time}&query_type={$filter.query_type}">
		<!-- {/if} -->
		<i class="glyphicon glyphicon-download-alt"></i> {t}{$action_link.text}{/t}</a>
	</div>
	<!-- {/if} -->
	<div class="clearfix">
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
	  <div class="row state-overview">
	    	<div class="col-lg-3 col-sm-3">
                <div class="value">
                    <h3>{if $data_count.order_count}{$data_count.order_count}{else}0{/if}</h3>
                    <p>订单总数量（单）</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-3">
                <div class="value">
                    <h3>¥{if $data_count.goods_amount}{$data_count.goods_amount}{else}0{/if}</h3>
                    <p>消费总金额（元）</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-3">
                <div class="value">
                    <h3>¥{if $data_count.favorable_amount}{$data_count.favorable_amount}{else}0{/if}</h3>
                    <p>优惠总金额（元）</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-3">
                <div class="value">
                    <h3>¥{if $data_count.order_amount}{$data_count.order_amount}{else}0{/if}</h3>
                    <p>实付总金额（元）</p>
                </div>
            </div>
        </div>
                          
		<section class="panel">
			<header class="panel-heading col-lg-12">
				<div class="form-group choose_list">
					<form class="form-inline f_r" action="{$form_action}" method="post" name="searchForm">
						<span class="f_l">年走势：</span>
		                	{html_select_date prefix="year_begin" class="no_search w110" time=$filter.start_time start_year="2006" end_year="+1" display_days=false display_months=false}
						<span class="f_l">-</span>
		        			{html_select_date prefix="year_end" class="no_search w110" time=$filter.end_time start_year="2006" end_year="+1" display_days=false display_months=false}
						<input type="submit" name="query_by_year" value="查询" class="btn btn-primary screen-btn"/>
					</form>
				</div>
			</header>
			<header class="panel-heading col-lg-12">
				<div class="form-group choose_list">
					<form class="form-inline f_r" action="{$form_action}" method="post" name="selectForm">
						<span class="f_l">月走势：</span>
		            		{html_select_date prefix="month_begin" class="no_search w110" time=$filter.start_month_time start_year="2006" end_year="+1" display_days=false field_order="YMD" month_format="%m"}
						<span class="f_l">-</span>
		        			{html_select_date prefix="month_end" class="no_search w110" time=$filter.end_month_time start_year="2006" end_year="+1" display_days=false field_order="YMD" month_format="%m"}
						<input type="submit" name="query_by_month" value="查询" class="btn btn-primary screen-btn1"/>
					</form>
				</div>
			</header>
			<div class="panel-body">
				<section id="unseen">
					<ul class="nav nav-tabs">
						<li class="{if $page eq 'init'}active{/if}">
						<!-- {if $smarty.get.query_by_year} -->
						<a class="data-pjax" href='{url path="quickpay/mh_sale_general/init" args="&query_by_year=1{if $smarty.get.year_beginYear}&year_beginYear={$smarty.get.year_beginYear}{/if}{if $smarty.get.year_endYear}&year_endYear={$smarty.get.year_endYear}{/if}"}'>{t domain="quickpay"}订单走势{/t}</a>
						<!-- {else if $smarty.get.query_by_month} -->
						<a class="data-pjax" href='{url path="quickpay/mh_sale_general/init" args="&query_by_month=1{if $smarty.get.month_beginYear}&month_beginYear={$smarty.get.month_beginYear}{/if}{if $smarty.get.month_beginMonth}&month_beginMonth={$smarty.get.month_beginMonth}{/if}{if $smarty.get.month_endYear}&month_endYear={$smarty.get.month_endYear}{/if}{if $smarty.get.month_endMonth}&month_endMonth={$smarty.get.month_endMonth}{/if}"}'>{t domain="quickpay"}订单走势{/t}</a>
						<!-- {else} -->
						<a class="data-pjax" href='{url path="quickpay/mh_sale_general/init"}'>{t domain="quickpay"}订单走势{/t}</a>
						<!-- {/if} -->
						<li class="{if $page eq 'sales_trends'}active{/if}">
						<!-- {if $smarty.get.query_by_year} -->
						<a class="data-pjax" href='{url path="quickpay/mh_sale_general/sales_trends" args="&query_by_year=1{if $smarty.get.year_beginYear}&year_beginYear={$smarty.get.year_beginYear}{/if}{if $smarty.get.year_endYear}&year_endYear={$smarty.get.year_endYear}{/if}"}'>{t domain="quickpay"}销售额走势{/t}</a>
						<!-- {else if $smarty.get.query_by_month} -->
						<a class="data-pjax" href='{url path="quickpay/mh_sale_general/sales_trends" args="&query_by_month=1{if $smarty.get.month_beginYear}&month_beginYear={$smarty.get.month_beginYear}{/if}{if $smarty.get.month_beginMonth}&month_beginMonth={$smarty.get.month_beginMonth}{/if}{if $smarty.get.month_endYear}&month_endYear={$smarty.get.month_endYear}{/if}{if $smarty.get.month_endMonth}&month_endMonth={$smarty.get.month_endMonth}{/if}"}'>{t domain="quickpay"}销售额走势{/t}</a>
						<!-- {else} -->
						<a class="data-pjax" href='{url path="quickpay/mh_sale_general/sales_trends"}'>{t domain="quickpay"}销售额走势{/t}</a>
						<!-- {/if} -->
						</li>
					</ul>
					<form class="form-horizontal">
						<div class="tab-content">
		    				{if $page eq 'init'}
							<div class="tab-pane active" id="tab1">
								<div class="m_t10">
									<div id="order_count" data-url='{RC_Uri::url("quickpay/mh_sale_general/get_order_status","start_time={$filter.start_time}&end_time={$filter.end_time}&start_month_time={$filter.start_month_time}&end_month_time={$filter.end_month_time}&query_type={$filter.query_type}&order_type=1")}'>
										<div class="ajax_loading">
											<i class="fa fa-spin fa-spinner"></i>加载中...
										</div>
									</div>
								</div>
							</div>
		    				{/if}
		    				{if $page eq 'sales_trends'}
							<div class="tab-pane active" id="tab2">
								<div class="m_t10">
									<div id="order_amount" data-url='{RC_Uri::url("quickpay/mh_sale_general/get_order_status","start_time={$filter.start_time}&end_time={$filter.end_time}&start_month_time={$filter.start_month_time}&end_month_time={$filter.end_month_time}&query_type={$filter.query_type}&order_type=0")}'>
										<div class="ajax_loading">
											<i class="fa fa-spin fa-spinner"></i>加载中...
										</div>
									</div>
								</div>
							</div>
		    				{/if}
						</div>
					</form>
				</section>
			</div>
		</section>
	</div>
</div>	
<!-- {/block} -->