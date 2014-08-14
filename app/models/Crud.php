<?php
class Crud {
	public static $selectors = array(
		'=' => array('name' => '=', 'code' => '{field} = {data}' ),
		'!=' => array('name' => '!=', 'code' => '{field} <> {data}' ),
		'<' => array('name' => '<', 'code' => '{field} < {data}' ),
		'>' => array('name' => '>', 'code' => '{field} > {data}' ),
		'dt_equal' => array('name'=> '=', 'code' => "DATE({field}) = STR_TO_DATE({data},'%m/%d/%Y') ")		

		);

	public static function get_selectors($selectors) {
		$s_arr = explode(",", $selectors);
		$data = array();
		foreach($s_arr as $sel) {
			$data[] = array(
					'id' => $sel,
					'name' => self::$selectors[$sel]['name']
				);
		}
		return $data;
	}

	public static function get_filters_sql($filters) {
		$sql_arr = array();
		foreach($filters as $filter) {
			$field = key($filter);
			$selector = $filter[$field]['selector'];
			$data = $filter[$field]['data'];
			if($data != '') {
				$code = self::$selectors[$selector]['code'];
				$code = str_replace("{field}", $field, $code);
				$code = str_replace("{data}", DB::connection()->getPdo()->quote($data), $code);
				$sql_arr[] = $code;
				//$sql_arr[] = $field.' '.self::$selectors[$selector]['code'].' '.DB::connection()->getPdo()->quote($data);  
			}
		}
		if(count($sql_arr) > 0) {
			return implode (' and ',$sql_arr);
		} else {
			return '1';
		}
	}

	public static function get_filters_status($filters,$filters_map) {
		$status = "";
		foreach($filters as $filter) {
			$field = key($filter);
			if(isset($filters_map[$field])) {
				$status .= $filters_map[$field]['title'].self::$selectors[$filter[$field]['selector']]['name']."'".$filter[$field]['data']."' ";
			}
		}
		return $status;

	}

}