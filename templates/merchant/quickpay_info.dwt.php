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
.wmiddens {text-align: center;width: 8%;}
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
                        	<!-- 左边 -->
	                        <div class="col-lg-7" style="padding-left:0px;">
	                            <fieldset>
	                                <div class="form-group">
	                                    <label class="control-label col-lg-2">闪惠名称：</label>
	                                    <div class="controls col-lg-9">
	                                        <input class="form-control" type="text" name="title" id="title" value="{$data.title}" />
	                                    </div>
	                                    <span class="input-must">{lang key='system::system.require_field'}</span>
	                                </div>
	                                
	                               	<div class="form-group">
										<label class="control-label col-lg-2">闪惠描述：</label>
										<div class="controls col-lg-9">
											<textarea class="form-control" name="description" id="description" >{$data.description}</textarea>
										</div>
									</div>
	                                
	                                <div class="form-group">
	                                    <label class="control-label col-lg-2">闪惠类型：</label>
	                                    <div class="controls col-lg-9">
	                                    	<select name='activity_type' id="activity_type" class="form-control">
												<!-- {foreach from=$type_list item=list key=key} -->
												<option value="{$key}" {if $key eq $data.activity_type}selected="selected"{/if}>{$list}</option>
												<!-- {/foreach} -->
											</select>
	                                    </div>
	                                </div>
	                                
	                                <div id="activity_type_discount" {if $data.activity_type neq 'discount'}style="display:none"{/if}>
										<div class="form-group">
		                                    <label class="control-label col-lg-2">折扣价：</label>
		                                    <div class="controls col-lg-9">
		                                        <input class="form-control" type="text" name="activity_value" value="{$data.activity_value}" {if $data.activity_type neq 'discount'}disabled="disabled"{/if}/>
		                                    </div>
		                                    <span class="input-must">{lang key='system::system.require_field'}</span>
		                                </div>
									</div>
									
									<div id="activity_type_reduced" {if $data.activity_type neq 'reduced'}style="display:none"{/if}>
										<div class="form-group">
		                                    <label class="control-label col-lg-2">满金额：</label>
		                                    <div class="controls col-lg-9">
		                                        <div class="controls-split">
		                                            <div class="ecjiaf-fl wright_wleft">
		                                                <input name="activity_value[]" class="form-control  w200" type="text" placeholder="消费达到的金额" value="{$data.activity_value.0}" {if $data.activity_type neq 'reduced'}disabled="disabled"{/if} />
		                                            </div>
		                                            
		                                            <div class="wmiddens ecjiaf-fl p_t5">减</div>
		                                            
		                                            <div class="ecjiaf-fl wright_wleft">
		                                                <input name="activity_value[]" class="form-control  w200" type="text" placeholder="优惠金额" value="{$data.activity_value.1}" {if $data.activity_type neq 'reduced'}disabled="disabled"{/if}  />
		                                            </div>
	                                        	</div>
	                                        	 &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span>
		                                    </div>
		                                </div>
									</div>
									
									<div id="activity_type_everyreduced" {if $data.activity_type neq 'everyreduced'}style="display:none"{/if}>
										<div class="form-group">
		                                    <label class="control-label col-lg-2">每满金额：</label>
		                                    <div class="controls col-lg-9">
		                                        <div class="controls-split">
		                                            <div class="ecjiaf-fl wright_wleft">
		                                                <input name="activity_value[]" class="form-control  w200" type="text" placeholder="消费达到的金额" value="{$data.activity_value.0}" {if $data.activity_type neq 'everyreduced'}disabled="disabled"{/if} />
		                                            </div>
		                                            
		                                            <div class="wmiddens ecjiaf-fl p_t5">减</div>
		                                            
		                                            <div class="ecjiaf-fl wright_wleft">
		                                                <input name="activity_value[]" class="form-control  w200" type="text" placeholder="优惠金额" value="{$data.activity_value.1}" {if $data.activity_type neq 'everyreduced'}disabled="disabled"{/if} />
		                                            </div> &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span><br><br>
		                                            
		                                            <div class="ecjiaf-fl p_t5">最高减：</div>
		                                            
		                                            <div class="ecjiaf-fl wright_wleft">
		                                                <input name="activity_value[]" class="form-control" style="width: 380px;" type="text" placeholder="优惠金额" value="{$data.activity_value.2}" {if !$data.activity_value.2}disabled="disabled"{/if} />
		                                            </div> &nbsp;<span class="input-must">{lang key='system::system.require_field'}</span>
	                                        	</div>
		                                    </div>
		                                  </div>
									</div>
									
	                                <div class="form-group">
	                                    <label class="control-label col-lg-2">时间规则：</label>
	                                    <div class="controls col-lg-9">
	                                        <select name="limit_time_type" id="limit_time_type" class="form-control" >
												<option value='nolimit'>不限制时间</option>
												<option value='customize'>自定义时间</option>
											</select>
	                                    </div>
	                                </div>
	                                
	                                <div class="form-group">
	                                    <label class=" control-label col-lg-2">有效时间：</label>
	                                    <div class="col-lg-9">
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
				                        <label class="control-label col-lg-2">是否开启：</label>
				                       	<div class="controls col-lg-9">
			                                <input id="open" name="enabled" value="1" type="radio" {if $data.enabled eq 1} checked="true" {/if}>
			                                <label for="open">开启</label>
			                                <input id="close" name="enabled" value="0" type="radio" {if $data.enabled eq 0} checked="true" {/if}>
			                                <label for="close">关闭</label>
			                            </div>
			                      	</div>
			                      	
	                                <div class="form-group">
	                                    <div class="col-lg-offset-2 col-lg-6">
	                                        <button class="btn btn-info" type="submit">确定</button>
	                                        <input type="hidden" name="id" value="{$data.id}" />
	                                    </div>
	                                </div>         
	                            </fieldset>  
	                        </div>
	                        
	                        
	                        
	                        
	                        
                            <!-- 右边 -->
							<div class="col-lg-5">
                                <fieldset>
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#telescopic1" class="accordion-toggle">
                                            	<span class="glyphicon"></span>
                                                <h4 class="panel-title">红包优惠</h4>
                                            </a>
                                        </div>
                                        
                                        <div id="telescopic1" class="panel-collapse collapse in">
	                                         <div class="panel-body">
	                                            <div class="form-group">
	                                            	<label class="control-label" style="float: left; padding-left: 15px;">是否允许同时使用红包抵现：</label>
							                       	<div>
						                                <input id="open_bonuns" name="use_bonus_enabled" value="open" type="radio" {if $data.use_bonus neq 'close'} checked="true" {/if}>
						                                <label for="open_bonuns">开启</label>
						                                <input id="close_bonus" name="use_bonus_enabled" value="close" type="radio" {if $data.use_bonus eq 'close'} checked="true" {/if}>
						                                <label for="close_bonus">关闭</label>
						                            </div>
						                      	</div>
			                      	
                                                <div class="form-group">
                                                    <div class="col-lg-8">
                                                        <select class="form-control" id="use_bonus_select" name="use_bonus_select" {if $data.use_bonus eq 'close'}disabled="disabled"{/if}>
                                                            <option value="nolimit" {if $data.use_bonus eq 'nolimit'}selected{/if}>全部红包</option>
                                                            <option value="bonus_id" {if $act_range_ext}selected{/if}>指定红包</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group" id="range_search" >
                                                    <div class="col-lg-8">
                                                        <input name="keyword" class="form-control" type="text" id="keyword" placeholder="请输入关键词进行搜索" {if $data.use_bonus eq 'close'}disabled="disabled"{/if} />
                                                    </div>
                                                    <button class="btn btn-primary" type="button" id="search" data-url='{url path="quickpay/merchant/search"}' {if $data.use_bonus eq 'close'}disabled="disabled"{/if}><i class='fa fa-search'></i> {lang key='system::system.button_search'}</button>
                                                </div>
                                                
                                                <span class="help-block">
                                                	当红包优惠设为“指定红包”时，需要输入关键词搜索并且设置指定红包，
													如果优惠设为“不指定红包”则不需要设置。
                                                </span>
                                                
                                                <ul id="range-div" {if $act_range_ext}style="display:block;"{/if}>
                                                    <!-- {foreach from=$act_range_ext item=item} -->
                                                    <li>
                                                        <input name="act_range_ext[]" type="hidden" value="{$item.type_id}" />
                                                        {$item.type_name}
                                                        <a href="javascript:;" class="delact"><i class="fa fa-minus-circle ecjiafc-red"></i></a>
                                                    </li>
                                                    <!-- {/foreach} -->
                                                </ul>

                                                <div class="form-group" id="selectbig" style="display:none">
                                                    <div class="col-lg-10">
                                                        <select name="result" id="result" class="noselect form-control" size="10">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                    	</form>
                	</div>
           	 	</div>
        	</div>
    	</div>
	</div>
</div>
<!-- {/block} -->