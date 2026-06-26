<?php

namespace App\Controllers;

use Core\Controller;

class ContactController extends Controller
{
    public function index(): void
    {
        $flash = $this->getFlash();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                $this->setFlash('error', 'Invalid request. Please try again.');
                header('Location: ' . url('contact/index'));
                exit;
            }

            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (!$name || !$email || !$message) {
                $flash = ['error' => 'Please fill in all required fields.'];
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $flash = ['error' => 'Please enter a valid email address.'];
            } else {
                $this->setFlash('success', 'Thank you! Your message has been received. We\'ll get back to you within 24 hours.');
                header('Location: ' . url('contact/index'));
                exit;
            }
        }

        $this->render('contact.index', [
            'pageTitle' => 'Contact Us – ' . APP_NAME,
            'flash'     => $flash,
        ]);
    }
}
