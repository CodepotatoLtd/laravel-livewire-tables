<?php

namespace Rappasoft\LaravelLivewireTables\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Trait WithSorting.
 */
trait WithSorting
{
    public bool $showSorting = true;
    public bool $singleColumnSorting = false;
    public array $sorts = [];
    public array $sortNames = [];
    public array $sortDirectionNames = [];
    public string $defaultSortColumn = '';
    public string $defaultSortDirection = 'asc';
    public string $prioritiseColumnsWhenSearching = '';

    public function sortBy(string $field): ?string
    {
        if ($this->singleColumnSorting && count($this->sorts) && ! isset($this->sorts[$field])) {
            $this->sorts = [];
        }

        if (! isset($this->sorts[$field])) {
            return $this->sorts[$field] = 'asc';
        }

        if ($this->sorts[$field] === 'asc') {
            return $this->sorts[$field] = 'desc';
        }

        unset($this->sorts[$field]);

        return null;
    }

    /**
     * @param  Builder|Relation  $query
     *
     * @return Builder|Relation
     */
    public function applySorting($query)
    {
        if (! empty($this->defaultSortColumn) && ! count($this->sorts)) {
            return $query->orderBy($this->defaultSortColumn, $this->defaultSortDirection);
        }

        foreach ($this->sorts as $field => $direction) {
            if (optional($this->getColumn($field))->hasSortCallback()) {
                $query = app()->call($this->getColumn($field)->getSortCallback(), ['query' => $query, 'direction' => $direction]);
            } else {
                $query->orderBy($field, $direction);
            }
        }

        if (isset($this->filters['search']) && $this->prioritiseColumnsWhenSearching) {
            $searchTerm = $this->filters['search'];
            $columns = $this->prioritiseColumnsWhenSearching;
            return $query->orderByRaw("MATCH ($columns) AGAINST ('$searchTerm') DESC");
        }

        return $query;
    }

    public function removeSort(string $field): void
    {
        if (isset($this->sorts[$field])) {
            unset($this->sorts[$field]);
        }
    }

    public function resetSorts(): void
    {
        $this->sorts = [];
    }
}
