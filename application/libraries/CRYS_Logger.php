<?php

class CRYS_Logger {
	private $CI;

	private $model;

	public function __construct($config=array()) {
		if (empty($config))
			show_error('CRYS_Logger config cannot be empty');
		$this->model = $config['model'];
	}

	public function log($data=array()) {
		$log = $this->{$this->model}->create($data);
		$this->{$this->model}->save($log);
	}
}
