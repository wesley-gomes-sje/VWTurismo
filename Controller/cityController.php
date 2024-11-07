<?php
require_once './View/menuView.php';
require_once './Model/cityModel.php';
class cityController
{
    private $cityModel;
    private $cityView;

    public function __construct()
    {
        $this->cityModel = new City();
        $this->cityView = new menuView();
    }

    public function open($message = '', $data = [])
    {
        $data = $this->cityModel->all();
        return $this->cityView->registerCity($message, $data);
    }

    public function register()
    {
        $name = $this->sanitizeString($_POST['name'] ?? '');

        if (!$name) {

            return $this->open('Preencha todos os dados.');
        }

        $this->cityModel->setName($name);

        if (!$this->cityModel->register()) {
            return $this->open('Erro ao registrar cidade');
        }

        $data = $this->cityModel->all();
        return $this->open('Cidade registrada com sucesso!', $data);
    }

    public function delete()
    {
        $id = $this->sanitizeString($_GET['id'] ?? '');

        if (!$this->cityModel->delete($id)) {
            return $this->open('Erro ao excluir cidade.');
        };

        return $this->open('Cidade excluida.');
    }

    public function show()
    {
        $id = $this->sanitizeString($_GET['id'] ?? '');
        if (!$id) {
            return $this->open('Erro ao editar cidade.');
        }
        $data = $this->cityModel->show($id);
        
        return $this->cityView->editCity($data);
    }
    
    public function all()
    {
        $data = $this->cityModel->all();
        return $data;
    }
    
    public function edit()
    {
        $id = $this->sanitizeString($_GET['id'] ?? '');
        $name = $this->sanitizeString($_POST['name'] ?? '');

        if (!$name) {
            return $this->open('Preencha todos os dados.');
        }

        if (!$this->cityModel->edit($id, $name)) {
            return $this->open('Erro ao editar cidade.');
        }

        $data = $this->cityModel->all();
        return $this->open('Cidade editada com sucesso!', $data);
    }

    private function sanitizeString($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
