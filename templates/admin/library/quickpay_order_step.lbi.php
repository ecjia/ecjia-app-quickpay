<?php defined('IN_ECJIA') or exit('No permission resources.');?> 

<div class="quickpay-time-base m_b20">
	{if $order_status eq 'UNCONFIRMED' or $order_status eq 'CONFIRMED' or $order_status eq 'UNPAYED'}
	<ul>
		<li class="step-first">
			<div class="step-cur">
				<div class="step-no">1</div>
				<div class="m_t5">提交订单</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.add_time}</div>
			</div>
		</li>
		<li>
			<div class="step-cut">
				<div class="step-no">2</div>
				<div class="m_t5">待付款</div>
			</div>
		</li>

		<li class="step-last">
			<div class="step-done">
				<div class="step-no">3</div>
				<div class="m_t5">待核实</div>
			</div>
		</li>
	</ul>
	{elseif $order_status eq 'PAYED' or $order_status eq 'UNCHECKED'}
	<ul>
		<li class="step-first">
			<div class="step-done">
				<div class="step-no"></div>
				<div class="m_t5">提交订单</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.add_time}</div>
			</div>
		</li>
		<li>
			<div class="step-cur">
				<div class="step-no">2</div>
				<div class="m_t5">已付款</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.pay_time}</div>
			</div>
		</li>

		<li class="step-last">
			<div class="step-done">
				<div class="step-no">3</div>
				<div class="m_t5">待核实</div>
			</div>
		</li>
	</ul>
	{elseif $order_status eq 'CHECKED'}
	<ul>
		<li class="step-first">
			<div class="step-done">
				<div class="step-no"></div>
				<div class="m_t5">提交订单</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.add_time}</div>
			</div>
		</li>
		<li>
			<div class="step-done">
				<div class="step-no"></div>
				<div class="m_t5">已付款</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.pay_time}</div>
			</div>
		</li>

		<li class="step-last">
			<div class="step-cur">
				<div class="step-no">3</div>
				<div class="m_t5">已核实</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.verification_time}</div>
			</div>
		</li>
	</ul>
	{else}
	<ul>
		<li class="step-first">
			<div class="step-done">
				<div class="step-no"></div>
				<div class="m_t5">提交订单</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.add_time}</div>
			</div>
		</li>
		<li>
			<div class="step-done">
				<div class="step-no"></div>
				<div class="m_t5">已付款</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.pay_time}</div>
			</div>
		</li>

		<li class="step-last">
			<div class="step-cur">
				<div class="step-failed">3</div>
				<div class="m_t5">核实失败</div>
				<div class="m_t5 ecjiafc-blue">{$order_info.verification_time}</div>
			</div>
		</li>
	</ul>
	{/if}
	
</div>
