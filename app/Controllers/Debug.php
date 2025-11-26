<?php namespace App\Controllers;

class Debug extends BaseController
{
    public function fcpath()
    {
        echo '<pre>';
        echo FCPATH;
        echo '</pre>';
    }
}