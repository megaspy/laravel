<?php
class Crud {
	public static $selectors = array(
		'=' => array('name' => 'Equal To', 'code' => '{field} = {data}' ),
		'!=' => array('name' => 'Not Equal To', 'code' => '{field} <> {data}' ),
		'like' => array('name' => 'Contains', 'code' => '{field} like CONCAT(\'%\',{data},\'%\')' ),
		'not_like' => array('name' => 'Not Contains', 'code' => '{field} not like CONCAT(\'%\',{data},\'%\')' ),
		'start_like' => array('name' => 'Starts With', 'code' => '{field} like CONCAT({data},\'%\')' ),
		'end_like' => array('name' => 'Ends With', 'code' => '{field} like CONCAT(\'%\',{data})' ),
		'<' => array('name' => 'Less Than', 'code' => '{field} < {data}' ),
		'>' => array('name' => 'Greater Than', 'code' => '{field} > {data}' ),
		'btw' => array('name' => 'Between', 'code' => '{field} between {data} and {data2}' ),		
		'dt_btw' => array('name'=> 'Between', 'code' => " {field} between STR_TO_DATE(CONCAT({data},' 00:00:00'),'%m/%d/%Y %H:%i:%s') and STR_TO_DATE(CONCAT({data2},' 23:59:59'),'%m/%d/%Y %H:%i:%s') "),		
		'dt_equal' => array('name'=> 'Equal To', 'code' => "DATE({field}) = STR_TO_DATE({data},'%m/%d/%Y') ")		

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

	public static function get_selector_code($field,$selector,$data,$data2 = '') {
		$code = self::$selectors[$selector]['code'];
		$code = str_replace("{field}", $field, $code);
		$code = str_replace("{data}", DB::connection()->getPdo()->quote($data), $code);
		$code = str_replace("{data2}", DB::connection()->getPdo()->quote($data2), $code);
		return $code;
	}	

	public static function get_filters_sql($filters) {
		$sql_arr = array();
		foreach($filters as $filter) {
			$field = key($filter);
			$selector = $filter[$field]['selector'];

			//$data = $filter[$field]['data'];
			$data = array_get($filter[$field],'data', '');
			$data2 = array_get($filter[$field],'data2', '');

			if($data != '') {
				$sql_arr[] = self::get_selector_code($field,$selector,$data,$data2);
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
			if(isset($filters_map[$field]) && ($filters_map[$field]['type'] != 'static') 
				&& isset($filter[$field]['data']) && ($filter[$field]['data'] != '') 
				&& isset($filters_map[$field]['title'])) {
				if ($filters_map[$field]['type'] == 'select') {
					$res_mod = $filters_map[$field]['resource'];
					$item = $res_mod::find($filter[$field]['data']);
					$status .= $filters_map[$field]['title'].' '.self::$selectors[$filter[$field]['selector']]['name']." '".$item['name']."' <br>";
				} elseif ($filters_map[$field]['type'] == 'checkbox') {
					$status .= $filters_map[$field]['title'].'<br>';
				} else {
					if(self::$selectors[$filter[$field]['selector']]['name'] == 'Between') {
						$status .= $filters_map[$field]['title'].' '.self::$selectors[$filter[$field]['selector']]['name']." '".$filter[$field]['data']."' and '".$filter[$field]['data2']."' <br>";
					} else {
						$status .= $filters_map[$field]['title'].' '.self::$selectors[$filter[$field]['selector']]['name']." '".$filter[$field]['data']."' <br>";
					}					
				}
			}
		}
		return $status;
	}


	public static function check_filters($filters_map,$filters) {
		$data = array();
		foreach ($filters as $i => $filter) {
			$field = key($filter);
			if(isset($filters_map[$field]) && ($filters_map[$field]['type'] != 'static') ) {
				$data[] = $filter;
			}
		}
		foreach ($filters_map as $field => $map) {
			if($map['type'] == 'static') {
				$data[][$field] = array(
						'selector' => $map['selector'],
						'data' => $map['value']
					); 
			}
		}
		return $data;
	}

	public static function get_model_name($model) {
		if(strpos(' '.$model,'_')) {
			$arr = explode("_", $model);
			$data = '';
			foreach($arr as $i => $d) {
				$data .= $d;
				if(isset($arr[$i+1])) {
					if(ctype_upper(substr($arr[$i+1],0,1))) {
						$data .= '\\';
					} else {
						$data .= '_';
					}
				}
			} 
			return $data;
		} else {
			return $model;
		}
	}

}