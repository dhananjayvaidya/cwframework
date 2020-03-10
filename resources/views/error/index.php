<?php require 'views/header.php'; ?>

<div class='hero-unit' >
    <div class='container' id="content">
        <h1>404 Page not found</h1>
        <small class="small" style='color:grey;'><i><?php echo $_SERVER['REQUEST_URI'];?></i></small>
    </div>
</div>
<div class='container'>
    <center>
        <img src="<?php echo _PATH_;?>public/img/443809727_640.jpg"
    </center>
</div>

<?php require 'views/footer.php';?>