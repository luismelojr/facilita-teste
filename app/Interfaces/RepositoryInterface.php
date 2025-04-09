<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Obter todos os registros
     *
     * @param array $columns Colunas para selecionar
     * @param array $relations Relacionamentos para eager load
     * @return Collection
     */
    public function getAll(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Obter registro por ID
     *
     * @param int $id ID do registro
     * @param array $columns Colunas para selecionar
     * @param array $relations Relacionamentos para eager load
     * @param array $appends Atributos para append
     * @return Model|null
     */
    public function getById(int $id, array $columns = ['*'], array $relations = [], array $appends = []): ?Model;

    /**
     * Criar novo registro
     *
     * @param array $data Dados para criar o registro
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Atualizar registro existente
     *
     * @param int $id ID do registro
     * @param array $data Dados para atualizar
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Deletar registro
     *
     * @param int $id ID do registro
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Buscar registros com filtros
     *
     * @param array $criteria Critérios de busca
     * @param array $columns Colunas para selecionar
     * @param array $relations Relacionamentos para eager load
     * @return Collection
     */
    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Collection;
}
