<?php
class MysqlDriver
{
    public static function factory()
    {
        return new PDO(DATABASE_DSN, DATABASE_USER, DATABASE_PASSWORD);
    }
}
