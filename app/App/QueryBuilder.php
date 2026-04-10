<?php

namespace TheFramework\App;

use InvalidArgumentException;
use Closure;
use TheFramework\App\Exceptions\ModelNotFoundException;

class QueryBuilder
{
    private $db;
    private $table;
    private $columns = "*";
    
    private $limit;
    private $offset;

    private $wheres = [];
    private $joins = [];
    private $groupBy = [];
    private $orders = [];
    
    private $withRelations = [];
    private $withCounts = [];
    private $withAggregates = [];

    /** @var Model */
    private $model;
    private $lock; 

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function setModel($model)
    {
        $this->model = $model;
        $this->table = $model->getTable();
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    private function wrapColumn($column) {
        if ($column instanceof Raw) {
            return $column->getValue();
        }
        if (strpos($column, '.') !== false || strpos($column, '(') !== false) {
            return $column;
        }
        if (strpos($column, '.') === false) {
            return "`{$this->table}`.`{$column}`";
        }
        return "`{$column}`";
    }

    public function select($columns = ["*"])
    {
        $rawColumns = is_array($columns) ? $columns : func_get_args();
        $this->columns = implode(", ", array_map(function($item) {
            return (string) $item;
        }, $rawColumns));
        return $this;
    }

    public function selectRaw(string $expression, array $bindings = [])
    {
        $this->select(new Raw($expression));
        return $this;
    }

    public function getWithPartition(string $partitionKey, int $limit): array
    {
        if (empty($this->orders)) {
            throw new \Exception("Anda harus menggunakan orderBy() saat menggunakan getWithPartition().");
        }

        $orderClauses = [];
        foreach ($this->orders as $order) {
            $orderClauses[] = "{$this->wrapColumn($order['column'])} {$order['direction']}";
        }
        $orderBySql = implode(', ', $orderClauses);

        [$whereSql, $partitionBindings] = $this->compileWheres();

        $rowNumColumn = 'gemini_row_num'; 
        $subQuery = "SELECT *, ROW_NUMBER() OVER (PARTITION BY {$this->wrapColumn($partitionKey)} ORDER BY {$orderBySql}) as `{$rowNumColumn}` FROM `{$this->table}` {$whereSql}";

        $mainQuery = "SELECT * FROM ({$subQuery}) as `gemini_sub` WHERE `gemini_sub`.`{$rowNumColumn}` <= :gemini_partition_limit";
        
        $finalBindings = array_merge($partitionBindings, [':gemini_partition_limit' => $limit]);

        $this->db->query($mainQuery);
        foreach ($finalBindings as $param => $value) {
            $this->db->bind($param, $value);
        }
        $this->db->execute();
        $results = $this->db->resultSet();
        
        if ($this->model) {
            return $this->model->hydrate($results);
        }

        return $results;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'AND')
    {
        if ($column instanceof Closure) {
            return $this->whereNested($column, $boolean);
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        return $this;
    }
    
    public function whereNested(Closure $callback, $boolean = 'AND')
    {
        $query = $this->model->newQueryWithoutScopes();
        $callback($query);
        $this->wheres[] = [
            'type' => 'nested',
            'query' => $query,
            'boolean' => $boolean,
        ];
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if ($column instanceof Closure) {
            return $this->whereNested($column, 'OR');
        }
        return $this->where($column, $operator, $value, 'OR');
    }

    public function whereIn(string $column, array $values, $boolean = 'AND', $not = false)
    {
        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean,
            'not' => $not,
        ];
        return $this;
    }

    public function whereNotIn(string $column, array $values, $boolean = 'AND')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }
    
    public function whereColumn(string $first, string $operator, string $second, string $boolean = 'AND')
    {
        $this->wheres[] = [
            'type' => 'column',
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'boolean' => $boolean
        ];
        return $this;
    }

