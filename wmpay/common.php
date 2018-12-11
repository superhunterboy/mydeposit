<?php

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    define('IS_AJAX', strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' );
}else{
    define('IS_AJAX', false);
}