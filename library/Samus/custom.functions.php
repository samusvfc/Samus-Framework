<?php
if(!function_exists("lcfirst")) {
    function lcfirst($string) {
        return strtolower(substr($string, 0, 1)).substr($string, 1);
    }
}

