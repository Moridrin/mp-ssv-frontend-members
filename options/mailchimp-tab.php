<?php
include_once "mailchimp-tab-save.php";
function mp_ssv_mailchimp_settings_page_frontend_members_tab() {
	$mailchimp_merge_tags = mp_ssv_get_merge_fields(get_option('mailchimp_member_sync_list_id'));
	
	global $wpdb;
	$table_name = $wpdb->prefix."mp_ssv_mailchimp_merge_fields";
	$fields = $wpdb->get_results("SELECT * FROM $table_name");
	$table_name = $wpdb->prefix."mp_ssv_frontend_members_fields";
	$fields_in_tab = $wpdb->get_results("SELECT * FROM $table_name");
	?>
	<br/>Add & Synchronize members to this MailChimp List.
	<form method="post" action="#">
		<table id="container" class="form-table">
			<tr>
				<th scope="row">List ID</th>
				<td><input type="text" class="regular-text" name="mailchimp_member_sync_list_id" value="<?php echo get_option('mailchimp_member_sync_list_id'); ?>"/></td>
			</tr>
			<tr>
				<th scope="row">Membber Field Name</th>
				<th scope="row">*|MERGE|* tag</th>
			</tr>
			<tr>
				<td><?php echo mp_ssv_get_member_fields_select("first_name", true, $fields_in_tab); ?></td>
				<td> <?php mp_ssv_get_merge_fields_select("first_Name", "FNAME", true, $mailchimp_merge_tags); ?> </td>
				<td></td>
			</tr>
			<tr>
				<td><?php echo mp_ssv_get_member_fields_select("last_name", true, $fields_in_tab); ?></td>
				<td><?php mp_ssv_get_merge_fields_select("last_Name", "LNAME", true, $mailchimp_merge_tags); ?></td>
				<td></td>
			</tr>
			<?php 
			foreach ($fields as $field) {
				$field = json_decode(json_encode($field),true);
				$member_tag = stripslashes($field["member_tag"]);
				$mailchimp_tag = stripslashes($field["mailchimp_tag"]);
				?>
				<tr>
					<td><?php echo mp_ssv_get_member_fields_select($member_tag, false, $fields_in_tab); ?></td>
					<td><?php mp_ssv_get_merge_fields_select($member_tag, $mailchimp_tag, false, $mailchimp_merge_tags); ?></td>
					<td><input type="hidden" name="submit_option_<?php echo $member_tag; ?>"></td>
				</tr>
				<?php
			}
			?>
		</table>
		<button type="button" id="add_field_button" onclick="mp_ssv_add_new_field()">Add Field</button>
		<?php submit_button(); ?>
	</form>
	<script src="https://code.jquery.com/jquery-2.2.0.js"></script>
	<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
	function mp_ssv_add_new_field() {
		var id = Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
		$("#container > tbody:last-child").append(
			$('<tr id="' + id + '">').append(
				$('<td>').append(
					'<?php echo mp_ssv_get_member_fields_select_for_javascript(false, $fields_in_tab); ?>'
				)
			).append(
				$('<td>').append(
					'<?php echo mp_ssv_get_merge_fields_select_for_javascript(false, $mailchimp_merge_tags); ?>'
				)
			).append(
				$('<td>').append(
					'<input type="hidden" name="submit_option_' + id + '">'
				)
			)
		);
	}
	</script>
	<?php
}

if (!function_exists("mp_ssv_get_member_fields_select_for_javascript")) {
	function mp_ssv_get_member_fields_select_for_javascript($disabled, $fields_in_tab) {
		?><select name="member_' + id + '" <?php if ($disabled) { echo "disabled"; } ?>><option></option><?php
		foreach ($fields_in_tab as $field) {
			$field = json_decode(json_encode($field),true);
			$database_component = stripslashes($field["component"]);
			$title = stripslashes($field["title"]);
			if (strpos($database_component, "name=\"") !== false) {
				$identifier = preg_replace("/.*name=\"/","",stripslashes($database_component));
				$identifier = preg_replace("/\".*/","",$identifier);
				$identifier = strtolower($identifier);
				echo "<option>".$identifier."</option>";
			} else if (strpos($database_component, "select") !== false || strpos($database_component, "radio") !== false || strpos($database_component, "role checkbox") !== false) {
				$identifier = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", $title)));
				echo "<option>".$identifier."</option>";
			}
		}
		?></select><?php
	}
}

if (!function_exists("mp_ssv_get_member_fields_select")) {
	function mp_ssv_get_member_fields_select($tag_name, $disabled, $fields_in_tab) {
		if ($tag_name == "") {
			$s = uniqid('', true);
			$tag_name = base_convert($s, 16, 36);
		}
		?><select name="member_<?php echo $tag_name; ?>" <?php if ($disabled) { echo "disabled"; } ?>><option></option><?php
		foreach ($fields_in_tab as $field) {
			$field = json_decode(json_encode($field),true);
			$database_component = stripslashes($field["component"]);
			$title = stripslashes($field["title"]);
			if (strpos($database_component, "name=\"") !== false) {
				$identifier = preg_replace("/.*name=\"/","",stripslashes($database_component));
				$identifier = preg_replace("/\".*/","",$identifier);
				$identifier = strtolower($identifier);
				if ($identifier == $tag_name) {
					echo "<option selected>".$identifier."</option>";
				} else {
					echo "<option>".$identifier."</option>";
				}
			} else if (strpos($database_component, "select") !== false || strpos($database_component, "radio") !== false || strpos($database_component, "role checkbox") !== false) {
				$identifier = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '_', str_replace(" ", "_", $title)));
				if ($identifier == $tag_name) {
					echo "<option selected>".$identifier."</option>";
				} else {
					echo "<option>".$identifier."</option>";
				}
			}
		}
		?></select><?php
	}
}
?>