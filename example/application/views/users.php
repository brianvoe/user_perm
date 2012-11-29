<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>User Perm</title>
	<script type="text/javascript">
		window.onload = function(){
			var text_input = document.getElementById('user_name');
			text_input.focus();
			text_input.select();
		}
	</script>
	<style>
		a{text-decoration: none;}
	</style>
</head>
<body>
	<h1>Users</h1>
	<div><a href="<?php echo site_url(); ?>">Home</a></div>
	<div style="padding: 15px 0 0 0;">
		<form action="<?php echo ($user_id ? site_url('users/edit'): site_url('users/add')); ?>" method="post">
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
			<label><?php echo ($user_id ? 'Update': 'Add'); ?> User</label>
			<input type="text" id="user_name" name="name" value="<?php echo $name; ?>" />
			<input type="submit" value="<?php echo ($user_id ? 'Update': 'Add'); ?>" />
		</form>
	</div>
	<div style="padding: 15px 0 0 0;"><?php echo $users; ?></div>
</body>
</html>