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

    public function Open($message = '', $data = [])
    {
        $data = $this->cityModel->all();
        return $this->cityView->registerCity($message, $data);
    }

    public function register()
    {
        $name = $this->sanitizeString($_POST['name'] ?? '');

        if (!$name) {

            return $this->Open('Preencha todos os dados.');
        }

        $this->cityModel->setName($name);

        if (!$this->cityModel->register()) {
            return $this->Open('Erro ao registrar cidade');
        }

        $data = $this->cityModel->all();
        return $this->Open('Cidade registrada com sucesso!', $data);
    }

    public function delete()
    {
        $id = $this->sanitizeString($_GET['id'] ?? '');

        if (!$this->cityModel->delete($id)) {
            return $this->Open('Erro ao excluir cidade.');
        };

        return $this->Open('Cidade excluida.');
    }

    public function show()
    {
        $id = $this->sanitizeString($_GET['id'] ?? '');
        if (!$id) {
            return $this->Open('Erro ao editar cidade.');
        }
        $data = $this->cityModel->show($id);
        
        return $this->cityView->editCity($data);
    }
    
    public function edit()
    {
        $id = $this->sanitizeString($_GET['id'] ?? '');
        $name = $this->sanitizeString($_POST['name'] ?? '');

        if (!$name) {
            return $this->Open('Preencha todos os dados.');
        }

        if (!$this->cityModel->edit($id, $name)) {
            return $this->Open('Erro ao editar cidade.');
        }

        $data = $this->cityModel->all();
        return $this->Open('Cidade editada com sucesso!', $data);
    }

    private function sanitizeString($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
