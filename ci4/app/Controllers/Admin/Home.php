<?php

namespace App\Controllers\Admin;

class Home extends BaseController
{
    /**
     * Chama a view principal
     *
     * @return void
     */
    public function index()
    {
        echo view('admin/home/index');
    }
}
