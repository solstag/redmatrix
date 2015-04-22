<div class="section-title-wrapper">
	{{if $editor}}
	<div class="pull-right">
		<button id="webpage-create-btn" class="btn btn-xs btn-success" onclick="openClose('block-editor');"><i class="icon-edit"></i>&nbsp;{{$create}}</button>
	</div>
	{{/if}}
	<h2>{{$title}}</h2>
	<div class="clear"></div>
</div>
{{if $editor}}
<div id="block-editor" class="section-content-tools-wrapper">
	{{$editor}}
</div>
{{/if}}
{{if $pages}}

	   <div id="pagelist-content-wrapper" class="generic-content-wrapper">
		{{foreach $pages as $key => $items}} 
				{{foreach $items as $item}}
					<div class="page-list-item">
					{{if $edit}}<a href="{{$baseurl}}/{{$item.url}}" title="{{$edit}}"><i class="icon-pencil design-icons design-edit-icon btn btn-default"></i></a> {{/if}}
					{{if $view}}<a href="block/{{$channel}}/{{$item.title}}" title="{{$view}}"><i class="icon-external-link design-icons design-view-icon btn btn-default"></i></a> {{/if}}
					{{*if $preview}}<a href="block/{{$channel}}/{{$item.title}}?iframe=true&width=80%&height=80%" title="{{$preview}}" class="webpage-preview" ><i class="icon-eye-open design-icons design-preview-icon btn-btn-default"></i></a> {{/if*}}
					{{$item.title}}
					</div>
				{{/foreach}}
		{{/foreach}}
	   </div>
	
	   <div class="clear"></div>

{{/if}}
