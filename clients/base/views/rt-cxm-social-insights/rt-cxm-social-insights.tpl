<div id="not-valid">
</div>
{section name=data loop=$collection}
		<div id="{$active_users[user].id}" class="ui-state-default" onclick="enable_one(this)">
			{$collection[data].dataFetched}
		</div>
{/section}
{if collection.dataFetched}
    {foreach meta.panels}
        <table class="table table-striped dataTable rt_cxm_social_table">
            <thead>
                <tr>
                    {foreach fields}
                        <th class="rt_cxm_social {isSortable ../../module this}sorting sorting_{eq ../../../collection.orderBy.field name}{../../../../orderBy.direction}{/eq} orderBy{name}{/isSortable}"{if name} data-fieldname="{name}"{/if} data-orderby="{orderBy}">
                            <span>{str label ../../module}</span>
                        </th>
                    {/each}
                </tr>
            </thead>
            <tbody>
                {foreach ../collection.models}
                    <tr class="single rt_cxm_social">
                        {foreach ../fields}
                            <td class="rt_cxm_social {this.name}">{field ../../../this model=../this template=../../../this.viewName}</td>
                        {/each}
                    </tr>
                {/each}
            </tbody>
        </table>
    {/each}
    {unless collection.length}
        <div class="block-footer">
            {str "LBL_NO_DATA_AVAILABLE"}
        </div>
    {/unless}
{/if}


{if this.row_elem}
	<div id="rt-data-div" class="list-tabs">
		<div class="dashlet-tabs tab3">
		<div class="dashlet-tabs-row">
			<div class="dashlet-tab ui-social-item active" name="cxm-general" id="cxm-1" data-action="change_tab">
				<a data-toggle="tab" data-action="tab-switcher" data-index="0" tabindex="-1">
				<i class="fa fa-home"></i>{LBL_HOME}
				</a>
			</div>
			<div class="dashlet-tab ui-social-item" name="cxm-twitter" id="cxm-2" data-action="change_tab">
				<a data-toggle="tab" data-action="tab-switcher" data-index="1" tabindex="-1">
				<i class="fa fa-twitter-square"></i>{LBL_TWITTER}
				</a>
			</div>
			<div class="dashlet-tab ui-social-item" name="cxm-facebook" id="cxm-3" data-action="change_tab">
				<a data-toggle="tab" data-action="tab-switcher" data-index="2" tabindex="-1">
				<i class="fa fa-facebook-square"></i>{LBL_FACEBOOK}
				</a>
			</div>
			<div class="dashlet-tab ui-social-item" name="cxm-google" id="cxm-4" data-action="change_tab">
				<a data-toggle="tab" data-action="tab-switcher" data-index="2" tabindex="-1">
				<i class="fa fa-google-plus-square"></i>{LBL_GOOGLE_PLUS}
				</a>
			</div>
			<div class="dashlet-tab ui-social-item" name="cxm-linkedin" id="cxm-5" data-action="change_tab">
				<a data-toggle="tab" data-action="tab-switcher" data-index="2" tabindex="-1">
				<i class="fa fa-linkedin-square"></i>{LBL_LINKEDIN}
				</a>
			</div>
			<div class="dashlet-tab ui-social-item" name="cxm-cform" data-action="cxm_auto_forms">
				<a data-toggle="tab" data-action="tab-switcher" data-index="2" tabindex="-1">
				<i class="fa fa-table"></i>{LBL_FORM}
				</a>
			</div>
			<span class="back-arrow" id="go-back" data-action="reset_main">
				<a class="arrow-color"><i class="fa fa-arrow-circle-left"></i></a>
			</span>
		</div>
		</div>
		<div class="tab-content pops">
			<div class="tab-pane active">
				<div class="content-box" id="social-stats" >
					{this.social_status}
				</div>
			</div>
		</div>
	</div>
{/if}

<div class="modal1">
	<div class="spinner">
	  <div class="dot1"></div>
	  <div class="dot2"></div>
	</div>
</div>
