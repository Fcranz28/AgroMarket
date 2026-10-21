<?php

namespace App\Modules\Catalog\Livewire;

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Search box of the header with live suggestions. It is a regular GET form, so it also
 * works before Livewire loads.
 */
class SearchBox extends Component
{
    public const MIN_LENGTH = 2;

    public const SUGGESTION_LIMIT = 5;

    public string $q = '';

    public string $category = 'all';

    public function mount(): void
    {
        $this->q = (string) request()->query('q', '');
        $this->category = (string) request()->query('category', 'all');
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()->orderBy('name')->get(['id', 'name', 'slug']);
    }

    /**
     * @return Collection<int, Product>
     */
    #[Computed]
    public function suggestions(): Collection
    {
        $term = trim($this->q);

        if (mb_strlen($term) < self::MIN_LENGTH) {
            return collect();
        }

        return Product::search($term, includeDescription: false)
            ->select(['id', 'name', 'slug', 'image_path', 'price', 'unit'])
            ->limit(self::SUGGESTION_LIMIT)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.catalog.search-box');
    }
}
