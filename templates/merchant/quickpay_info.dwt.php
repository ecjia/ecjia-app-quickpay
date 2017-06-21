<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia-merchant.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
    ecjia.merchant.quickpay_info.init();
</script>
<!-- {/block} -->

<!-- {block name="home-content"} -->
<style>
{literal}
.wmiddens {text-align: center;width: 11%;}
.panel-primary .panel-title{color: #fff;}
#gift-div .form-control{display: inline-block;}
ul,ol{padding:0;}
#range-div{margin-top:0;}
table{border-collapse: separate; border-spacing: 0 3px;}
{/literal}
</style>
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">
        <!-- {if $ur_here}{$ur_here}{/if} -->
        {if $action_link}
        <a class="btn btn-primary data-pjax" href="{$action_link.href}" id="sticky_a" style="float:right;margin-top:-3px;"><i class="fa fa-reply"></i> {$action_link.text}</a>
        {/if}
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tab-content">
            <div class="panel">
                <div class="panel-body">
                    <div class="form">
                        <form id="form-privilege" class="form-horizontal" name="theForm" action="{$form_action}" method="post" >
                            <fieldset>
                                <div class="form-group">
                                    <label class="control-label col-lg-2">闪惠名称：</label>
                                    <div class="controls col-lg-6">
                                        <input class="form-control" type="text" name="title" id="title" value="{$data.title}" />
                                    </div>
                                    <span class="input-must">{lang key='system::system.require_field'}</span>
                                </div>
                                
                               	<div class="form-group">
									<label class="control-label col-lg-2">闪惠描述：</label>
									<div class="controls col-lg-6">
										<textarea class="form-control" name="description" id="description" >{$data.description}</textarea>
									</div>
								</div>
                                
                                <div class="form-group">
                                    <label class="control-label col-lg-2">闪惠类型：</label>
                                    <div class="controls col-lg-6">
                                    	<select name='activity_type' id="activity_type" class="form-control">
											<!-- {foreach from=$type_list item=list key=key} -->
											<option value="{$key}" {if $key eq $data.activity_type}selected="selected"{/if}>{$list}</option>
											<!-- {/foreach} -->
										</select>
                                    </div>
                                </div>
                                
                                <div id="activity_type_discount" style="display:none">
									<div class="form-group">
	                                    <label class="control-label col-lg-2">折扣价：</label>
	                                    <div class="controls col-lg-6">
	                                        <input class="form-control" type="text" name="discount_value" value="" />
	                                    </div>
	                                    <span class="input-must">{lang key='system::system.require_field'}</span>
	                                </div>
								</div>
								
								<div id="activity_type_reduced" style="display:none">
									<div class="form-group">
	                                    <label class="control-label col-lg-2">满多少：</label>
	                                    <div class="controls col-lg-6">
	                                        <div class="controls-split">
	                                            <div class="ecjiaf-fl wright_wleft">
	                                                <input name="reduced_value[]" class="form-control  w200" type="text" placeholder="消费达到的金额" value=""/>
	                                            </div>
	                                            
	                                            <div class="wmiddens ecjiaf-fl p_t5">减</div>
	                                            
	                                            <div class="ecjiaf-fl wright_wleft">
	                                                <input name="reduced_value[]" class="form-control  w200" type="text" placeholder="优惠金额" value=""/>
	                                            </div>
                                        	</div>
                                        	 &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span>
	                                    </div>
	                                </div>
								</div>
								
								<div id="activity_type_everyreduced" style="display:none">
									<div class="form-group">
	                                    <label class="control-label col-lg-2">每满多少：</label>
	                                    <div class="controls col-lg-6">
	                                        <div class="controls-split">
	                                            <div class="ecjiaf-fl wright_wleft">
	                                                <input name="everyreduced_value[]" class="form-control  w200" type="text" placeholder="消费达到的金额" value=""/>
	                                            </div>
	                                            
	                                            <div class="wmiddens ecjiaf-fl p_t5">减</div>
	                                            
	                                            <div class="ecjiaf-fl wright_wleft">
	                                                <input name="everyreduced_value[]" class="form-control  w200" type="text" placeholder="优惠金额" value=""/>
	                                            </div> &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span><br><br>
	                                            
	                                            <div class="ecjiaf-fl p_t5">最高减：</div>
	                                            
	                                            <div class="ecjiaf-fl wright_wleft">
	                                                <input name="everyreduced_value[]" class="form-control  w400" type="text" placeholder="优惠金额" value=""/>
	                                            </div> &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span>
                                        	</div>
	                                    </div>
	                                  </div>
								</div>
								
								
                                <div class="form-group">
                                    <label class=" control-label col-lg-2">有效时间：</label>
                                    <div class="col-lg-6">
                                        <div class="controls-split">
                                            <div class="ecjiaf-fl wright_wleft">
                                                <input name="start_time" class="form-control date w200" type="text" placeholder="请输入开始时间" value="{$data.start_time}"/>
                                            </div>
                                            <div class="wmiddens ecjiaf-fl p_t5">至</div>
                                            <div class="ecjiaf-fl wright_wleft">
                                                <input name="end_time" class="form-control date w200" type="text" placeholder="请输入结束时间" value="{$data.end_time}"/>
                                            </div>
                                            &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label col-lg-2">具体时间：</label>
                                    <div class="controls col-lg-6">
                                        <select name="limit_time_type" id="limit_time_type" class="form-control" >
											<option value='nolimit'>不限制时间</option>
											<option value='customize'>自定义时间</option>
										</select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-2">其他优惠：</label>
                                    <div class="col-lg-6 m_t5">
                                    	 <!-- {foreach from=$offer_list key=key item=val} -->
											<input type="checkbox" name="other_offer[]" value="{$key}" id="{$key}" {if in_array($key, $data.other_offer)}checked="true"{/if}/> <label for="{$key}">{$val}</label>
										 <!-- {/foreach} -->
                                    </div>
                                </div>
                                
                                <div class="form-group">
			                        <label class="control-label col-lg-2">是否开启：</label>
			                       	<div class="controls col-lg-6">
		                                <input id="open" name="enabled" value="1" type="radio" {if $data.enabled eq 1} checked="true" {/if}>
		                                <label for="open">开启</label>
		                                <input id="close" name="enabled" value="0" type="radio" {if $data.enabled eq 0} checked="true" {/if}>
		                                <label for="close">关闭</label>
		                            </div>
		                      	</div>
		                      	
                                <div class="form-group">
                                    <div class="col-lg-offset-2 col-lg-6">
                                        <button class="btn btn-info" type="submit">确定</button>
                                        <input type="hidden" name="id" value="" />
                                    </div>
                                </div>         
                            </fieldset>  
                    	</form>
                	</div>
           	 	</div>
        	</div>
    	</div>
	</div>
</div>
<!-- {/block} -->