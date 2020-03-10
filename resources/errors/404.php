<?php $this->section("header");?>
<div class="container">
    <h1>Opps!, We could not find <i><?php echo $_SERVER['REQUEST_URI'];?></i></h1>
    <p></p>
    <hr>
    <center>
    <img src='<?php echo _PATH_;?>resources/public/img/404.png' />
    </center>
</div>
<?php $this->section("footer");?>