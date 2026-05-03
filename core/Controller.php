<?php
// core/Controller.php

class Controller {
    // Load model
    public function model($model) {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

    // Load view
    public function view($view, $data = []) {
        if (file_exists(APPROOT . '/views/' . $view . '.php')) {
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die('Vue introuvable : ' . $view);
        }
    }
    
    // Redirect
    public function redirect($url) {
        header('Location: ' . URLROOT . '/' . $url);
        exit();
    }
}
