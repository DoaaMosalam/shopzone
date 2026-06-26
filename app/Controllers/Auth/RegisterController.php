<?php

namespace App\Controllers\Auth;

use Core\Controller;
use App\Models\User;
use App\Models\Customer;

class RegisterController extends Controller
{
    private User     $userModel;
    private Customer $customerModel;

    public function __construct()
    {
        $this->userModel     = new User();
        $this->customerModel = new Customer();
    }

    /** GET /auth/register */
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('');
        }
        $this->render('auth.register', ['flash' => $this->getFlash()], 'auth');
    }

    /** POST /auth/register */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/register');
        }

        $fname    = trim($this->post('fname', ''));
        $lname    = trim($this->post('lname', ''));
        $email    = trim($this->post('email', ''));
        $password = $this->post('password', '');
        $confirm  = $this->post('confirm_password', '');
        $gender   = $this->post('gender', 'Male');
        $phone    = trim($this->post('phone', ''));

        // Validation
        $errors = [];
        if (!$fname)                              $errors[] = 'First name is required.';
        if (!$lname)                              $errors[] = 'Last name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
        if (strlen($password) < 8)                $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)               $errors[] = 'Passwords do not match.';

        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email already registered.';
        }

        if ($errors) {
            $this->flash('error', implode('<br>', $errors));
            $this->redirect('auth/register');
        }

        // Create User record
        $userId = $this->userModel->register([
            'Fname'    => $fname,
            'Lname'    => $lname,
            'Gender'   => $gender,
            'Email'    => $email,
            'Password' => $password,
        ]);

        // Create Customer record
        $this->customerModel->insert([
            'Customer_ID'    => $userId,
            'Account_Status' => 'Active',
        ]);

        // Add phone if provided
        if ($phone) {
            $this->userModel->addPhone($userId, $phone);
        }

        $this->flash('success', 'Registration successful! Please log in.');
        $this->redirect('auth/login');
    }
}
