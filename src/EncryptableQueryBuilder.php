<?php

namespace Silverd\Encryptable;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

class EncryptableQueryBuilder extends Builder
{
    private $model;

    public function __construct(ConnectionInterface $connection, $model)
    {
        parent::__construct($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());

        $this->model = $model;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($this->model->isEncryptable($column)) {

            [$value, $operator] = $this->prepareValueAndOperator($value, $operator, func_num_args() === 2);

            $value = $this->model->encryptAttribute($value);
        }

        return parent::where($column, $operator, $value, $boolean);
    }

    public function whereDecrypt($column, string $rawSQL, array $bindings = [])
    {
        $column = app('encryption')->getDecryptExpr($column);

        return $this->whereRaw($column . ' ' . $rawSQL, $bindings);
    }

    public function orWhereDecrypt($column, string $rawSQL, array $bindings = [])
    {
        $column = app('encryption')->getDecryptExpr($column);

        return $this->orWhereRaw($column . ' ' . $rawSQL, $bindings);
    }
}
