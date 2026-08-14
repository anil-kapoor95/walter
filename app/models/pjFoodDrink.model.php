<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFoodDrinkModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'food_drinks';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public $i18n = array('title');
	
	public static function factory($attr=array())
	{
		return new pjFoodDrinkModel($attr);
	}
}
?>