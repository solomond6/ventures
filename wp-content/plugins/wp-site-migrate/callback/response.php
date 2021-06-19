<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVCallbackResponse')) :

	class BVCallbackResponse extends BVCallbackBase {
		public $status;

		public function __construct() {
			$this->status = array("blogvault" => "response");
		}

		public function addStatus($key, $value) {
			$this->status[$key] = $value;
		}

		public function addArrayToStatus($key, $value) {
			if (!isset($this->status[$key])) {
				$this->status[$key] = array();
			}
			$this->status[$key][] = $value;
		}

		public function terminate($resp = array(), $req_params) {
			$resp = array_merge($this->status, $resp);
			$resp["signature"] = "Blogvault API";
			$response = "bvbvbvbvbv".serialize($resp)."bvbvbvbvbv";
			if (array_key_exists('bvb64resp', $req_params)) {
				$chunk_size = array_key_exists('bvb64cksize', $req_params) ? intval($req_params['bvb64cksize']) : false;
				$response = "bvb64bvb64".$this->base64Encode($response, $chunk_size)."bvb64bvb64";
			}
			die($response);

			exit;
		}
	}
endif;