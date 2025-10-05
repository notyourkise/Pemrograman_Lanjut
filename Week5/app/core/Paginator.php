<?php

class Paginator
{
    public int $page;
    public int $perPage;
    public int $total;

    public function __construct(int $total, int $page = 1, int $perPage = 10)
    {
        $this->total = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->page = max(1, $page);
        $maxPage = $this->pages();
        if ($this->page > $maxPage && $maxPage > 0) {
            $this->page = $maxPage;
        }
    }

    public function pages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function hasPrev(): bool { return $this->page > 1; }
    public function hasNext(): bool { return $this->page < $this->pages(); }
}
