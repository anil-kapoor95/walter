<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingFoodDrinkModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings_food_drinks';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'food_drink_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'people', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingFoodDrinkModel($attr);
	}
}
?>