<table id="$id">
	<% control FieldsForForm %>
	<tr id="{$Top.id}-row-$Pos">
		<% control Fields %>
		<td>
			$Field
		</td>
		<% end_control %>
		<td>
			<a class="csvlist-remove" id="{$Top.id}-remove-$Pos" href="javascript:;">[&minus;]</a>
		</td>
	<% end_control %>

	</tr>
	<tr id="{$id}-addform" style="display:none">
		<% control NewFields %>
		<td>
			$Field
		</td>
		<% end_control %>
		<td>
			<a class="csvlist-remove" id="{$Top.id}-remove-$Pos" href="javascript:;">[&minus;]</a>
		</td>
	</tr>
	<tr>
		<td><a class="csvlist-add" id="{$id}-add" href="javascript:;">[+]</a></td>
	</tr>
	
</table>
<script type="text/javascript">
	var {$id}_num = $FieldsForForm.Count;
	jQuery(".csvlist-remove").click(function() {
		var rowid = this.id.replace("remove", "row");
		jQuery("#"+rowid).remove();
	});
	jQuery("#{$id}-add").click(function() {
		{$id}_num++;
		
		//Clone row
		jQuery("#{$id}-addform")
			.clone()
			.attr("id", "{$id}-row-"+{$id}_num)
			.insertAfter(jQuery("#{$id}-addform"))
			.show();
			
		//Rename fields (XXX: Only input)
		jQuery("#{$id}-row-"+{$id}_num+" input, #{$id}-row-"+{$id}_num+" select").each(function() {
			var id="{$id}-"+{$id}_num+"-"+this.name+"";
			var name="{$Name}["+{$id}_num+"]["+this.name+"]";
			this.id = id;
			this.name = name;
		});
			
		//Activate remove button
		jQuery("#{$id}-row-"+{$id}_num+" .csvlist-remove")
			.attr("id", "{$id}-remove-"+{$id}_num)
			.click(function() {
				var rowid = this.id.replace("remove", "row");
				jQuery("#"+rowid).remove();
			});
	});
</script>