<?php
class MongoDriver
{
    public static function factory()
    {
        return new Mongo;
    }
}
