<?php

namespace App\Controllers\Auth;

use Core\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;

class LoginController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /** GET /auth/login */
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect(!empty($_SESSION['is_admin']) ? 'admin/dashboard' : '');
        }
        $this->render('auth.login', ['flash' => $this->getFlash()], 'auth');
    }

    /** POST /auth/login */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
        }

        $email    = trim($this->post('email', ''));
        $password = $this->post('password', '');

        if (!$email || !$password) {
            $this->flash('error', 'Email and password are required.');
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['Password'])) {
            $this->flash('error', 'Invalid email or password.');
            $this->redirect('auth/login');
        }

        // Check if admin first
        $adminModel = new Admin();
        $admin      = $adminModel->findWithUser((int) $user['User_ID']);

        if ($admin) {
            // Admin login — no status restriction
            $_SESSION['user_id']  = $user['User_ID'];
            $_SESSION['name']     = $user['Fname'] . ' ' . $user['Lname'];
            $_SESSION['email']    = $user['Email'];
            $_SESSION['avatar']   = $user['Profile_Img'];
            $_SESSION['is_admin'] = true;
            $adminModel->touchLastLogin((int) $user['User_ID']);
            $this->redirect('admin/dashboard');
        }

        // ── Customer login — check account status ───────────────────────────
        $customerModel  = new Customer();
        $customerRecord = $customerModel->findStatusById((int) $user['User_ID']);

        if ($customerRecord) {
            $status   = $customerRecord['Account_Status'];
            $banUntil = $customerRecord['Ban_Until'];

            // If status is Banned but ban has already expired → auto-lift ban
            if ($status === 'Banned' && $banUntil !== null && strtotime($banUntil) < time()) {
                $customerModel->setStatus((int) $user['User_ID'], 'Active', null);
                $status = 'Active';
            }

            if ($status === 'Suspended') {
                $this->flash('error', 'Your account has been suspended. Please contact support.');
                $this->redirect('auth/login');
            }

            if ($status === 'Banned') {
                if ($banUntil !== null) {
                    $dateFormatted = date('d M Y', strtotime($banUntil));
                    $this->flash(
                        'banned',
                        'Your account has been banned and will be automatically lifted on ' . $dateFormatted . '.'
                    );
                } else {
                    $this->flash('banned', 'Your account has been permanently banned. Please contact support.');
                }
                $this->redirect('auth/login');
            }
        }

        // All clear — set session
        $_SESSION['user_id']     = $user['User_ID'];
        $_SESSION['name']        = $user['Fname'] . ' ' . $user['Lname'];
        $_SESSION['email']       = $user['Email'];
        $_SESSION['avatar']      = $user['Profile_Img'];
        $_SESSION['is_admin']    = false;
        $_SESSION['customer_id'] = $user['User_ID'];
        $this->redirect('');
    }

    /** GET /auth/logout */
    public function logout(): void
    {
        session_destroy();
        $this->redirect('auth/login');
    }
}
