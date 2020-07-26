<div class="logged_in_info">
    <?php if(isset($_SESSION['user'])):?>
		<span>welcome <?php echo $_SESSION['user']['username'] ?></span>
		|
        <span><a href="logout.php">logout</a></span>
    <?php endif?>
	</div>