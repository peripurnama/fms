<style>
	
</style>

<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">

				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
				<?php if($_SESSION['login_type'] == 2): ?>
					<a href="index.php?page=files" class="nav-item nav-files"><span class='icon-field'><i class="fa fa-file"></i></span> Files</a>
					<a href="index.php?page=tracking" class="nav-item nav-files"><span class='icon-field'><i class="fa fa-file"></i></span> Tracking</a>
					<a href="index.php?page=history" class="nav-item nav-files"><span class='icon-field'><i class="fa fa-file"></i></span> History</a>
				<?php endif; ?>
				<?php if($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 4): ?>
					<a href="index.php?page=task" class="nav-item nav-files"><span class='icon-field'><i class="fa fa-file"></i></span> Task</a>
				<?php endif; ?>
				<?php if($_SESSION['login_type'] == 1): ?>
					<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
				<?php endif; ?>
				
		</div>

</nav>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>