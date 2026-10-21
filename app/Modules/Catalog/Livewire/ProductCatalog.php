<?php

namespace App\Modules\Catalog\Livewire;

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Product listing of /productos and /buscar: filters live in the query string, so
 * every state of the page can be shared or bookmarked.
 *
 * @property-read Collection<int, Category> $categories
 * @property-read Category|null $currentCategory
 * @property-read LengthAwarePaginator<int, Product> $products
 */
class ProductCatalog extends Component
{
    use WithPagination;

    public const PER_PAGE = 12;

    public const SORT_OPTIONS = [
        'featured' => 'Destacados',
        'price-asc' => 'Precio: menor a mayor',
        'price-desc' => 'Precio: mayor a menor',
        'name-asc' => 'Nombre: A-Z',
        'name-desc' => 'Nombre: Z-A',
    ];

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: 'all')]
    public string $category = 'all';

    #[Url(except: 'featured')]
    public string $sort = 'featured';

    public function updated(string $property): void
    {
        if (in_array($property, ['q', 'category', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function selectCategory(string $slug): void
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->q = '';
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('q', 'category');
        $this->resetPage();
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()->orderBy('name')->get();
    }

    #[Computed]
    public function currentCategory(): ?Category
    {
        return $this->categories->firstWhere('slug', $this->category);
    }

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        $term = trim($this->q);

        return Product::query()
            ->with('category')
            ->when($term !== '', fn ($query) => $query->search($term))
            ->when($this->currentCategory, fn ($query, Category $category) => $query->where('category_id', $category->id))
            ->tap(fn ($query) => match ($this->sort) {
                'price-asc' => $query->orderBy('price'),
                'price-desc' => $query->orderByDesc('price'),
                'name-asc' => $query->orderBy('name'),
                'name-desc' => $query->orderByDesc('name'),
                default => $query->latest()->orderByDesc('id'),
            })
            ->paginate(self::PER_PAGE);
    }

    public function render(): View
    {
        return view('livewire.catalog.product-catalog');
    }
}
