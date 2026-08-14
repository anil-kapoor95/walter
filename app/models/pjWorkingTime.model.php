<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWorkingTimeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'working_times';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'monday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'tuesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'wednesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'thursday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'friday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'saturday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'sunday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_morning_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_morning_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_afternoon_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_afternoon_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_dayoff', 'type' => 'enum', 'default' => 'F')
	);
	
	protected $validate = array(
		'rules' => array(
			'foreign_id' => array(
				'pjActionNumeric' => true,
				'pjActionRequired' => true
			)
		)
	);

	public static function factory($attr=array())
	{
		return new pjWorkingTimeModel($attr);
	}
	
	public function getDaysOff($foreign_id)
	{
		$_arr = array();
		$arr = $this->reset()->where('t1.foreign_id', $foreign_id)->limit(1)->findAll()->getData();
		$arr = !empty($arr) ? $arr[0] : $arr;
		foreach ($arr as $k => $v)
		{
			if (strpos($k, "_dayoff") !== false && $v == 'T')
			{
				list($key) = explode("_", $k);
				$_arr[$key] = 1;
			}
		}
		return $_arr;
	}
	
	public function getWorkingTime($foreign_id)
	{
		$arr = $this->reset()->where('t1.foreign_id', $foreign_id)->limit(1)->findAll()->getData();
	
		return !empty($arr) ? $arr[0] : $arr;
	}
	
	public function filterDate($arr, $date)
	{
		if (empty($arr))
		{
			return false;
		}
	
		$day = strtolower(date("l", strtotime($date)));
	
		if ($arr[$day . '_dayoff'] == 'T')
		{
			return array();
		}
	
		$wt = array();
		foreach ($arr as $k => $v)
		{
			if (strpos($k, $day . '_morning_from') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['morning_start_hour'] = $d['hours'];
				$wt['morning_start_minutes'] = $d['minutes'];
				$wt['morning_start_ts'] = strtotime($date . " " . $v);
				continue;
			}
				
			if (strpos($k, $day . '_morning_to') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['morning_end_hour'] = $d['hours'];
				$wt['morning_end_minutes'] = $d['minutes'];
				$wt['morning_end_ts'] = strtotime($date . " " . $v);
				continue;
			}
			if (strpos($k, $day . '_afternoon_from') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['afternoon_start_hour'] = $d['hours'];
				$wt['afternoon_start_minutes'] = $d['minutes'];
				$wt['afternoon_start_ts'] = strtotime($date . " " . $v);
				continue;
			}
			
			if (strpos($k, $day . '_afternoon_to') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['afternoon_end_hour'] = $d['hours'];
				$wt['afternoon_end_minutes'] = $d['minutes'];
				$wt['afternoon_end_ts'] = strtotime($date . " " . $v);
				continue;
			}
				
			if (strpos($k, $day . '_from') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['start_hour'] = $d['hours'];
				$wt['start_minutes'] = $d['minutes'];
				$wt['start_ts'] = strtotime($date . " " . $v);
				continue;
			}
	
			if (strpos($k, $day . '_to') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['end_hour'] = $d['hours'];
				$wt['end_minutes'] = $d['minutes'];
				$wt['end_ts'] = strtotime($date . " " . $v);
				continue;
			}
		}
		return $wt;
	}
	
}