<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\User;
use App\Models\Admin;

class ProfileController extends Controller
{
    private User  $userModel;
    private Admin $adminModel;

    public function __construct()
    {
        $this->userModel  = new User();
        $this->adminModel = new Admin();
    }

    /** GET /admin/profile/index */
    public function index(): void
    {
        $this->requireAdmin();

        $admin = $this->adminModel->findWithUser((int) $_SESSION['user_id']);

        $this->render('admin.profile.index', [
            'admin' => $admin,
            'flash' => $this->getFlash(),
        ], 'admin');
    }

    /** POST /admin/profile/update */
    public function update(): void
    {
        $this->requireAdmin();

        $userId = (int) $_SESSION['user_id'];

        $userData = [
            'Fname' => trim($this->post('fname', '')),
            'Mname' => trim($this->post('mname', '')),
            'Lname' => trim($this->post('lname', '')),
        ];

        if ($userData['Fname'] && $userData['Lname']) {
            $this->userModel->update($userData, 'User_ID = ?', [$userId]);
            $_SESSION['name'] = trim($userData['Fname'] . ' ' . $userData['Lname']);
        }

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $path = $this->uploadAvatar($_FILES['avatar'], $userId);
            if ($path) {
                $this->userModel->updateProfileImage($userId, $path);
                $_SESSION['avatar'] = $path;
            }
        }

        $this->flash('success', 'Profile updated successfully.');
        $this->redirect('admin/profile/index');
    }

    /** POST /admin/profile/change-password */
    public function changePassword(): void
    {
        $this->requireAdmin();

        $userId  = (int) $_SESSION['user_id'];
        $current = $this->post('current_password', '');
        $new     = $this->post('new_password', '');
        $confirm = $this->post('confirm_password', '');

        $user = $this->userModel->find($userId);

        if (!$this->userModel->verifyPassword($current, $user['Password'])) {
            $this->flash('error', 'Current password is incorrect.');
            $this->redirect('admin/profile/index');
        }

        if (strlen($new) < 8) {
            $this->flash('error', 'New password must be at least 8 characters.');
            $this->redirect('admin/profile/index');
        }

        if ($new !== $confirm) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect('admin/profile/index');
        }

        $this->userModel->update(
            ['Password' => $this->userModel->hashPassword($new)],
            'User_ID = ?',
            [$userId]
        );

        $this->flash('success', 'Password changed successfully.');
        $this->redirect('admin/profile/index');
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
        $dir  = STORAGE_PATH . '/users';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return move_uploaded_file($file['tmp_name'], $dir . '/' . $name) ? 'users/' . $name : false;
    }
}
