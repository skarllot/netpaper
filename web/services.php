<?php

	require_once dirname(__FILE__).'/db/connection.php';
	require_once dirname(__FILE__).'/db/dbversion.php';

	if(isset($_REQUEST['method']) && $_REQUEST['method'] != '') {
		extract($_REQUEST);
	} else {
		$method = '';
	}
	if(empty($format)) {
		$format = 'json';
	}

	switch($method) {
		case "getDBVersion":
			$params = array("version" => getDBVersion());
			echo formatData($params, $format);
			break;
		default:
			$params = array("status" => 0, "msg" => "Invalid parameters supplied");
			echo formatData($params, $format);
			break;
	}

	function getDBVersion() {
		$conn = new Connection();
		$conn->connect();
		$dbversion = new DBVersion($conn);
		return $dbversion->getVersion();
	}

	function formatData($data, $format='json') {
		/* output in necessary format */
		if($format == 'json') {
			header('Content-type: application/json');
			return json_encode(array('data'=>$data));
		} else {
			$response = '';
			header('Content-type: text/xml');
			$response .= '<user>';
			foreach($data as $index => $data) {
				if(is_array($data)) {
					foreach($data as $key => $value) {
						$response .= '<'.$key.'>';
						if(is_array($value)) {
							foreach($value as $tag => $val) {
								$response .= '<'.$tag.'>'.htmlentities($val).'</'.$tag.'>';
							}
						}
						$response .= '</'.$key.'>';
					}
				}
			}
			$response .= '</user>';
		}
		return $response;
	}

/*
vim: ts=4 sw=4
*/
?>
