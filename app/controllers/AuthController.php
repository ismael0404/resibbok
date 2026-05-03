<?php
// app/controllers/AuthController.php

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'role_id' => trim($_POST['role_id'] ?? '') == 'owner' ? 2 : 3,
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'confirm_password' => trim($_POST['confirm_password'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'status' => trim($_POST['role_id'] ?? '') == 'owner' ? 'pending' : 'active',
                'email_err' => '', 'password_err' => '', 'confirm_password_err' => '', 'name_err' => ''
            ];

            // Validations
            if (empty($data['first_name']) || empty($data['last_name'])) $data['name_err'] = 'Le nom est requis';
            if (empty($data['email'])) {
                $data['email_err'] = 'Veuillez entrer un email';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Cet email est déjà utilisé';
            }
            if (empty($data['password'])) {
                $data['password_err'] = 'Mot de passe requis';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Minimum 6 caractères';
            }
            if ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Les mots de passe ne correspondent pas';
            }

            if (empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) && empty($data['name_err'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                if ($this->userModel->register($data)) {
                    if ($data['role_id'] == 2) {
                        $_SESSION['flash_msg'] = "Inscription réussie ! Votre compte est en attente de validation.";
                    } else {
                        $_SESSION['flash_msg'] = "Inscription réussie ! Connectez-vous maintenant.";
                    }
                    $this->redirect('auth/login');
                } else {
                    die('Erreur lors de l\'inscription');
                }
            } else {
                $this->view('auth/register', $data);
            }
        } else {
            $data = ['first_name'=>'','last_name'=>'','email'=>'','password'=>'','confirm_password'=>'','phone'=>'',
                     'email_err'=>'','password_err'=>'','confirm_password_err'=>'','name_err'=>''];
            $this->view('auth/register', $data);
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data = ['email' => trim($_POST['email'] ?? ''), 'password' => trim($_POST['password'] ?? ''), 'email_err' => '', 'password_err' => ''];

            if (empty($data['email'])) $data['email_err'] = 'Entrez votre email';
            if (empty($data['password'])) $data['password_err'] = 'Entrez votre mot de passe';
            if (!$this->userModel->findUserByEmail($data['email']) && empty($data['email_err'])) {
                $data['email_err'] = 'Aucun compte trouvé';
            }

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                if ($loggedInUser) {
                    // Vérifier suspension
                    if ($loggedInUser->status == 'suspended') {
                        $data['email_err'] = 'Votre compte a été suspendu. Contactez le support.';
                        $this->view('auth/login', $data);
                        return;
                    }
                    if ($loggedInUser->status == 'rejected' && $loggedInUser->role_id != 2) {
                        $data['email_err'] = 'Votre compte a été rejeté.';
                        $this->view('auth/login', $data);
                        return;
                    }
                    // IMPORTANT: Les propriétaires pending PEUVENT se connecter
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Mot de passe incorrect';
                    $this->view('auth/login', $data);
                }
            } else {
                $this->view('auth/login', $data);
            }
        } else {
            $data = ['email'=>'','password'=>'','email_err'=>'','password_err'=>''];
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
        $_SESSION['user_role'] = $user->role_id;
        $_SESSION['user_status'] = $user->status;
        $_SESSION['user_avatar'] = $user->avatar;

        if ($user->role_id == 1) {
            $this->redirect('admin/dashboard');
        } elseif ($user->role_id == 2) {
            $this->redirect('owner/dashboard');
        } else {
            $this->redirect('pages/index');
        }
    }

    public function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['user_role'], $_SESSION['user_status'], $_SESSION['user_avatar']);
        session_destroy();
        $this->redirect('auth/login');
    }
}
