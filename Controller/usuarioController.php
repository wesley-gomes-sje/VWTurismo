<?php
require_once './Model/usuarioModel.php';
require_once './View/cadUsuarioView.php';

class usuarioController
{
    public function __construct()
    { }
    public function preencheCampos($mensagem = '')
    {
        $usuarioView = new cadUsuarioView();
        $usuarioView->formulario($mensagem);
    }

    public function cadUsuario()
    {
        $nomeUsuario = filter_input(INPUT_POST, 'nomeUsuario', FILTER_SANITIZE_STRING);
        $emailUsuario = filter_input(INPUT_POST, 'emailUsuario', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
        $senhaConfirma = filter_input(INPUT_POST, 'senhaConfirma', FILTER_SANITIZE_STRING);

        if (!$nomeUsuario || !$emailUsuario || !$senha || !$senhaConfirma) {

            return $this->preencheCampos('Preencha todos os campos!');
        }
       
        $usuarioModel = new Usuario();
        $usuarioModel->setnomeUsuario($nomeUsuario);
        $usuarioModel->setemailUsuario($emailUsuario);
        $usuarioModel->setsenha($senha);

        if ($senha == $senhaConfirma) {
            if ($usuarioModel->verUsuario($emailUsuario) == true) {
                return $this->preencheCampos('Usuario já cadastrado.');
            } else {
                $retorno = $usuarioModel->cadUsuario();
                if ($retorno == true) {
                    return $this->preencheCampos('Usuario cadastrado.');
                } else {
                    return $this->preencheCampos('Erro ao cadastrar.');
                }
            }
        } else{
            return $this->preencheCampos('Senhas não conferem.');
        }
    }
}
