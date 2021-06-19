<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVCallbackRequest')) :
	class BVCallbackRequest {
		public $params;
		public $method;
		public $wing;
		public $is_afterload;
		public $is_admin_ajax;
		public $is_debug;
		public $is_recovery;

		public function __construct($params) {
			$this->params = $params;
			$this->wing = $this->params['wing'];
			$this->method = $this->params['bvMethod'];
			$this->is_afterload = array_key_exists('afterload', $this->params);
			$this->is_admin_ajax = array_key_exists('adajx', $this->params);
			$this->is_debug = array_key_exists('bvdbg', $this->params);
			$this->is_recovery = array_key_exists('bvrcvr', $this->params);
		}

		public function isAPICall() {
			return array_key_exists('apicall', $this->params);
		}

		public function respInfo() {
			$info = array(
				"requestedsig" => $this->params['sig'],
				"requestedtime" => intval($this->params['bvTime']),
				"requestedversion" => $this->params['bvVersion']
			);
			if ($this->is_debug) {
				$info["inreq"] = $this->params;
			}
			if ($this->is_admin_ajax) {
				$info["adajx"] = true;
			}
			if ($this->is_afterload) {
				$info["afterload"] = true;
			}
			return $info;
		}

		public function processParams() {
			$params = $this->params;
			if (array_key_exists('obend', $params) && function_exists('ob_end_clean'))
				@ob_end_clean();
			if (array_key_exists('op_reset', $params) && function_exists('output_reset_rewrite_vars'))
				@output_reset_rewrite_vars();
			if (array_key_exists('binhead', $params)) {
				header("Content-type: application/binary");
				header('Content-Transfer-Encoding: binary');
			}
			if (array_key_exists('concat', $params)) {
				foreach ($params['concat'] as $key) {
					$concated = '';
					$count = intval($params[$key]);
					for ($i = 1; $i <= $count; $i++) {
						$concated .= $params[$key."_bv_".$i];
					}
					$params[$key] = $concated;
				}
			}
			if (array_key_exists('b64', $params)) {
				foreach ($params['b64'] as $key) {
					if (is_array($params[$key])) {
						$params[$key] = array_map('base64_decode', $params[$key]);
					} else {
						$params[$key] = base64_decode($params[$key]);
					}
				}
			}
			if (array_key_exists('unser', $params)) {
				foreach ($params['unser'] as $key) {
					$params[$key] = json_decode($params[$key], TRUE);
				}
			}
			if (array_key_exists('b642', $params)) {
				foreach ($params['b642'] as $key) {
					if (is_array($params[$key])) {
						$params[$key] = array_map('base64_decode', $params[$key]);
					} else {
						$params[$key] = base64_decode($params[$key]);
					}
				}
			}
			if (array_key_exists('dic', $params)) {
				foreach ($params['dic'] as $key => $mkey) {
					$params[$mkey] = $params[$key];
					unset($params[$key]);
				}
			}
			if (array_key_exists('clacts', $params)) {
				foreach ($params['clacts'] as $action) {
					remove_all_actions($action);
				}
			}
			if (array_key_exists('clallacts', $params)) {
				global $wp_filter;
				foreach ( $wp_filter as $filter => $val ){
					remove_all_actions($filter);
				}
			}
			if (array_key_exists('memset', $params)) {
				$val = intval(urldecode($params['memset']));
				@ini_set('memory_limit', $val.'M');
			}
			return $params;
		}
	}
endif;