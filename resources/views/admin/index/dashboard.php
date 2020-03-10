
<?php
	$this->section("admin_header");
?>

<div class="container" style="margin-top: 50px;">
	<div class='row'>
		
		<div class="col-md-12">
			<div class="row">
			    
				<div class="col-md-4" style='padding-right:0px;'>
			        <div class='card'>
						<div class="card-body">
							<h4>Users</h4>
							<h1><?php echo $this->userCount;?></h1>
						</div>
						<div class="card-footer">
							<a href='<?php echo _ADMIN_PATH_;?>/user/view' class='btn btn-primary'>View</a>
							
						</div>
					</div>
			    </div>
			    <div class="col-md-4" style='padding-right:0px;'>
			        <div class='card'>
						<div class="card-body">
							<h4>Quote Required</h4>
							<h1><?php echo $this->leadCount;?></h1>
						</div>
						<div class="card-footer">
							<a href='<?php echo _ADMIN_PATH_;?>/lead/view' class='btn btn-primary'>View</a>
							
						</div>
					</div>
			    </div>
			    <div class="col-md-4" style='padding-right:0px;'>
			        <div class='card'>
						<div class="card-body">
							<h4>Clients</h4>
							<h1><?php echo $this->clientCount;?></h1>
						</div>
						<div class="card-footer">
							<a href='<?php echo _ADMIN_PATH_;?>/client/view' class='btn btn-primary'>View</a>
							
						</div>
					</div>
			    </div>
			</div>
		</div>
	</div>
</div>
<?php
	$this->section("admin_footer");
?>