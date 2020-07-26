<?php  include('../config.php'); ?>
	<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
	<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
	
	<title>Admin | Dashboard</title>
</head>
<body>
	
	<div class="header">
		<div class="logo">
			<a href="<?php echo BASE_URL .'admin/dashboard.php' ?>">
				<h1>Bash's Blog - Admin</h1>
			</a>
		</div>
		<?php if (isset($_SESSION['user'])): ?>
		
			<!-- if user is not Admin or Author -->
			<?php if (!in_array($_SESSION['user']['role'], ["Admin", "Author"])){header('location:'.BASE_URL .'index.php');} ?>
		
			<div class="user-info">
				<span><?php echo $_SESSION['user']['username'] ?></span> &nbsp; &nbsp; 
				<a href="<?php echo BASE_URL . '/logout.php'; ?>" class="logout-btn">logout</a>
			</div>
		<?php endif ?>
	</div>
	<div class="container dashboard">
		<h1>Welcome</h1>
		<!-- validation errors for the form -->
	<?php include(ROOT_PATH . '/includes/errors.php') ?>

		
		<div class="stats">
			<a href="users.php" class="first">
			<!-- FIX THESE STATIC NUMBERS	 -->
			<span>43</span> <br>
				<span>Newly registered users</span>
			</a>
			<a href="posts.php">
				<span>43</span> <br>
				<span>Published posts</span>
			</a>
			<a>
				<span>43</span> <br>
				<span>Published comments</span>
			<!-- FIX THESE STATIC NUMBERS	 -->

			</a>
		</div>
		<br><br><br>
	<!-- DISABLE LINKS TO ACCESS ADMIN PREVILAGED SECTIONS -->
		<div class="buttons">	
			<a href="users.php" >Add Users</a>
			<a href="posts.php" disabled>Add Posts</a>
		</div>
	</div>
</body>
</html>