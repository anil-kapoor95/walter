<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjEquipmentModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'equipments';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'multi_units', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_per', 'type' => 'enum', 'default' => ':NULL')
	);
	
	public $i18n = array('title');
	
	public static function factory($attr=array())
	{
		return new pjEquipmentModel($attr);
	}
}
?>