<?php

namespace Rappasoft\LaravelLivewireTables\Traits;

/**
 * Trait WithSearch.
 */
trait WithSearch
{
    /**
     * Show the search field.
     *
     * @var bool
     */
    public bool $showSearch = true;

    /**
     * Search placeholder
     *
     * @var string
     */
    public string $searchPlaceholder;

    /**
     * @var int|null
     */
    public ?int $searchFilterDebounce = null;

    /**
     * @var bool|null
     */
    public ?bool $searchFilterDefer = null;

    /**
     * @var bool|null
     */
    public ?bool $searchFilterLazy = null;

    /**
     * Update the selected page rows when changing search parameter
     */
    public function updatedFiltersSearch(): void
    {
        if ($this->selectPage) {
            $this->selectPageRows();
        }
    }

    /**
     * Clear the search filter specifically
     */
    public function resetSearch(): void
    {
        $this->filters['search'] = null;
    }

    /**
     * Build Livewire model options for the search input
     *
     * @return string
     */
    public function getSearchFilterOptionsProperty(): string
    {
        if ($this->searchFilterDebounce) {
            return '.debounce.' . $this->searchFilterDebounce . 'ms';
        }

        if ($this->searchFilterDefer) {
            return '.defer';
        }

        if ($this->searchFilterLazy) {
            return '.lazy';
        }

        return '';
    }
}
