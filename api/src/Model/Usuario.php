<?php

namespace WebService\Model;

use WebService\Util\JWTAuth;

class Usuario extends AppModel
{
    public function login(string $login, string $senha)
    {
        $usuario = $this->getUsuario($login, $senha);
        if ($usuario['code'] === 1) {
            $token = [
                'dataCriacao' => strtotime('now'),
                'intIdUsuario' => $usuario['usuario']['id_usuario'],
                'strNome' => $usuario['usuario']['nome_usuario'],
                'strLogin' => $usuario['usuario']['login_usuario'],
                'intNivel' => $usuario['usuario']['nivelAcesso_usuario']
            ];

            return [
                'token' => JWTAuth::encode($token),
                'intIdUsuario' => $usuario['usuario']['id_usuario'],
                'strNome' => $usuario['usuario']['nome_usuario'],
                'intNivel' => $usuario['usuario']['nivelAcesso_usuario'],
                'convites' => $usuario['convite']
            ];
        } else {
            return [
                'code' => 0,
                'message' => 'Login ou Senha incorretos!',
            ];
        }
    }

    private function getUsuario(string $login, string $senha)
    {
        $sql = 'SELECT nome_usuario, login_usuario, ativo_usuario, id_usuario, senha_usuario, nivelAcesso_usuario FROM tab_usuario where login_usuario = :login order by id_usuario limit 1';
        $usuario = $this->select($sql, [':login' => $login]);
        if ($usuario === false && $usuario[0]['senha_usuario'] === false) {
            return ['code' => 0, 'message' => 'Usuário não encontrado!'];
        } elseif ($usuario[0]['ativo_usuario'] === 'true') {
            return ['code' => 0, 'message' => 'Este usuário está inativo no sistema!'];
        } elseif (!password_verify($senha, $usuario[0]['senha_usuario'])) {
            return ['code' => 0, 'message' => 'Senha incorreta!'];
        } else {
            // verifica convite Grupo
            $sql = 'SELECT grupo_convite FROM tab_convite where email_convite = :strEmail';
            $convites = $this->select($sql, [':strEmail' => $usuario[0]['email']]);
            if ($convites === false && $convites[0]['grupo_convite'] === false) {
                return ['code' => 1, 'usuario' => $usuario[0]];
            } else {
                return ['code' => 1, 'usuario' => $usuario[0], 'convite' => $convites[0]['grupo_convite']];
            }
        }
    }

    private function getUsuarioSenha(string $idUsuario)
    {
        $sql = 'SELECT senha_usuario FROM tab_usuario where id_usuario = :idUsuario order by id_usuario limit 1';
        $usuario = $this->select($sql, [':idUsuario' => $idUsuario]);
        if ($usuario === false && $usuario[0]['senha_usuario'] === false) {
            return ['code' => 0, 'message' => 'Usuário não encontrado!'];
        } else {
            return ['code' => 1, 'usuario' => $usuario[0]];
        }
    }

