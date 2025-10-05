<?php

class QueryBuilder
{
    private mysqli $db;
    private string $select = '*';
    private string $from = '';
    private array $wheres = [];
    private array $params = [];
    private array $types = [];
    private ?string $orderBy = null;
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(?mysqli $db = null)
    {
        $this->db = $db ?: Database::getConnection();
    }

    public function select(string $select): self { $this->select = $select; return $this; }
    public function from(string $table): self { $this->from = $table; return $this; }

    public function where(string $clause, string $type = '', $value = null): self
    {
        $this->wheres[] = $clause;
        if ($type !== '' && $value !== null) {
            $this->types[] = $type;
            $this->params[] = $value;
        }
        return $this;
    }

    public function orderBy(string $orderBy): self { $this->orderBy = $orderBy; return $this; }
    public function limit(int $limit): self { $this->limit = $limit; return $this; }
    public function offset(int $offset): self { $this->offset = $offset; return $this; }

    public function get(): array
    {
        $sql = $this->toSql();
        $stmt = $this->prepare($sql);
        $rows = [];
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $stmt->close();
        return $rows;
    }

    public function first(): ?array
    {
        $this->limit(1)->offset(0);
        $rows = $this->get();
        return $rows[0] ?? null;
    }

    public function count(string $countExpr = 'COUNT(*) as c'): int
    {
        $origSelect = $this->select;
        $this->select = $countExpr;
        $sql = $this->toSql();
        $stmt = $this->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        $c = (int)($res->fetch_assoc()['c'] ?? 0);
        $stmt->close();
        $this->select = $origSelect;
        return $c;
    }

    private function toSql(): string
    {
        $sql = 'SELECT ' . $this->select . ' FROM ' . $this->from;
        if ($this->wheres) { $sql .= ' WHERE ' . implode(' AND ', $this->wheres); }
        if ($this->orderBy) { $sql .= ' ORDER BY ' . $this->orderBy; }
        if ($this->limit !== null) { $sql .= ' LIMIT ?'; $this->types[] = 'i'; $this->params[] = $this->limit; }
        if ($this->offset !== null) { $sql .= ' OFFSET ?'; $this->types[] = 'i'; $this->params[] = $this->offset; }
        return $sql;
    }

    private function prepare(string $sql): mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);
        if ($this->params) {
            $types = implode('', $this->types);
            // mysqli requires references for bind_param
            $refs = [];
            $refs[] = & $types;
            foreach ($this->params as $k => $v) {
                $refs[] = & $this->params[$k];
            }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        return $stmt;
    }
}
