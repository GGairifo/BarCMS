<?php

	// captcha/xxx.php
	function validate_captcha($input) {
		if (!isset($_SESSION['captcha']) || $_SESSION['captcha'] !== $input) {
			return false;
		}
		return true;
	}

	if ( !isset( $_SESSION ) ) {
		session_start();
	}
?>