    private function setUsuarioSenha(string $senha, string $idUsuario)
    {
        $sql = 'UPDATE
                    tab_usuario
                SET
                    senha_usuario = :senha
                WHERE
                    id_usuario = :idUsuario';
        $arrParametros = [
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':idUsuario' => $idUsuario,
        ];
        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false){
            return ['code' => 0];
        }else{
            return ['code' => 1];
        }
    }

    public function alterarSenha($intIdUsuario, $objUsuario)
    {
        $usuarioInfo = $this->getUsuarioSenha($intIdUsuario);
        if ($usuarioInfo['code'] === 1){
            if (strlen($objUsuario['newPasswd']) < 6) {
                return ['code' => 0, 'message' => 'A senha digitada deve conter ao menos 6 caracteres!'];
            } elseif (!password_verify($objUsuario['oldPasswd'], $usuarioInfo['usuario']['senha'])) {
                return ['code' => 0, 'message' => 'A senha atual informada está incorreta!'];
            } elseif (password_verify($objUsuario['newPasswd'], $usuarioInfo['usuario']['senha'])) {
                return ['code' => 0, 'message' => 'A senha atual e a nova senha são iguais!'];
            } elseif ($objUsuario['newPasswd'] !== $objUsuario['confirmPasswd']) {
                return ['code' => 0, 'message' => 'A confirmação da nova senha está incorreta!'];
            } else {
                $usuarioUpdate = $this->setUsuarioSenha($objUsuario['newPasswd'], $intIdUsuario);
                if ($usuarioUpdate['code'] === 1){
                    return ['code' => 1, 'message' => 'Senha Alterada'];
                }else{
                    return ['code' => 0, 'message' => 'A Senha não foi alterada!'];
                }
            }
        } else {
            return ['code' => 0, 'message' => 'A Senha não foi alterada!'];
        }

    }

    // public function recuperarSenha($strEmail)
    // {
    //   $sql = 'SELECT id_usuario FROM tab_usuario where email_usuario = :strEmail order by id_usuario limit 1';
    //   $usuario = $this->select($sql, [':strEmail' => $strEmail]);
    //   if ($usuario === false && $usuario[0]['id_usuario'] === false) {
    //       return ['code' => 0, 'message' => 'Est email não está cadastrado no sistema'];
    //   } else {
    //       return ['code' => 1, 'usuario' => $usuario[0]];
    //
    //       $sql = 'UPDATE TAB_USUARIO SET senhaRec_usuario = 'teste' where id_usuario = :intUsuario';
    //       $usuario = $this->execute($sql, [':intUsuario' => $usuario[0]['id_usuario']]);
    //       //envia email.
    //   }
    // }

    public function retornarTodosUsuarios()
    {
        $this->setConnection('mysql');
        $sql = 'SELECT * FROM tab_usuario';
        $objUsuario = $this->select($sql);
        if ($objUsuario === false) {
            return ['code' => 0, 'message' => 'Usuário não encontrado!'];
        } else {
            return ['code' => 1, 'usuarios' => $objUsuario];
        }
    }

    public function verificaRegistro($objUsuario)
    {
        $this->setConnection('mysql');
        $sql = 'Select Count(*) as intUsuarios from tab_usuario where login_usuario = :strLogin or nome_usuario = :strNome';
        $arrParametros = [
            ':strLogin' => $objUsuario['strLogin'],
            ':strNome' => $objUsuario['strNome'],
        ];
        $objTransacao = $this->select($sql, $arrParametros);
        if ($objTransacao === false) {
            return false;
        } else {
            if ($objTransacao[0]['intUsuarios'] == 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function registrarUsuario($objUsuario)
    {
        if ($this->verificaObj($objUsuario) === true) {
            return ['code' => 0, 'message' => 'Usuário não registrado, dados incompletos!'];
        }
        if ($objUsuario['strSenha'] !== $objUsuario['strSenhaConfirma']) {
            return ['code' => 0, 'message' => 'As senhas digitadas devem ser iguais!'];
        }
        if (strlen($objUsuario['strSenha']) < 6) {
            return ['code' => 0, 'message' => 'A senha digitada deve conter ao menos 6 caracteres!'];
        }
        if ($this->verificaRegistro($objUsuario) === false) {
            return ['code' => 0, 'message' => 'Não foi possivel cadastrar, este usuário já está cadastrado!'];
        }
        if ($objUsuario['blnAtivo'] === true) {
            $opcaoAtivo = 0;
        } else {
            $opcaoAtivo = 1;
        }
        $this->setConnection('mysql');
        $sql = 'INSERT INTO tab_usuario
                        (nome_usuario,
                        login_usuario,
                        email_usuario,
                        dataCriacao_usuario,
                        nivelAcesso_usuario,
                        senha_usuario)
                 VALUES (:strNome,
                         :strLogin,
                         :strEmail,
                         :dataCadastro,
                         :intNivel,
                         :strSenha)';
        $arrParametros = [
            ':strNome' => $objUsuario['strNome'],
            ':strLogin' => $objUsuario['strLogin'],
            ':strEmail' => $objUsuario['strEmail'],
            ':dataCadastro' => date('Y-m-d'),
            ':intNivel' => $objUsuario['intNivel'],
            ':strSenha' => password_hash($objUsuario['strSenha'], PASSWORD_DEFAULT)
        ];
        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Usuário não registrado!'];
        } else {
            return ['code' => 1, 'message' => 'Usuário registrado!'];
        }
    }

    public function atualizarUsuario($objUsuario)
    {
        if ($this->verificaObj($objUsuario) === true) {
            return ['code' => 0, 'message' => 'Usuário não registrado, dados incompletos!'];
        }
        if ($objUsuario['strSenha'] !== $objUsuario['strSenhaConfirma']) {
            return ['code' => 0, 'message' => 'As senhas digitadas devem ser iguais!'];
        }
        if (strlen($objUsuario['strSenha']) < 6) {
            return ['code' => 0, 'message' => 'A senha digitada deve conter ao menos 6 caracteres!'];
        }

        if ($objUsuario['blnAtivo'] === true) {
            $opcaoAtivo = 0;
        } else {
            $opcaoAtivo = 1;
        }
        $this->setConnection('mysql');
        $sql = 'UPDATE tab_usuario
                    SET
                        nome_usuario = :strNome,
                        email_usuario = :strEmail,
                        nivelAcesso_usuario = :intNivel,
                        senha_usuario = :strSenha,
                        ativo_usuario = :blnAtivo
                WHERE id_usuario = :intUsuario';
        $arrParametros = [
            ':intUsuario' => $objUsuario['intUsuario'],
            ':strNome' => $objUsuario['strNome'],
            ':strEmail' => $objUsuario['strEmail'],
            ':intNivel' => $objUsuario['intNivel'],
            ':strSenha' => password_hash($objUsuario['strSenha'], PASSWORD_DEFAULT),
            ':blnAtivo' => $objUsuario['blnAtivo']
        ];

        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Usuário não alterado!'];
        } else {
            return ['code' => 1, 'message' => 'Usuário alterado!'];
        }
    }

    public function verificaTransacoes($objUsuario)
    {
        $this->setConnection('mysql');
        //verifica se o usuario possui transacoes
        $sql = 'Select Count(*) as totalVendedores from mov_transacoes where id_vendedor = :intUsuario';
        $arrParametros = [
            ':intUsuario' => $objUsuario['intUsuario'],
        ];
        $objTransacao = $this->select($sql, $arrParametros);

        return $objTransacao;
        if ($objTransacao === false) {
            return false;
        } else {
            if ($objTransacao[0]['totalVendedores'] == 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}
