<?php ?>
<style>
div.error_box {
    font-size: 120%;
    font-family: monospace;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

div.error_box div.error_header {
    font-size: 110%;
    font-weight: 500;
    padding: 5px;
    color: #fff;
    border-radius: 5px 5px 0 0;
}

div.width {
    margin: 0 auto !important;
    width: 20%;
    text-align: center;
}

div.error_404 {
    background-color: #00bc09;
}

div.error_box div.error_content {
}

div.error_box div.error_text {
    padding: 8px 15px;
    font-family: sans-serif;
    background-color: #aaa;
    color: #fff;
    text-shadow: 2px 2px 7px rgba(0, 0, 0, 0.4), 0 0 2px rgb(0, 0, 0);
}
</style>
<div class="width error_box">
    <div class="error_404 error_header">404</div>
    <div></div>
    <div class="error_text">
        <?php echo $message ?>
    </div>
</div>