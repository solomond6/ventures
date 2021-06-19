<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('WPEAccount')) :
	class WPEAccount {
		public $settings;
		public $public;
		public $secret;
		public $sig_match;
	
		public function __construct($settings, $public, $secret) {
			$this->settings = $settings;
			$this->public = $public;
			$this->secret = $secret;
		}

		public static function find($settings, $public = false) {
			if (!$public) {
				$public = self::defaultPublic($settings);
			}
			$bvkeys = self::allKeys($settings);
			if ($public && array_key_exists($public, $bvkeys) && isset($bvkeys[$public])) {
				$secret = $bvkeys[$public];
			} else {
				$secret = self::defaultSecret($settings);
			}
			return new self($settings, $public, $secret);
		}

		public static function randString($length) {
			$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

			$str = "";
			$size = strlen($chars);
			for( $i = 0; $i < $length; $i++ ) {
				$str .= $chars[rand(0, $size - 1)];
			}
			return $str;
		}

		public static function allAccounts($settings) {
			return $settings->getOption('bvAccounts');
		}

		public static function hasAccount($settings) {
			$accounts = self::allAccounts($settings);
			return (is_array($accounts) && sizeof($accounts) >= 1);
		}

		public static function isConfigured($settings) {
			return self::defaultPublic($settings);
		}

		public function setup() {
			$bvinfo = new WPEInfo($this->settings);
			$this->settings->updateOption('bvSecretKey', self::randString(32));
			$this->settings->updateOption($bvinfo->plug_redirect, 'yes');
			$this->settings->updateOption('bvActivateTime', time());
		}

		public function authenticatedUrl($method) {
			$bvinfo = new WPEInfo($this->settings);
			$qstr = http_build_query($this->newAuthParams($bvinfo->version));
			return $bvinfo->appUrl().$method."?".$qstr;
		}

		public function newAuthParams($version) {
			$args = array();
			$time = time();
			$sig = sha1($this->public.$this->secret.$time.$version);
			$args['sig'] = $sig;
			$args['bvTime'] = $time;
			$args['bvPublic'] = $this->public;
			$args['bvVersion'] = $version;
			$args['sha1'] = '1';
			return $args;
		}

		public static function defaultPublic($settings) {
			return $settings->getOption('bvPublic');
		}

		public static function defaultSecret($settings) {
			return $settings->getOption('bvSecretKey');
		}

		public static function allKeys($settings) {
			$keys = $settings->getOption('bvkeys');
			if (!is_array($keys)) {
				$keys = array();
			}
			$public = self::defaultPublic($settings);
			$secret = self::defaultSecret($settings);
			if ($public)
				$keys[$public] = $secret;
			$keys['default'] = $secret;
			return $keys;
		}

		public function addKeys($public, $secret) {
			$bvkeys = $this->settings->getOption('bvkeys');
			if (!$bvkeys || (!is_array($bvkeys))) {
				$bvkeys = array();
			}
			$bvkeys[$public] = $secret;
			$this->settings->updateOption('bvkeys', $bvkeys);
		}

		public function updateKeys($publickey, $secretkey) {
			$this->settings->updateOption('bvPublic', $publickey);
			$this->settings->updateOption('bvSecretKey', $secretkey);
			$this->addKeys($publickey, $secretkey);
		}

		public function rmKeys($publickey) {
			$bvkeys = $this->settings->getOption('bvkeys');
			if ($bvkeys && is_array($bvkeys)) {
				unset($bvkeys[$publickey]);
				$this->settings->updateOption('bvkeys', $bvkeys);
				return true;
			}
			return false;
		}

		public function respInfo() {
			return array(
				"public" => substr($this->public, 0, 6),
				"sigmatch" => substr($this->sig_match, 0, 6)
			);
		}

		public function authenticate() {
			$method = $_REQUEST['bvMethod'];
			$time = intval($_REQUEST['bvTime']);
			$version = $_REQUEST['bvVersion'];
			$sig = $_REQUEST['sig'];
			if ($time < intval($this->settings->getOption('bvLastRecvTime')) - 300) {
				return false;
			}
			if (array_key_exists('sha1', $_REQUEST)) {
				$sig_match = sha1($method.$this->secret.$time.$version);
			} else {
				$sig_match = md5($method.$this->secret.$time.$version);
			}
			$this->sig_match = $sig_match;
			if ($sig_match !== $sig) {
				return $sig_match;
			}
			$this->settings->updateOption('bvLastRecvTime', $time);
			return 1;
		}
	
		public function add($info) {
			$accounts = self::allAccounts($this->settings);
			if(!is_array($accounts)) {
				$accounts = array();
			}
			$pubkey = $info['pubkey'];
			$accounts[$pubkey]['lastbackuptime'] = time();
			$accounts[$pubkey]['url'] = $info['url'];
			$accounts[$pubkey]['email'] = $info['email'];
			$this->update($accounts);
		}

		public function remove($pubkey) {
			$bvkeys = $this->settings->getOption('bvkeys');
			$accounts = self::allAccounts($this->settings);
			$this->rmkeys($pubkey);
			$this->setup();
			if ($accounts && is_array($accounts)) {
				unset($accounts[$pubkey]);
				$this->update($accounts);
				return true;
			}
			return false;
		}

		public function doesAccountExists($pubkey) {
			$accounts = self::allAccounts($this->settings);
			return array_key_exists($pubkey, $accounts);
		}

		public function update($accounts) {
			$this->settings->updateOption('bvAccounts', $accounts);
		}
	}
endif;