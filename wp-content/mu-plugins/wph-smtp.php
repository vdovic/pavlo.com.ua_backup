<?php
/**
 * @package WPHost.me WordPress SMTP
 * @version 0.1
 */
/*
Plugin Name: WPHost.me WordPress SMTP
Plugin URI: http://help.WPHost.me
Description: This is a plugin to send mail from WordPress through SMTP.
Author: WPHost.me
Version: 0.1
Author URI: https://WPHost.me
*/

function configure_wp_smtp( $phpmailer )
{
	/*
	$phpmailer->isSMTP(); // наприклад, ви створили скриньку nazva@domen.com.ua
	$phpmailer->Host = "smtp.hosting.name"; // потрібно ввести назву вашого домену, у прикладі domen.com.ua
	$phpmailer->SMTPAuth = true;
	$phpmailer->Port = 587;
	$phpmailer->Username = "user@smtp.hosting.name"; // потрібно ввести вашу скриньку, у прикладі nazva@domen.com.ua
	$phpmailer->Password = "********"; // потрібно ввести пароль, вказаний при створені скриньки
	$phpmailer->SMTPSecure = 'tls';
	$phpmailer->From = $phpmailer->Username;
	$phpmailer->FromName = $phpmailer->Host;
	*/
	$phpmailer->isMail();
	if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.') {
	    $domain = substr($_SERVER['HTTP_HOST'], 4);
	} else {
	    $domain = $_SERVER['HTTP_HOST'];
	}
	$phpmailer->Sender = 'wp@'.$domain;
		
}
add_action( 'phpmailer_init', 'configure_wp_smtp' );

///////////////////////////////