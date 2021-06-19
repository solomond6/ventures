<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVAccountCallback')) :
class BVAccountCallback extends BVCallbackBase {
	public $account;
	public $settings;

	public function __construct($callback_handler) {
		$this->account = $callback_handler->account;
		$this->settings = $callback_handler->settings;
	}

	function process($request) {
		$params = $request->params;
		$account = $this->account;
		switch ($request->method) {
		case "addkeys":
			$resp = array("status" => $account->addKeys($params['public'], $params['secret']));
			break;
		case "updatekeys":
			$resp = array("status" => $account->updateKeys($params['public'], $params['secret']));
			break;
		case "rmkeys":
			$resp = array("status" => $account->rmKeys($params['public']));
			break;
		case "updt":
			$info = array();
			$info['email'] = $params['email'];
			$info['url'] = $params['url'];
			$info['pubkey'] = $params['pubkey'];
			$account->add($info);
			$resp = array("status" => $account->doesAccountExists($params['pubkey']));
			break;
		case "disc":
			$account->remove($params['pubkey']);
			$resp = array("status" => !$account->doesAccountExists($params['pubkey']));
		case "fetch":
			$resp = array("status" => WPEAccount::allAccounts($this->settings));
			break;
		default:
			$resp = false;
		}
		return $resp;
	}
}
endif;