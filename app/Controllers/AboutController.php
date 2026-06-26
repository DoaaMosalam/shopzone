<?php
// This file is part of the PHP MVC Framework package.

namespace App\Controllers;

use Core\Controller;

class AboutController extends Controller
{
    public function index(): void
    {
        $this->render('about.index', [
            'pageTitle' => 'About Us – ' . APP_NAME,
            'flash'     => $this->getFlash(),
        ]);
    }
}
