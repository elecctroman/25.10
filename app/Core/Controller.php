<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected View $view;
    protected Session $session;
    protected Security $security;

    public function __construct(View $view, Session $session, Security $security)
    {
        $this->view = $view;
        $this->session = $session;
        $this->security = $security;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
