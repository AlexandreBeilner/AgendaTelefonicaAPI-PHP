<?php

namespace Repository;

use DB\MySQL;

class ListaTelefonicaRepository
{

    private object $MySQL;
    public const TABELA = 'ListaTelefonica';

    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    public function insertContact($nome, $telefone, $endereco=""){
        $consultaInsert = 'INSERT INTO '. self::TABELA. ' (nome, telefone, endereco) VALUES (:nome, :telefone, :endereco)';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);

        $stmt->execute();
        return $stmt->rowCount();
    }
    public function updateContact($id, $dados){
        $consultaUpdate = 'UPDATE '. self::TABELA . ' SET nome = :nome, telefone = :telefone, endereco = :endereco WHERE id = :id';
        $this->MySQL->getDb()->beginTransaction();
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        $stmt->bindParam(':nome', $dados['name']);
        $stmt->bindParam(':telefone', $dados['cellNum']);
        $stmt->bindParam(':endereco', $dados['address']);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getMySQL()
    {
        return $this->MySQL;
    }
}
