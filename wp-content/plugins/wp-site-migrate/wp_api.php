<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('WPEWPAPI')) :
	class WPEWPAPI {
		public $account;

		public function __construct($settings) {
			$this->account = WPEAccount::find($settings);
		}
		
		public function pingbv($method, $body) {
			$url = $this->account->authenticatedUrl($method);
			$this->http_request($url, $body);
		}

		public function http_request($url, $body) {
			$_body = array(
				'method' => 'POST',
				'timeout' => 15,
				'body' => $body);

			return wp_remote_post($url, $_body);
		}
	}
endif;