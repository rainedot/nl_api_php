<?php
require_once ('funcs.php');

$callback = new callback();

$json = file_get_contents('php://input');

if(isset($json) & (!empty($json)) & ($callback->isJson($json))) {
    /*
     * $callback->balance_transfer($json);
     * $callback->item_purchase($json);
     */
}






