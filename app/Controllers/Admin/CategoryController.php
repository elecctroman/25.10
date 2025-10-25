<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct(View $view, Session $session, Security $security)
    {
        parent::__construct($view, $session, $security);
    }

    public function index(): void
    {
        view('admin/categories', [
            'title' => 'Kategoriler',
            'categories' => Category::all(),
            'tree' => Category::tree(),
            'flash' => $this->session->flash('message'),
            'csrf' => $this->security->csrfToken(),
        ]);
    }

    public function create(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/categories');
        }
        $errors = Validator::required($_POST, [
            'name' => 'Kategori adı zorunludur.',
        ]);
        if ($errors) {
            $this->session->flash('message', ['type' => 'danger', 'text' => implode(' ', $errors)]);
            $this->redirect('/admin/categories');
        }
        Category::create([
            'parent_id' => $_POST['parent_id'] !== '' ? (int) $_POST['parent_id'] : null,
            'name' => $_POST['name'],
            'slug' => strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $_POST['name'])),
            'status' => $_POST['status'] ?? 'active',
        ]);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Kategori oluşturuldu.']);
        $this->redirect('/admin/categories');
    }

    public function update(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/categories');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Kategori bulunamadı.']);
            $this->redirect('/admin/categories');
        }
        Category::update($id, [
            'parent_id' => $_POST['parent_id'] !== '' ? (int) $_POST['parent_id'] : null,
            'name' => $_POST['name'],
            'slug' => strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $_POST['name'])),
            'status' => $_POST['status'] ?? 'active',
        ]);
        $this->session->flash('message', ['type' => 'success', 'text' => 'Kategori güncellendi.']);
        $this->redirect('/admin/categories');
    }

    public function delete(): void
    {
        if (!$this->security->verifyCsrf($_POST['_token'] ?? '')) {
            $this->session->flash('message', ['type' => 'danger', 'text' => 'Geçersiz oturum.']);
            $this->redirect('/admin/categories');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            Category::delete($id);
            $this->session->flash('message', ['type' => 'success', 'text' => 'Kategori silindi.']);
        }
        $this->redirect('/admin/categories');
    }
}
