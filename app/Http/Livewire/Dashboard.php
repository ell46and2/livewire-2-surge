<?php

namespace App\Http\Livewire;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Dashboard extends Component
{
    use WithPagination;

    public array $selected = [];
    public bool $selectPage = false;
    public bool $selectAll = false;
    public bool $showDeleteModal = false;
    public string $sortField = '';
    public string $sortDirection = 'asc';
    public bool $showEditModal = false;
    public bool $showFilters = false;
    public $filters = [
        'search' => '',
        'status' => '',
        'amount-min' => null,
        'amount-max' => null,
        'date-min' => null,
        'date-max' => null,
    ];
    public Transaction $editing;

    protected $queryString = [
        'sortField' => ['except' => ''],
        'sortDirection',
    ];

    public function rules(): array
    {
        return [
            'editing.title' => [
                'required',
                'string',
            ],
            'editing.amount' => [
                'required',
                'numeric',
            ],
            'editing.status' => [
                'required',
                'in:'.collect(Transaction::STATUSES)->keys()->implode(','),
            ],
            'editing.date_for_editing' => [
                'required',
            ],
        ];
    }

    public function updatedFilters(): void
    {
        $this->resetPage(); // reset pagination
    }

    public function updatedSelectPage(bool $value): void
    {
        if ($value) {
            $this->selected = $this->transactions->pluck('id')->toArray();
            return;
        }

        $this->selectAll = false;
        $this->selected = [];
    }

    public function updatedSelected()
    {
        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function exportSelected(): StreamedResponse
    {
        return response()
            ->streamDownload(
                function () {
                    echo (clone $this->transactionsQuery)
                        ->unless(
                            $this->selectAll,
                            fn(Builder $query) => $query->whereIn('id', $this->selected)
                        )
                        ->toCsv();
                },
                'transactions.csv'
            );
    }

    public function selectAll()
    {
        $this->selectAll = true;
    }

    public function toggleDeleteModal(): void
    {
        $this->showDeleteModal = !$this->showDeleteModal;
    }

    public function deleteSelected(): void
    {
        (clone $this->transactionsQuery)
            ->unless(
                $this->selectAll,
                fn(Builder $query) => $query->whereIn('id', $this->selected)
            )
            ->delete();

        $this->showDeleteModal = false;
    }

    public function sortBy(string $field): void
    {
        if (isset($this->sortField) && $this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function toggleShowFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function create(): void
    {
        // if editing has an id then we make a new blank transaction
        // this way we don't clear the field if the user accidentally
        // closed the model
        if (!isset($this->editing) || $this->editing->getKey()) {
            $this->editing = Transaction::query()->make([
                'date' => now(),
                'status' => 'processing'
            ]);
            $this->resetValidation();
        }
        $this->showEditModal = true;
    }

    public function edit(Transaction $transaction): void
    {
        if (!isset($this->editing) || $this->editing->isNot($transaction)) {
            $this->editing = $transaction;
            $this->resetValidation();
        }

        $this->showEditModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $this->editing->save();

        $this->showEditModal = false;
    }

    public function resetFilters(): void
    {
        $this->reset('filters');
    }

    public function getTransactionsQueryProperty(): Builder
    {
        return Transaction::query()
            ->when(
                $this->filters[ 'status' ],
                fn(Builder $query, string $status) => $query->where('status', $status)
            )
            ->when(
                $this->filters[ 'amount-min' ],
                fn(Builder $query, float $amount) => $query->where('amount', '>=', $amount)
            )
            ->when(
                $this->filters[ 'amount-max' ],
                fn(Builder $query, float $amount) => $query->where('amount', '<=', $amount)
            )
            ->when(
                $this->filters[ 'date-min' ],
                fn(Builder $query, string $date) => $query->where('date', '>=', Carbon::parse($date))
            )
            ->when(
                $this->filters[ 'date-max' ],
                fn(Builder $query, string $date) => $query->where('date', '<=', Carbon::parse($date))
            )
            ->when(
                $this->filters[ 'search' ],
                fn(Builder $query, string $search) => $query->where('title', 'like', '%'.$search.'%')
            )
            ->when(
                $this->sortField !== '', function (Builder $query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            });
    }

    public function getTransactionsProperty(): LengthAwarePaginator
    {
        return $this->transactionsQuery->paginate(10);
    }

    public function render(): View
    {
        if ($this->selectAll) {
            $this->selected = $this->transactions->pluck('id')->toArray();
        }

        return view('livewire.dashboard', [
            'transactions' => $this->transactions
        ]);
    }
}
