<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingEquipmentModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings_equipments';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'equipment_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'units', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingEquipmentModel($attr);
	}
}
?>