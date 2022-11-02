<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		echo view('home/index', [
			'mes' => date('m'),
			'ano' => date('Y')
		]);
	}

	//--------------------------------------------------------------------

}
