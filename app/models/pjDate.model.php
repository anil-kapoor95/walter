<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDateModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'dates';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'start_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'end_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'start_morning', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'end_morning', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'start_afternoon', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'end_afternoon', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'start_evening', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'end_evening', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'is_dayoff', 'type' => 'enum', 'default' => 'F')
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
		return new pjDateModel($attr);
	}
	
	public function getDailyWorkingTime($foreign_id, $date)
	{
		$arr = $this->reset()
			->where('t1.foreign_id', $foreign_id)
			->where('t1.date', $date)
			->orderBy('t1.start_time ASC')
			->limit(1)
			->findAll()
			->getData();
			
		if (empty($arr))
		{
			return false;
		}
		$arr = $arr[0];
	
		if ($arr['is_dayoff'] == 'T')
		{
			return array();
		}
		
		$wt = array();
		$d = getdate(strtotime($arr['start_time']));
		$wt['start_hour'] = $d['hours'];
		$wt['start_minutes'] = $d['minutes'];
	
		$d = getdate(strtotime($arr['end_time']));
		$wt['end_hour'] = $d['hours'];
		$wt['end_minutes'] = $d['minutes'];
	
		$wt['start_ts'] = strtotime($date . " " . $arr['start_time']);
		$wt['end_ts'] = strtotime($date . " " . $arr['end_time']);

		$d = getdate(strtotime($arr['start_morning']));
		$wt['morning_start_hour'] = $d['hours'];
		$wt['morning_start_minutes'] = $d['minutes'];

		$d = getdate(strtotime($arr['end_morning']));
		$wt['morning_end_hour'] = $d['hours'];
		$wt['morning_end_minutes'] = $d['minutes'];

		$wt['morning_start_ts'] = strtotime($date . " " . $arr['start_morning']);
		$wt['morning_end_ts'] = strtotime($date . " " . $arr['end_morning']);
		
		$d = getdate(strtotime($arr['start_afternoon']));
		$wt['afternoon_start_hour'] = $d['hours'];
		$wt['afternoon_start_minutes'] = $d['minutes'];
		
		$d = getdate(strtotime($arr['end_afternoon']));
		$wt['afternoon_end_hour'] = $d['hours'];
		$wt['afternoon_end_minutes'] = $d['minutes'];
		
		$wt['afternoon_start_ts'] = strtotime($date . " " . $arr['start_afternoon']);
		$wt['afternoon_end_ts'] = strtotime($date . " " . $arr['end_afternoon']);

		$d = getdate(strtotime($arr['start_evening']));
		$wt['evening_start_hour'] = $d['hours'];
		$wt['evening_start_minutes'] = $d['minutes'];

		$d = getdate(strtotime($arr['end_evening']));
		$wt['evening_end_hour'] = $d['hours'];
		$wt['evening_end_minutes'] = $d['minutes'];

		$wt['evening_start_ts'] = strtotime($date . " " . $arr['start_evening']);
		$wt['evening_end_ts'] = strtotime($date . " " . $arr['end_evening']);

		return $wt;
	}
	
	public function getRangeWorkingTime($foreign_id, $date_from, $date_to)
	{
		$_arr = array();
		$from = strtotime($date_from);
		$to = strtotime($date_to);
		if ($from > $to)
		{
			$tmp = $from;
			$from = $to;
			$to = $tmp;
		}
		for ($i = $from; $i <= $to; $i += 86400)
		{
			$_arr[date("Y-m-d", $i)] = array();
		}

		$arr = $this
			->reset()
			->where('t1.foreign_id', $foreign_id)
			->where('t1.date >=', $date_from)
			->where('t1.date <=', $date_to)
			->orderBy('t1.start_time ASC')
			->findAll()
			->getData();
										
		foreach ( $arr as $item ) 
		{
			$_arr [$item ['date']] = $item;
			
			$d = getdate ( strtotime ( $item ['start_time'] ) );
			$_arr [$item ['date']] ['start_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['start_minutes'] = $d ['minutes'];
			
			$d = getdate ( strtotime ( $item ['end_time'] ) );
			$_arr [$item ['date']] ['end_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['end_minutes'] = $d ['minutes'];
			
			$_arr [$item ['date']] ['start_ts'] = strtotime ( $item ['date'] . " " . $item ['start_time'] );
			$_arr [$item ['date']] ['end_ts'] = strtotime ( $item ['date'] . " " . $item ['end_time'] );
			
			$d = getdate ( strtotime ( $item ['start_morning'] ) );
			$_arr [$item ['date']] ['morning_start_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['morning_start_minutes'] = $d ['minutes'];
			
			$d = getdate ( strtotime ( $item ['end_morning'] ) );
			$_arr [$item ['date']] ['morning_end_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['morning_end_minutes'] = $d ['minutes'];
			
			$_arr [$item ['date']] ['morning_start_ts'] = strtotime ( $item ['date'] . " " . $item ['start_morning'] );
			$_arr [$item ['date']] ['morning_end_ts'] = strtotime ( $item ['date'] . " " . $item ['end_morning'] );
			
			$d = getdate ( strtotime ( $item ['start_afternoon'] ) );
			$_arr [$item ['date']] ['afternoon_start_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['afternoon_start_minutes'] = $d ['minutes'];
				
			$d = getdate ( strtotime ( $item ['end_afternoon'] ) );
			$_arr [$item ['date']] ['afternoon_end_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['afternoon_end_minutes'] = $d ['minutes'];
				
			$_arr [$item ['date']] ['afternoon_start_ts'] = strtotime ( $item ['date'] . " " . $item ['start_afternoon'] );
			$_arr [$item ['date']] ['afternoon_end_ts'] = strtotime ( $item ['date'] . " " . $item ['end_afternoon'] );

			$d = getdate ( strtotime ( $item ['start_evening'] ) );
			$_arr [$item ['date']] ['evening_start_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['evening_start_minutes'] = $d ['minutes'];

			$d = getdate ( strtotime ( $item ['end_evening'] ) );
			$_arr [$item ['date']] ['evening_end_hour'] = $d ['hours'];
			$_arr [$item ['date']] ['evening_end_minutes'] = $d ['minutes'];

			$_arr [$item ['date']] ['evening_start_ts'] = strtotime ( $item ['date'] . " " . $item ['start_evening'] );
			$_arr [$item ['date']] ['evening_end_ts'] = strtotime ( $item ['date'] . " " . $item ['end_evening'] );
		}	
	
		return $_arr;
	}
	
	public function getDatesOff($foreign_id, $month, $year) 
	{
		$numOfDays = date ( "t", mktime ( 0, 0, 0, $month, 1, $year ) );
		$_arr = array ();
		for($i = 1; $i <= $numOfDays; $i ++) 
		{
			$_arr [date ( "Y-m-d", mktime ( 0, 0, 0, $month, $i, $year ) )] = array ();
		}
	
		$arr = $this->reset ()->where ( 't1.foreign_id', $foreign_id )->where ( 'MONTH(t1.date)', $month )->where ( 'YEAR(t1.date)', $year )->orderBy ( 't1.date ASC' )->findAll ()->getData ();
		
		foreach ( $arr as $v ) {
			$_arr [$v ['date']] = $v;
			$_arr [$v ['date']] ['start_ts'] = strtotime ( $v ['date'] . " " . $v ['start_time'] );
			$_arr [$v ['date']] ['end_ts'] = strtotime ( $v ['date'] . " " . $v ['end_time'] );
		}
		
		return $_arr;
	}
}
?>