<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjRoomLayoutModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'rooms_layouts';
	
	protected $schema = array(
		array('name' => 'room_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'layout_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjRoomLayoutModel($attr);
	}
}
?>