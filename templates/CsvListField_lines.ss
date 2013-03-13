<div id="$id" class="csvlistfield">
	<% control FieldsForForm %>
	<div id="{$Top.id}-row-$Pos">
		<% control Fields %>
		<div>
			$FieldHolder
		</div>
		<% end_control %>
		<div class="lessform">
			<a class="csvlist-remove" id="{$Top.id}-remove-$Pos" href="javascript:;">[&minus;]</a>
		</div>
	</div>
	<% end_control %>
	<div id="{$Name}-addform" style="display:none">
		<% control NewFields %>
		<div>
			$FieldHolder
		</div>
		<% end_control %>
		<div class="lessform">
			<a class="csvlist-remove" id="{$Top.Name}-remove-$Pos" href="javascript:;">[&minus;]</a>
		</div>
	</div>
        <br clear="left"/>
	<div class="moreform">
		<div><a class="csvlist-add" id="{$Name}-add" href="javascript:;">[+]</a></div>
	</div>
</div>
<script type="text/javascript">
	var {$Name}_num = $FieldsForForm.Count;
	jQuery(".csvlist-remove").click(function() {
		var rowid = this.id.replace("remove", "row");
		jQuery("#"+rowid).remove();
	});
	jQuery("#{$Name}-add").click(function() {
		{$Name}_num++;

		//Clone row
		jQuery("#{$Name}-addform")
			.clone()
			.attr("id", "{$Name}-row-"+{$Name}_num)
			.insertAfter(jQuery("#{$Name}-addform"))
			.show();

		//Rename fields (XXX: Only input)
		jQuery("#{$Name}-row-"+{$Name}_num+" input, #{$Name}-row-"+{$Name}_num+" select").each(function() {
			var id="{$Name}["+{$Name}_num+"]["+this.id+"]";
			this.id = id;
			this.name = id;
		});

		//Activate remove button
		jQuery("#{$Name}-row-"+{$Name}_num+" .csvlist-remove")
			.attr("id", "{$Name}-remove-"+{$Name}_num)
			.click(function() {
				var rowid = this.id.replace("remove", "row");
				jQuery("#"+rowid).remove();
			});
	});
</script>
