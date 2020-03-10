<?php $this->section("login_header"); ?>
<div class="container">
	<div class="row">
		<div class="col-md-4 offset-md-4">
			<div class="card">
				<div class="card-header" style="background:#fff;text-align:center;border:none;">
				    <img style="width:200px;" src="<?php echo _PATH_;?>resources/public/img/logo/logo-dark.png" /></div>
				<div class="card-body">
				<?php 
					if ($this->message){
						echo "<div class='alert alert-info'>".$this->message."</div>";
					}
				?>
				<form action="" method="post" autocomplete="off">
					<div class="form-group">
						<label>Username</label>
						<input type="text" name="username" placeholder="Username" class="form-control" />
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" placeholder="Password" class="form-control" />
					</div>
					<input type="submit" class="btn btn-primary" value=" Login " />
				</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->section("login_footer");?>