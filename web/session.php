<?php

class Session
{
	function __construct() {
	}

	public function isValid() {
		return (isset($_SESSION['program']) &&
			$_SESSION['program'] == 'netpaper');
	}

	public function create() {
		session_start();
		$_SESSION['program'] = 'netpaper';
		return session_id();
	}

	public function start($id) {
		if (isset($id) && !empty($id))
			session_id($id);

		session_start();

		if (!$this->isValid()) {
			$this->destroy();
			return False;
		}
		return True;
	}

	public function destroy($id = NULL) {
		if ($id) {
			session_id($id);
			session_start();
		}

		if (!$this->isValid())
			return False;

		$_SESSION = array();
		if (isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time() - 42000, '/');
		session_destroy();
		return True;
	}
}

/*
vim: ts=4 sw=4
*/
?>
