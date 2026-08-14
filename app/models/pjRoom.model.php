<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjRoomModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'rooms';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'image_source', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'image_thumb', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'capacity', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'book_by_multiday', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'book_by_halfday', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'book_by_hour', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'price_per_day', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_half_day', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_per_hour', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('title', 'description');
	
	public static function factory($attr=array())
	{
		return new pjRoomModel($attr);
	}
}
?>