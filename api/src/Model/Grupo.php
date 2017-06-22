<?php

namespace WebService\Model;
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
use PHPMailer;
class Grupo extends AppModel
{
    public function criarGrupo($objGrupo, $idUser)
    {
        $this->setConnection('mysql');
        $sql = 'INSERT INTO tab_grupo
                        (nome_grupo,
                        tipo_grupo,
                        ativo_grupo,
                        dataSorteio_grupo,
                        descricao_grupo,
                        admin_grupo,
                        dataCriacao_grupo,
                        faixaPreco_grupo)
                 VALUES (:strNome,
                         :intTipo,
                         :blnAtivo,
                         :dateSorteio,
                         :strDescricao,
                         :intAdmin,
                         :dataCriacao_grupo,
                         :fltPreco)';

        $arrParametros = [
            ':strNome' => $objGrupo['strNome'],
            ':intTipo' => $objGrupo['intTipo'],
            ':blnAtivo' => $objGrupo['blnAtivo'],
            ':dateSorteio' => $objGrupo['dateSorteio'],
            ':strDescricao' => $objGrupo['strDescricao'],
            ':intAdmin' => $idUser->intIdUsuario,
            ':dataCriacao_grupo' => date('Y-m-d'),
            ':fltPreco' => $objGrupo['fltPreco'],
        ];
        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
        } else {
            $sql3 = 'INSERT INTO tab_usuarioGrupo
                            (grupo_usuarioGrupo,
                            usuario_usuarioGrupo,
                            dataCriacao_usuarioGrupo)
                     VALUES (:intGrupo,
                             :intUsuario,
                             :dataAgora)';

            $arrParametros3 = [
                // ':intGrupo' =>  $objTransacao2[0][id_grupo],
                ':intGrupo' =>  $this->getConnection()->lastInsertId(),
                ':intUsuario' => $idUser->intIdUsuario,
                ':dataAgora' => date('Y-m-d'),
            ];
            // return $arrParametros3;
            $objTransacao3 = $this->execute($sql3, $arrParametros3);
            if ($objTransacao3 === false) {
                return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
            } else {
                return ['code' => 1, 'message' => 'Grupo criado com sucesso!'];
            }
        }
    }

    public function entrarGrupo($objGrupo, $idUsuario)
    {
        $this->setConnection('mysql');
        //verifica se o usuario já está no grupo
        $sqlVerifica = "SELECT count(usuario_usuarioGrupo) AS totalUser FROM tab_usuarioGrupo WHERE grupo_usuarioGrupo = :idGrupo AND usuario_usuarioGrupo = :idUsuario";
        $arrParametrosVerifica = [
            ':idGrupo' => $objGrupo['idGrupo'],
            ':idUsuario' => $idUsuario->intIdUsuario
        ];

        $objTransacao = $this->select($sqlVerifica, $arrParametrosVerifica);

        if($objTransacao[0]['totalUser'] == 0){
            $sql = 'INSERT INTO tab_usuarioGrupo
                            (grupo_usuarioGrupo,
                            usuario_usuarioGrupo,
                            dataCriacao_usuarioGrupo)
                     VALUES (:intGrupo,
                             :intUsuario,
                             :dataAgora)';
            $arrParametros = [
                 ':intGrupo' =>  $objGrupo['idGrupo'],
                 ':intUsuario' => $idUsuario->intIdUsuario,
                 ':dataAgora' => date('Y-m-d'),
            ];
            $objTransacao2 = $this->execute($sql, $arrParametros);
            if ($objTransacao2 === false) {
                return ['code' => 0, 'message' => 'Não foi possivel participar deste grupo'];
            } else {
                $sqlConvite = "UPDATE tab_convite set aceito_convite = '1' where email_convite = :strEmail";
                $arrParametrosConvite = [
                    ':strEmail' => $idUsuario->emailUsuario,
                ];
                $objTransacao3 = $this->execute($sqlConvite, $arrParametrosConvite);
                return ['code' => 1, 'message' => 'Agora você está participando deste grupo'];
            }
        }else{
            return ['code' => 0, 'message' => 'Você já está participando deste grupo.'];
        }

    }

    public function sairGrupo($objGrupo, $idUsuario)
    {
        $this->setConnection('mysql');
        $sql = 'UPDATE tab_usuarioGrupo
                    SET ativo_usuarioGrupo = "1"
                where
                    grupo_usuarioGrupo =:id_grupo,
                    usuario_usuarioGrupo = :id_usuario';
        $arrParametros = [
            ':id_grupo' => $objGrupo['strNome'],
            ':id_usuario' => $idUser->intIdUsuario
        ];
        $objTransacao2 = $this->execute($sql, $arrParametros);
        if ($objTransacao2 === false) {
            return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
        } else {
            return ['code' => 1, 'message' => 'Grupo criado com sucesso!'];
        }
    }

    public function verificaConvite($idUsuario)
    {
        $sql = 'SELECT DISTINCT grupo_convite FROM tab_convite where email_convite = :strEmail AND aceito_convite = 0';
        $convites = $this->select($sql, [':strEmail' => $usuario[0]['email_usuario']]);
        if ($convites === false) {
            return ['code' => 0, 'message' => 'Não existe nenhum convite em aberto.'];
        } else {
            $sql = 'SELECT Count() grupo_convite FROM tab_convite where email_convite = :strEmail AND aceito_convite = 0';
            return ['code' => 1, 'usuario' => $usuario[0], 'convite' => $convites];
        }
    }

    public function enviarConvite($objGrupo, $idUser)
    {
        $countConvites = 1;
        foreach($objGrupo['listMail'] as $value){
            $enviarSolicitacao = $this->enviaSolicitacao($value, $idUser->strNome);
            if($enviarSolicitacao == true){
                $sql = "INSERT INTO tab_convite (grupo_convite, email_convite, dataCriacao_convite) values (:idGrupo, :emailSorteado, :dataCriacao)";
                $arrParametros = [
                    ':idGrupo' => $objGrupo['idGrupo'],
                    ':emailSorteado' => $value,
                    ':dataCriacao' => date('Y-m-d')
                ];
                $objTransacao2 = $this->execute($sql, $arrParametros);
                $countConvites++;
            }
        }
        if($countConvites == 1){
            return ['code' => 1, 'message' => 'Houve um erro ao enviar os convites'];
        }elseif($countConvites == 2){
            return ['code' => 1, 'message' => 'O convite foi enviado com sucesso!'];
        }else{
            return ['code' => 1, 'message' => 'Os convites foram enviados com sucesso!'];
        }
    }

    public function enviaSolicitacao($emailCliente, $strRemetente)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->CharSet = 'UTF-8';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = 'fred.fmm@gmail.com';
        $mail->Password = 'xx';
        $mail->SMTPOptions = [
          'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
          ]

        ];
        // Define o remetente
        $mail->From = 'fred.fmm@gmail.com';
        $mail->FromName = $strRemetente;
        // Define os destinatário(s)
        $mail->AddAddress($emailCliente);
        $mail->IsHTML(true);
        $mail->Subject = 'Você recebeu um convite para participar do amigo secreto';
        $mail->Body = 'Olá, seu amigo '.$strRemetente.' convidou você para participar do amigo secreto. Acesse www.fred.com.br';

        $enviado = $mail->Send();

        $mail->ClearAllRecipients();
        $mail->ClearAttachments();

        if ($enviado) {
            return true;
        }
        return false;
        // if ($enviado) {
        //     return [
        //       'code' => 1,
        //       'message' => 'E-mail enviado com sucesso!'
        //     ];
        // }
        //
        // return [
        //   'code' => 0,
        //   'message' => 'Houve um erro ao enviar o e-mail',
        //   'erro' => $mail->ErrorInfo
        // ];
    }

    // public function sortear($objGrupo)
    // {
    //     $this->setConnection('mysql');
    //     $sql = 'SELECT * FROM
    //                 tab_grupoUsuarios
    //             where
    //                 inativo = 0 and id_grupo = :idGrupo';
    //     $arrParametros = [
    //         ':idGrupo' => $objGrupo['idGrupo']
    //     ];
    //     $objTransacao = $this->execute($sql, $arrParametros);
    //     if ($objTransacao === false) {
    //         return ['code' => 0, 'message' => 'Não foi possivel criar o grupo!'];
    //     } else {
    //         //for each VALUES
    //         for ($i = 1; $i < 2; $i++) {
    //         	$sorteado[$i] = $participantes[rand(0,$numParticipantes - 1)];
    //         	// Caso o ganhador já tenha saido, sorteia novamente.
    //         	if ($sorteado[3] == $sorteado[1] || $sorteado[3] == $sorteado[2]) {
    //         		--$i;
    //         	}
    //         }
    //         // send mail
    //     }
    // }

    public function tiposGrupo($objGrupo)
    {
        $this->setConnection('mysql');
        //verifica se o usuario possui transacoes
        $sql = 'Select * from tab_tiposGrupo where inativo = "0"';
        $arrParametros = [];
        $objTransacao = $this->select($sql, $arrParametros);

        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi recuperar os tipos!'];
        } else {
            return ['code' => 1, 'message' => $objTransacao[0]];
        }
    }

    public function retornarTodosGrupos()
    {
        $this->setConnection('mysql');
        $sql = 'SELECT * FROM tab_grupo';
        $objGrupo = $this->select($sql);
        if ($objGrupo === false) {
            return ['code' => 0, 'message' => 'Grupo não encontrado!'];
        } else {
            return ['code' => 1, 'usuarios' => $objGrupo];
        }
    }

    public function retornarGrupoId($objGrupo, $idUser)
    {
        $this->setConnection('mysql');
        $sql = 'SELECT * FROM tab_grupo where admin_grupo = :idUser and id_grupo = :intGrupo';
        $arrParametros = [
            ':idUser' => $idUser->intIdUsuario,
            ':intGrupo' =>$objGrupo['intIdGrupo']
        ];
        $objGrupo = $this->select($sql, $arrParametros);
        if ($objGrupo === false) {
            return ['code' => 0, 'message' => 'Grupo não encontrado!'];
        } else {
            return ['code' => 1, 'grupo' => $objGrupo];
        }
    }

    public function sortearGrupo($objGrupo, $idUser)
    {
        $this->setConnection('mysql');
        $intIdGrupo = $objGrupo['intIdGrupo'];
        $sql = 'SELECT COUNT(DISTINCT usuario_usuarioGrupo) as totalUsuarios FROM tab_usuarioGrupo where grupo_usuarioGrupo = :intGrupo';
        $arrParametros = [
            ':intGrupo' =>$intIdGrupo
        ];
        $objCount = $this->select($sql, $arrParametros);
        if ($objCount === false) {
            return ['code' => 0, 'message' => 'Grupo1 não encontrado!'];
        } else {
            if ($objCount[0]['totalUsuarios'] <= '1') {
                return ['code' => 0, 'message' => 'Grupo2 não encontrado!'];
            } else {
                $sql = 'SELECT * FROM tab_usuarioGrupo WHERE grupo_usuarioGrupo = :intGrupo';
                $objUsuarioGrupo = $this->select($sql, $arrParametros);
                if ($objUsuarioGrupo === false) {
                    return ['code' => 0, 'message' => 'Grupo3 não encontrado!'];
                } else {

                    $x = 0;
                    $arr = [];
                    $arr2 = [];
                    foreach($objUsuarioGrupo as $value){
                        array_push($arr, $value['usuario_usuarioGrupo']);
                    }

                    foreach($objUsuarioGrupo as $value){
                        $obj[$x]['idAmigo'] = $value['usuario_usuarioGrupo'];
                        $sorteado = $arr[array_rand($arr)];
                        if($sorteado != $value['usuario_usuarioGrupo']){
                            $obj[$x]['saiuCom'] = $sorteado;
                        }else{
                            while ($sorteado == $value['usuario_usuarioGrupo']) :
                                    $sorteado = $arr[array_rand($arr)];
                                    if($sorteado != $value['usuario_usuarioGrupo']){
                                        $obj[$x]['saiuCom'] = $sorteado;
                                    }
                            endwhile;
                        }
                        $x++;
                    }

                    $sqlUpdate = 'UPDATE tab_grupo set sorteio_grupo = 1 where id_grupo = :idGrupo';
                    $arrParametrosupdate = [
                        ':idGrupo' => $intIdGrupo
                    ];
                    $objUpdate = $this->execute($sqlUpdate, $arrParametrosupdate);
                    if ($objUpdate === false) {
                        return ['code' => 0, 'message' => 'Não foi possivel alterar o grupo!'];
                    } else {
                        return ['code' => 1, 'message' => 'Sorteio realizado com sucesso!'];
                    }
                }
            }
        }
    }

    public function retornarGrupos($idUser)
    {
        $this->setConnection('mysql');
        $sql = 'SELECT
                    nome_grupo,
                    id_grupo,
                    admin_grupo,
                    sorteio_grupo,
                    dataSorteio_grupo
                FROM tab_grupo
                INNER JOIN tab_usuarioGrupo on
                    tab_usuarioGrupo.grupo_usuarioGrupo = tab_grupo.id_grupo
                WHERE
                    usuario_usuarioGrupo = :idUser';
        $arrParametros = [
            ':idUser' => $idUser->intIdUsuario
        ];
        $objGrupo = $this->select($sql, $arrParametros);
        if ($objGrupo === false) {
            return ['code' => 0, 'message' => 'Grupo não encontrado!'];
        } else {
            return ['code' => 1, 'grupo' => $objGrupo];
        }
    }


    public function atualizarGrupo($objGrupo)
    {
        //verifica se o sorteio já foi realizado
        if ($this->verificaObj($objGrupo) === true) {
            return ['code' => 0, 'message' => 'Não é possivel alterar o grupo, o sorteio já foi realizado!'];
        }
        if ($objGrupo['blnAtivo'] === true) {
            $opcaoAtivo = 0;
        } else {
            $opcaoAtivo = 1;
        }
        // return $objGrupo;
        $this->setConnection('mysql');
        $sql = 'UPDATE tab_grupo
                    SET
                        nome_grupo = :strNome,
                        tipo_grupo = :intTipo,
                        ativo_grupo = :blnAtivo,
                        dataSorteio_grupo = :dateSorteio,
                        descricao_grupo = :strDescricao,
                        faixaPreco_grupo = :fltPreco
                WHERE id_grupo = :intGrupo';
        $arrParametros = [
            ':strNome' => $objGrupo['strNome'],
            ':intTipo' => $objGrupo['intTipo'],
            ':blnAtivo' => $objGrupo['blnAtivo'],
            ':dateSorteio' => $objGrupo['dateSorteio'],
            ':strDescricao' => $objGrupo['strDescricao'],
            ':fltPreco' => $objGrupo['fltPreco'],
            ':intGrupo' => $objGrupo['intGrupo']
        ];

        $objTransacao = $this->execute($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi possivel alterar o grupo!'];
        } else {
            return ['code' => 1, 'message' => 'Grupo alterado com sucesso!'];
        }
    }

    public function resultado($objGrupo)
    {
        $this->setConnection('mysql');
        $sql = 'SELECT * from tab_sorteio where grupo_sorteio = :idGrupo';
        $arrParametros = [
            ':idGrupo' => $objGrupo['idGrupo']
        ];
        $objTransacao = $this->select($sql, $arrParametros);
        if ($objTransacao === false) {
            return ['code' => 0, 'message' => 'Não foi possivel alterar o grupo!'];
        } else {
            return ['code' => 1, 'grupo' => $objTransacao];
        }
    }


}
