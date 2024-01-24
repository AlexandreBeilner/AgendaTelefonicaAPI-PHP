<?php

namespace Service;

use Repository\ListaTelefonicaRepository;
use Util\ConstantesGenericasUtil;

class ContatosService
{
    public const TABELA = 'ListaTelefonica';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];
    //rotas que eu tenho
    //localhost:8080/crudIXC/api/contatos/listar para pegar todos os contatos do db --- METODO GET
    //localhost:8080/crudIXC/api/contatos/deletar/${id} para deletar um contatato do db --- METODO DELETE
    //localhost:8080/crudIXC/api/contatos/cadastrar para cadastrar um contatato novo --- METODO POST
    //localhost:8080/crudIXC/api/contatos/atualizar/${id} para modificar um contato --- METODO PUT

    private array $dados;

    private array $dadosCorpoRequest = [];

    private object $ContatosRepository;

    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->ContatosRepository = new ListaTelefonicaRepository();
    }

    /**
     * @return mixed
     */
    public function validarDelete()
    {
        $retorno = null;
        $recurso = $this->dados['recursos'];
        if (in_array($recurso, self::RECURSOS_DELETE, true)) {
            if ($this->dados['id'] > 0) {
                $retorno = $this->$recurso();
            } else {
                throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }

    /**
     * @return mixed
     */
    public function validarPut()
    {
        $retorno = null;
        $recurso = $this->dados['recursos'];
        if (in_array($recurso, self::RECURSOS_PUT, true)) {
            if ($this->dados['id'] > 0) {
                $retorno = $this->$recurso();
            } else {
                throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }

    /**
     * @return mixed
     */
    public function validarPost()
    {
        $retorno = null;
        $recurso = $this->dados['recursos'];
        if (in_array($recurso, self::RECURSOS_POST, true)) {
            $retorno = $this->$recurso();
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }


    /**
     * @return mixed
     */
    public function validarGet()
    {
        $retorno = null;
        $recurso = $this->dados['recursos'];
        if (in_array($recurso, self::RECURSOS_GET, true)) {
            $retorno = $this->dados['id'] > 0 ? $this->getOneByKey() : $this->$recurso();
        } else {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }

    public function setDadosCorpoRequest($dadosRequest)
    {
        $this->dadosCorpoRequest = $dadosRequest;
    }

    private function getOneByKey()
    {
        return $this->ContatosRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    /**
     * @param $retorno
     * @return void
     */
    public function validarRetornoRequest($retorno): void
    {
        if ($retorno == null) {
            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
    }

    private function listar()
    {
        return $this->ContatosRepository->getMySQL()->getAll(self::TABELA);
    }

    private function deletar()
    {
        return $this->ContatosRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);

    }

    /**
     * @return array
     */
    private function cadastrar()
    {
        [$nome, $telefone, $endereco] = [$this->dadosCorpoRequest['name'], $this->dadosCorpoRequest['cellNum'], $this->dadosCorpoRequest['address']];
        if ($nome && $telefone) {
            if ($this->ContatosRepository->insertContact($nome, $telefone, $endereco) > 0) {
                $idInserido = $this->ContatosRepository->getMySQL()->getDb()->lastInsertId();
                $this->ContatosRepository->getMySQL()->getDb()->commit();
                return ['id_inserido' => $idInserido];
            }

            throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NOME_TELEFONE_OBRIGATORIO);
    }

    /**
     * @return string
     */
    private function atualizar()
    {
        if ($this->ContatosRepository->updateContact($this->dados['id'], $this->dadosCorpoRequest)) {
            $this->ContatosRepository->getMySQL()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->ContatosRepository->getMySQL()->getDb()->rollBack();

        throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
}
