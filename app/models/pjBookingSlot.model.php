<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingSlotModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings_slots';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start_ts', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'end_ts', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'start_iso', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'end_iso', 'type' => 'datetime', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingSlotModel($attr);
	}
}
?>