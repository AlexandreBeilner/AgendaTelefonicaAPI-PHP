<?php

namespace Validator;

use Repository\TokensAutorizadosRepository;
use Service\ContatosService;
use Util\ConstantesGenericasUtil;
use Util\jsonUtil;

class RequestValidator
{
    private $request;
    private array $dadosRequest;
    private object $tokesAutorizadosRepository;

    const GET = "GET";
    const DELETE = "DELETE";
    const CONTATOS = 'CONTATOS';


    public function __construct($request)
    {
        $this->request = $request;
        $this->tokesAutorizadosRepository = new TokensAutorizadosRepository();
    }


    public function ProcessarRequest()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);

        if (in_array($this->request['metodo'], ConstantesGenericasUtil::TIPO_REQUEST, true)) {
            $retorno = $this->direcionarRequest();
        }

        return $retorno;
    }

    private function direcionarRequest()
    {
        if ($this->request['metodo'] !== self::DELETE && $this->request['metodo'] !== self::GET) {
            $this->dadosRequest = jsonUtil::tratarRequisicaoJson();
        }
        $this->tokesAutorizadosRepository->validarToken(getallheaders()['authorization'] ?? getallheaders()['Authorization']);
        $metodo = $this->request['metodo'];
        return $this->$metodo();
    }

    private function get()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_GET, true)) {
            switch ($this->request['rota']) {
                case self::CONTATOS:
                    $contatoService = new ContatosService($this->request);
                    $retorno = $contatoService->validarGet();
                    break;
                default:
                    throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }

        return $retorno;
    }

    private function delete()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_DELETE, true)) {
            switch ($this->request['rota']) {
                case self::CONTATOS:
                    $contatoService = new ContatosService($this->request);
                    $retorno = $contatoService->validarDelete();
                    break;
                default:
                    throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }

        return $retorno;
    }

    private function post()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_POST, true)) {
            switch ($this->request['rota']) {
                case self::CONTATOS:
                    $contatoService = new ContatosService($this->request);
                    $contatoService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $contatoService->validarPost();
                    break;
                default:
                    throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }

        return $retorno;
    }

    private function put()
    {
        $retorno = utf8_encode(ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_PUT, true)) {
            switch ($this->request['rota']) {
                case self::CONTATOS:
                    $contatoService = new ContatosService($this->request);
                    $contatoService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $contatoService->validarPut();
                    break;
                default:
                    throw new \InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }

        return $retorno;
    }

}
