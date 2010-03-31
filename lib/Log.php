<?php

class Log
{
    public static function error(Exception $e)
    {
        error_log(sprintf("[%s] line %d in %s: %s", $e->getMessage(), $e->getLine(), $e->getFile(), $e->getTraceAsString()));
    }
}
