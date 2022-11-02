<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Honeypot extends BaseConfig
{

	/**
	 * Makes Honeypot visible or not to human
	 *
	 * @var boolean
	 */
	public $hidden = true;
	/**
	 * Honeypot Label Content
	 *
	 * @var string
	 */
	public $label = 'required';

	/**
	 * Honeypot Field Name
	 *
	 * @var string
	 */
	public $name = 'your_name';

	/**
	 * Honeypot HTML Template
	 *
	 * @var string
	 */
	public $template = '<label>{label}</label><input type="text" name="{name}" value=""/>';
}
