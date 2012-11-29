<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>User Perm</title>
	<script type="text/javascript">
		window.onload = function(){
			var text_input = document.getElementById('group_name');
			text_input.focus();
			text_input.select();
		}
	</script>
	<style>
		a{text-decoration: none;}
		ul{list-style: none;}
	</style>
</head>
<body>
	<h1>Groups</h1>
	<div style="padding: 0 0 15px 0;"><a href="<?php echo site_url(); ?>">Home</a></div>
	<div style="float: left; width: 300px;">
		<h3 style="padding: 0 0 10px 0; margin: 0;">Current Groups</h3>
		<?php echo $groups; ?>
	</div>
	<div style="float: left; width 700px;">
		<h3 style="padding: 0 0 10px 0; margin: 0;"><?php echo ($group_id ? 'Update': 'Add'); ?> Group</h3>
		<form action="<?php echo ($group_id ? site_url('groups/edit'): site_url('groups/add')); ?>" method="post">
			<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
			<div style="float: left; width: 250px;">
				<label>Group Name</label>
				<div><input type="text" id="group_name" name="name" value="<?php echo $name; ?>" /></div>
				<br />
				<label>Users</label>
				<div><?php echo $users; ?></div>
			</div>
			<div style="float: left; width: 400px;">
				<label>Permissions</label>
				<div><?php echo $permissions; ?></div>
			</div>
			<div style="clear: both; padding-bottom: 20px;"></div>
			<input type="submit" value="Submit" />
		</form>
	</div>
</body>
</html>