    public function whereDate($column, $operator, $value = null, $boolean = 'AND')
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
    }

    public function whereMonth($column, $operator, $value = null, $boolean = 'AND')
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }

    public function whereDay($column, $operator, $value = null, $boolean = 'AND')
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }
    
    public function whereYear($column, $operator, $value = null, $boolean = 'AND')
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }

    public function whereTime($column, $operator, $value = null, $boolean = 'AND')
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        return $this->addDateBasedWhere('Time', $column, $operator, $value, $boolean);
    }

    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean)
    {
        $this->wheres[] = [
            'type' => 'date',
            'date_type' => $type,
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];
        return $this;
    }

    public function whereJsonContains(string $column, $value, $boolean = 'AND', $not = false)
    {
         $this->wheres[] = [
            'type' => 'json_contains',
            'column' => $column,
            'value' => $value,
            'boolean' => $boolean,
            'not' => $not,
        ];
        return $this;
    }

    public function whereJsonLength(string $column, $operator, $value = null, $boolean = 'AND')
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = [
            'type' => 'json_length',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];
        return $this;
    }

    protected function addSubqueryWhere($type, $relationName, $boolean, ?Closure $callback = null)
    {
        $relation = $this->model->$relationName();
        $subQuery = $relation->getRelated()->newQueryWithoutScopes();
        
        $subQuery->whereColumn(
            $relation->getQualifiedForeignKeyName(), '=', $this->model->getQualifiedKeyName()
        );

        if ($callback) {
            $callback($subQuery);
        }

        $this->wheres[] = [
            'type' => $type,
            'subquery' => $subQuery,
            'boolean' => $boolean,
        ];

        return $this;
    }

    public function whereHas(string $relationName, ?Closure $callback = null, $boolean = 'AND')
    {
        return $this->addSubqueryWhere('exists', $relationName, $boolean, $callback);
    }
    
    public function orWhereHas(string $relationName, ?Closure $callback = null)
    {
        return $this->whereHas($relationName, $callback, 'OR');
    }

    public function doesntHave(string $relation, $boolean = 'AND', ?Closure $callback = null)
    {
        return $this->addSubqueryWhere('not_exists', $relation, $boolean, $callback);
    }

    public function orDoesntHave(string $relation, ?Closure $callback = null)
    {
        return $this->doesntHave($relation, 'OR', $callback);
    }

    public function whereRelation(string $relationName, string $column, $operator, $value = null)
    {
        return $this->whereHas($relationName, function ($query) use ($column, $operator, $value) {
            $query->where($column, $operator, $value);
        });
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER')
    {
        $this->joins[] = "{$type} JOIN `{$table}` ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }
    
    public function orderBy(string $column, string $direction = 'ASC')
    {
        if (!preg_match('/^[a-zA-Z0-9_\.\(\)]+$/', $column)) {
            throw new InvalidArgumentException("Invalid column name for orderBy: $column");
        }
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = ['column' => $column, 'direction' => $dir];
        return $this;
    }

    public function latest(string $column = 'created_at') { return $this->orderBy($column, 'DESC'); }
    public function oldest(string $column = 'created_at') { return $this->orderBy($column, 'ASC'); }

    public function groupBy($columns)
    {
        $this->groupBy = is_array($columns) ? $columns : [$columns];
        return $this;
    }
    
    public function when($value, callable $callback, ?callable $default = null)
    {
        if ($value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }
        return $this;
    }

    private function compileWheres(string $prefix = ''): array
    {
        if (empty($this->wheres)) return ['', []];

        $sqlParts = [];
        $bindings = [];
        $counter = 0;
        
        foreach ($this->wheres as $i => $where) {
            $boolean = ($i === 0) ? '' : $where['boolean'];
            $part = '';

            switch ($where['type']) {
                case 'basic':
                    $paramName = ":where_{$prefix}{$counter}";
                    $part = "{$this->wrapColumn($where['column'])} {$where['operator']} {$paramName}";
                    $bindings[$paramName] = $where['value'];
                    $counter++;
                    break;
                case 'in':
                    if (empty($where['values'])) {
                         $part = $where['not'] ? '1=1' : '0=1';
                    } else {
                        $inPlaceholders = [];
                        foreach ($where['values'] as $value) {
                            $inParam = ":in_{$prefix}{$counter}";
                            $inPlaceholders[] = $inParam;
                            $bindings[$inParam] = $value;
                            $counter++;
                        }
                        $operator = $where['not'] ? 'NOT IN' : 'IN';
                        $part = $this->wrapColumn($where['column']) . " {$operator} (" . implode(", ", $inPlaceholders) . ")";
                    }
                    break;
                case 'column':
                    $part = "{$this->wrapColumn($where['first'])} {$where['operator']} {$this->wrapColumn($where['second'])}";
                    break;
                case 'nested':
                    [$nestedSql, $nestedBindings] = $where['query']->compileWheres($prefix . $counter . "_");
                    $part = "(". ltrim(ltrim($nestedSql, 'WHERE ')) .")";
                    $bindings = array_merge($bindings, $nestedBindings);
                    break;
                case 'date':
                    $paramName = ":date_{$prefix}{$counter}";
                    $part = "{$where['date_type']}({$this->wrapColumn($where['column'])}) {$where['operator']} {$paramName}";
                    $bindings[$paramName] = $where['value'];
                    $counter++;
                    break;
                case 'exists':
                case 'not_exists':
                     [$subSql, $subBindings] = $where['subquery']->toSql();
                     $operator = $where['type'] === 'exists' ? 'EXISTS' : 'NOT EXISTS';
                     $part = "{$operator} ({$subSql})";
                     $bindings = array_merge($bindings, $subBindings);
                    break;
                case 'json_contains':
                     $paramName = ":json_{$prefix}{$counter}";
                     $operator = $where['not'] ? 'NOT ' : '';
                     $part = "{$operator}JSON_CONTAINS({$this->wrapColumn($where['column'])}, {$paramName})";
                     $bindings[$paramName] = json_encode($where['value']);
                     $counter++;
                    break;
                case 'json_length':
                    $paramName = ":json_len_{$prefix}{$counter}";
                    $part = "JSON_LENGTH({$this->wrapColumn($where['column'])}) {$where['operator']} {$paramName}";
                    $bindings[$paramName] = $where['value'];
                    $counter++;
                    break;
            }
            
            $sqlParts[] = "{$boolean} {$part}";
        }
        
        $sql = implode(' ', $sqlParts);
        if (!empty($sql)) {
            $sql = 'WHERE ' . ltrim(ltrim($sql), 'AND ');
        }

        return [$sql, $bindings];
    }

    public function toSql(): array
    {
        $sql = "SELECT {$this->columns} FROM `{$this->table}`";

        if (!empty($this->joins)) $sql .= " " . implode(" ", $this->joins);

        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql .= " " . $whereSql;
        $finalBindings = $whereBindings;

        if (!empty($this->groupBy)) $sql .= " GROUP BY " . implode(", ", $this->groupBy);

        if (!empty($this->orders)) {
             $orderClauses = [];
            foreach ($this->orders as $order) {
                $orderClauses[] = "{$this->wrapColumn($order['column'])} {$order['direction']}";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT :main_limit";
            $finalBindings[':main_limit'] = (int) $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET :main_offset";
            $finalBindings[':main_offset'] = (int) $this->offset;
        }

        if ($this->lock) $sql .= " " . $this->lock;

        return [$sql, $finalBindings];
    }
    
    public function get() { return $this->executeQuery(); }
    public function all() { return $this->get(); }

    private function executeQuery()
    {
        [$sql, $bindings] = $this->toSql();
        
        $this->db->query($sql);
        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        $this->db->execute();
        $results = $this->db->resultSet();

        if ($this->model) {
            $models = $this->model->hydrate($results);
            if (!empty($this->withRelations)) {
                $models = $this->model->loadRelations($models, $this->withRelations);
            }
            if (!empty($this->withCounts)) {
                $models = $this->model->loadCounts($models, $this->withCounts);
            }
             if (!empty($this->withAggregates)) {
                $models = $this->model->loadAggregates($models, $this->withAggregates);
            }
            return $models;
        }

        return $results;
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function find($id)
    {
        $primaryKey = $this->model ? $this->model->getKeyName() : 'id';
        return $this->where($primaryKey, '=', $id)->first();
    }
    
    public function findOrFail($id)
    {
        $result = $this->find($id);
        if (is_null($result)) {
            throw new ModelNotFoundException(
                "No query results for model [" . get_class($this->model) . "] " . $id
            );
        }
        return $result;
    }

    public function insertGetId(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->lastInsertId();
    }

    public function insert(array $data): int
    {
        if ($this->db->insert($this->table, $data)) {
            return $this->db->rowCount();
        }
        return 0;
    }
    
    public function firstOrCreate(array $attributes, array $values = [])
    {
        $instance = $this->where($attributes)->first();
        if (!is_null($instance)) {
            return $instance;
        }
        return $this->model->create(array_merge($attributes, $values));
    }
    
    public function updateOrCreate(array $attributes, array $values)
    {
        $instance = $this->where($attributes)->first();
        if (!is_null($instance)) {
            $instance->fill($values)->save();
            return $instance;
        }
        return $this->model->create(array_merge($attributes, $values));
    }

    public function upsert(array $values, array $uniqueBy, array $update)
    {
        if (empty($values)) return 0;

        $columns = array_keys($values[0]);
        $columnSql = '`' . implode('`, `', $columns) . '`';
        
        $bindings = [];
        $valuesSqlParts = [];
        $i = 0;
        
        foreach ($values as $row) {
            $rowPlaceholders = [];
            foreach ($row as $column => $value) {
                $paramName = ":upsert_{$i}";
                $rowPlaceholders[] = $paramName;
                $bindings[$paramName] = $value;
                $i++;
            }
            $valuesSqlParts[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }
        
        $valuesSql = implode(', ', $valuesSqlParts);

        $updateSqlParts = [];
        foreach($update as $col) {
            $updateSqlParts[] = "`{$col}` = VALUES(`{$col}`)";
        }
        $updateSql = implode(', ', $updateSqlParts);

        $sql = "INSERT INTO `{$this->table}` ({$columnSql}) VALUES {$valuesSql} ON DUPLICATE KEY UPDATE {$updateSql}";

        $this->db->query($sql);
        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function update(array $data): int
    {
        if (empty($this->wheres)) {
            throw new \Exception('Update without a WHERE clause is not allowed for safety.');
        }

        $setParts = [];
        $updateBindings = [];
        foreach ($data as $column => $value) {
            $paramName = ":update_" . $column;
            $setParts[] = "{$this->wrapColumn($column)} = {$paramName}";
            $updateBindings[$paramName] = $value;
        }
        $setSql = implode(', ', $setParts);

        [$whereSql, $whereBindings] = $this->compileWheres();
        $finalBindings = array_merge($updateBindings, $whereBindings);

        $sql = "UPDATE `{$this->table}` SET {$setSql} {$whereSql}";

        $this->db->query($sql);
        foreach ($finalBindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function delete(): int
    {
        if (empty($this->wheres)) {
            throw new \Exception('Delete without a WHERE clause is not allowed for safety.');
        }

        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = "DELETE FROM `{$this->table}` {$whereSql}";
        
        $this->db->query($sql);
        foreach ($whereBindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        $this->db->execute();
        return $this->db->rowCount();
    }

    public function count(): int
    {
        $countBuilder = clone $this;
        $countBuilder->columns = 'COUNT(*) as total';

        [$sql, $bindings] = $countBuilder->toSql();
        
        $sql = preg_replace('/ ORDER BY .*$/i', '', $sql);
        $sql = preg_replace('/ LIMIT .*$/i', '', $sql);
        $sql = preg_replace('/ OFFSET .*$/i', '', $sql);

        $this->db->query($sql);
        foreach ($bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        $this->db->execute();
        $result = $this->db->single();
        return (int) ($result['total'] ?? 0);
    }
    
    public function chunk(int $count, callable $callback)
    {
        $page = 1;
        do {
            $results = $this->limit($count)->offset(($page - 1) * $count)->get();
            $countResults = count($results);

            if ($countResults == 0) break;

            if ($callback($results, $page) === false) {
                return false;
            }

            unset($results);
            $page++;
        } while ($countResults == $count);
        return true;
    }

    public function lazy()
    {
        $this->chunk(100, function ($results) {
            foreach ($results as $result) {
                yield $result;
            }
        });
    }

    public function paginate(int $perPage = 15, int $page = 1)
    {
        $page = $page > 0 ? $page : 1;
        $total = $this->count();
        $results = $this->limit($perPage)->offset(($page - 1) * $perPage)->get();

        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }
    
    public function limit(int $limit) { $this->limit = $limit; return $this; }
    public function offset(int $offset) { $this->offset = $offset; return $this; }
    public function take(int $count) { return $this->limit($count); }

    public function with(array $relations)
    {
        $this->withRelations = array_merge($this->withRelations, $relations);
        return $this; 
    }

    public function withCount(array $relations)
    {
        $this->withCounts = array_merge($this->withCounts, $relations);
        return $this;
    }

    public function withAggregate(array $relations, string $function, string $column)
    {
        foreach($relations as $relation) {
            $this->withAggregates[] = [
                'relation' => $relation,
                'function' => $function,
                'column' => $column
            ];
        }
        return $this;
    }

    public function lockForUpdate() { $this->lock = "FOR UPDATE"; return $this; }
    public function sharedLock() { $this->lock = "LOCK IN SHARE MODE"; return $this; }

    public function __call($method, $parameters)
    {
        if (method_exists($this->model, 'scope'.ucfirst($method))) {
            return $this->model->{'scope'.ucfirst($method)}($this, ...$parameters);
        }
        throw new \BadMethodCallException("Method {$method} does not exist on QueryBuilder.");
    }
}

class Raw {
    protected $value;
    public function __construct($value) { $this->value = $value; }
    public function getValue() { return $this->value; }
    public function __toString() { return (string)$this->value; }
}
