<?php

/**
 * Utilitary class for the GForge ViewCVS wrapper.
 *
 * Portion of this file is inspired from the ViewCVS wrapper
 * contained in CodeX.
 * Copyright (c) Xerox Corporation, CodeX / CodeX Team, 2001,2002. All Rights Reserved.
 * http://codex.xerox.com
 *
 * @version   $ID$
 */

define ('SEPARATOR', "\n\t\r\0\x0B");

/**
 *      viewcvs_is_html() - Test if ViewCVS returns HTML.
 *
 *      @return true if the content type of the ViewCVS is text/html.
 */
function viewcvs_is_html() {
	$request_uri = getStringFromServer('REQUEST_URI');
	$query_string = getStringFromServer('QUERY_STRING');

	return (strpos($request_uri,"*checkout*") === false && 
		strpos($query_string,"view=graphimg") === false &&
		strpos($query_string,"view=tar") === false &&
		strpos($request_uri,"*docroot*") === false &&
		strpos($request_uri,"makepatch=1") === false);
}

/**
 * make_arg_cmd_safe() - Make strings safe for the command line.
 *
 * @param  string  The argument that needs to be cleaned.
 * @return string  The argument with dangerous shell characters escaped.
 */
function make_arg_cmd_safe($arg) {
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }
    return escapeshellcmd($arg);
}

/**
 *      viewcvs_execute() - Call to viewcvs.cgi and returned the output.
 *
 *      @return String the output of the ViewCVS command.
 */
function viewcvs_execute() {

	$request_uri = getStringFromServer('REQUEST_URI');
	$query_string = getStringFromServer('QUERY_STRING');

	// this is very important ...
 	if (getStringFromServer('PATH_INFO') == '') {
 		$path = '/';
 	} else {
 		$path = getStringFromServer('PATH_INFO');
 		// hack: path must always end with /
 		if (strrpos($path,'/') != (strlen($path)-1)) {
 			$path .= '/';
 		}
 	}
 	$query_string = str_replace('\\&', '&', make_arg_cmd_safe($query_string));
 	$query_string = str_replace('\\*', '*', $query_string);
 	$path = str_replace('\\*', '*', make_arg_cmd_safe($path));
	$command = 'HTTP_COOKIE="'.make_arg_cmd_safe(getStringFromServer('HTTP_COOKIE')).'" '.
		'REMOTE_ADDR="'.make_arg_cmd_safe(getStringFromServer('REMOTE_ADDR')).'" '.
		'QUERY_STRING="'.$query_string.'" '.
		'SERVER_SOFTWARE="'.make_arg_cmd_safe(getStringFromServer('SERVER_SOFTWARE')).'" '.
		'SCRIPT_NAME="'.make_arg_cmd_safe(getStringFromServer('SCRIPT_NAME')).'" '.
		'HTTP_USER_AGENT="'.make_arg_cmd_safe(getStringFromServer('HTTP_USER_AGENT')).'" '.
		'HTTP_ACCEPT_ENCODING="'.make_arg_cmd_safe(getStringFromServer('HTTP_ACCEPT_ENCODING')).'" '.
		'HTTP_ACCEPT_LANGUAGE="'.make_arg_cmd_safe(getStringFromServer('HTTP_ACCEPT_LANGUAGE')).'" '.
		'PATH_INFO="'.$path.'" '.
		'PATH="'.make_arg_cmd_safe(getStringFromServer('PATH')).'" '.
		'HTTP_HOST="'.make_arg_cmd_safe(getStringFromServer('HTTP_HOST')).'" '.
		$GLOBALS['sys_path_to_scmweb'].'/viewcvs.cgi 2>&1';

	ob_start();
	passthru($command);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

?>