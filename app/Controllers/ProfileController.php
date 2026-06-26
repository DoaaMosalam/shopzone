<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Customer;

class ProfileController extends Controller
{
    private User     $userModel;
    private Customer $customerModel;

    public function __construct()
    {
        $this->userModel     = new User();
        $this->customerModel = new Customer();
    }

    /** GET /profile/index */
    public function index(): void
    {
        $this->requireCustomer();

        $customer = $this->customerModel->findWithUser((int) $_SESSION['customer_id']);
        $phones   = $this->userModel->getPhones((int) $_SESSION['user_id']);

        $this->render('profile.index', [
            'customer' => $customer,
            'phones'   => $phones,
            'flash'    => $this->getFlash(),
        ]);
    }

    /** POST /profile/update */
    public function update(): void
    {
        $this->requireCustomer();

        $userId     = (int) $_SESSION['user_id'];
        $customerId = (int) $_SESSION['customer_id'];

        // Update User fields
        $userData = [
            'Fname'         => trim($this->post('fname', '')),
            'Mname'         => trim($this->post('mname', '')),
            'Lname'         => trim($this->post('lname', '')),
            'Date_of_Birth' => $this->post('dob', null) ?: null,
        ];
        $this->userModel->update($userData, 'User_ID = ?', [$userId]);

        // Update address
        $this->customerModel->updateAddress($customerId, [
            'City'     => trim($this->post('city', '')),
            'Street'   => trim($this->post('street', '')),
            'State'    => trim($this->post('state', '')),
            'Zip_Code' => trim($this->post('zip', '')),
        ]);

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $path = $this->uploadAvatar($_FILES['avatar'], $userId);
            if ($path) {
                $this->userModel->updateProfileImage($userId, $path);
                $_SESSION['avatar'] = $path;
            }
        }

        $this->flash('success', 'Profile updated.');
        $this->redirect('profile/index');
    }

    /** POST /profile/change-password */
    public function changePassword(): void
    {
        $this->requireCustomer();

        $userId  = (int) $_SESSION['user_id'];
        $current = $this->post('current_password', '');
        $new     = $this->post('new_password', '');
        $confirm = $this->post('confirm_password', '');

        $user = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($current, $user['Password'])) {
            $this->flash('error', 'Current password is incorrect.');
            $this->redirect('profile/index');
        }

        if (strlen($new) < 8) {
            $this->flash('error', 'New password must be at least 8 characters.');
            $this->redirect('profile/index');
        }

        if ($new !== $confirm) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect('profile/index');
        }

        $this->userModel->update(
            ['Password' => $this->userModel->hashPassword($new)],
            'User_ID = ?',
            [$userId]
        );

        $this->flash('success', 'Password changed successfully.');
        $this->redirect('profile/index');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function uploadAvatar(array $file, int $userId): string|false
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $mime    = mime_content_type($file['tmp_name']);

        if (!in_array($mime, $allowed, true)) return false;
        if ($file['size'] > 2 * 1024 * 1024)  return false;

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'user_' . $userId . '_' . time() . '.' . $ext;
        $dest = STORAGE_PATH . '/users/' . $name;

        if (!is_dir(STORAGE_PATH . '/users')) {
            mkdir(STORAGE_PATH . '/users', 0755, true);
        }

        return move_uploaded_file($file['tmp_name'], $dest) ? 'users/' . $name : false;
    }
}
