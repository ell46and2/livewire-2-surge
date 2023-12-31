<div>

    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>

    <div class="py-4 space-y-4">
        <div class="flex justify-between">
            <div class="w-1/2 flex space-x-4">
                <x-input.text
                    wire:model="filters.search"
                    placeholder="Search transactions..."/>

                <x-button.link wire:click="toggleShowFilters">
                    @if($showFilters)
                        Hide
                    @endif
                    Advanced Search
                </x-button.link>
            </div>

            <div class="space-x-2">
                <x-dropdown label="Bulk Actions">
                    <x-dropdown.item
                        wire:click="exportSelected"
                        type="button"
                        class="flex items-center space-x-2">
                        <x-icon.download class="text-gray-400"/>
                        <span>Export</span>
                    </x-dropdown.item>
                    <x-dropdown.item
                        wire:click="toggleDeleteModal"
                        type="button"
                        class="flex items-center space-x-2">
                        <x-icon.trash
                            class="text-gray-400"/>
                        <span>Delete</span>
                    </x-dropdown.item>
                </x-dropdown>

                <x-button.primary wire:click="create">
                    <x-icon.plus/>
                    New
                </x-button.primary>
            </div>
        </div>

        <div>
            @if($showFilters)
                <div
                    wire:key="filters"
                    class="bg-cool-gray-200 p-4 rounded shadow-inner flex relative">
                    <div class="w-1/2 pr-2 space-y-4">
                        <x-input.group
                            inline
                            for="filter-status"
                            label="Status">
                            <x-input.select
                                wire:model="filters.status"
                                id="filter-status">
                                <option
                                    value=""
                                    disabled>Select Status...

                                </option>

                                @foreach (App\Models\Transaction::STATUSES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <x-input.group
                            inline
                            for="filter-amount-min"
                            label="Minimum Amount">
                            <x-input.money
                                wire:model.lazy="filters.amount-min"
                                id="filter-amount-min"/>
                        </x-input.group>

                        <x-input.group
                            inline
                            for="filter-amount-max"
                            label="Maximum Amount">
                            <x-input.money
                                wire:model.lazy="filters.amount-max"
                                id="filter-amount-max"/>
                        </x-input.group>
                    </div>

                    <div class="w-1/2 pl-2 space-y-4">
                        <x-input.group
                            inline
                            for="filter-date-min"
                            label="Minimum Date">
                            <x-input.date
                                wire:model="filters.date-min"
                                id="filter-date-min"
                                placeholder="MM/DD/YYYY"/>
                        </x-input.group>

                        <x-input.group
                            inline
                            for="filter-date-max"
                            label="Maximum Date">
                            <x-input.date
                                wire:model="filters.date-max"
                                id="filter-date-max"
                                placeholder="MM/DD/YYYY"/>
                        </x-input.group>

                        <x-button.link
                            wire:click="resetFilters"
                            class="absolute right-0 bottom-0 p-4">Reset Filters
                        </x-button.link>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex-col space-y-4">
            <x-table>
                <x-slot:head>
                    <x-table.heading class="pr-0 w-8">
                        <x-input.checkbox wire:model="selectPage"/>
                    </x-table.heading>
                    <x-table.heading
                        wire:click="sortBy('title')"
                        :direction="$sortField === 'title' ? $sortDirection : null"
                        :sortable="true"
                    >Title
                    </x-table.heading>
                    <x-table.heading
                        wire:click="sortBy('amount')"
                        :direction="$sortField === 'amount' ? $sortDirection : null"
                        :sortable="true">Amount
                    </x-table.heading>
                    <x-table.heading
                        wire:click="sortBy('status')"
                        :direction="$sortField === 'status' ? $sortDirection : null"
                        :sortable="true">Status
                    </x-table.heading>
                    <x-table.heading
                        wire:click="sortBy('date')"
                        :direction="$sortField === 'date' ? $sortDirection : null"
                        :sortable="true">Date
                    </x-table.heading>
                    <x-table.heading/>
                </x-slot:head>

                <x-slot:body>
                    @if ($selectPage)
                        <x-table.row
                            class="bg-gray-200"
                            wire:key="transaction-row-message">
                            <x-table.cell colspan="6">
                                @unless ($selectAll)
                                    <div>
                                        <span>You have selected <strong>{{ $transactions->count() }}</strong> transactions, do you want to select all <strong>{{ $transactions->total() }}</strong>?</span>
                                        <x-button.link
                                            wire:click="selectAll"
                                            class="ml-1 text-blue-600">Select All
                                        </x-button.link>
                                    </div>
                                @else
                                    <span>You are currently selecting all <strong>{{ $transactions->total() }}</strong> transactions.</span>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse($transactions as $transaction)
                        <x-table.row
                            wire:key="transaction-row-{{$transaction->id}}"
                            wire:loading.class.delay="opacity-50">
                            <x-table.cell class="pr-0">
                                <x-input.checkbox
                                    wire:model="selected"
                                    value="{{ $transaction->id }}"/>
                            </x-table.cell>
                            <x-table.cell>
                              <span class="inline-flex space-x-2 truncate text-sm leading-5">
                                  <x-icon.cash class="text-cool-gray-400"/>

                                <p class="text-cool-gray-600 truncate">
                                    {{ $transaction->title }}
                                </p>
                              </span>
                            </x-table.cell>
                            <x-table.cell>
                                <span class="text-cool-gray-900 font-medium">{{ $transaction->amount }}</span>
                            </x-table.cell>
                            <x-table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-800 capitalize">
                                {{ $transaction->status }}
                            </span>
                            </x-table.cell>
                            <x-table.cell>{{ $transaction->date_for_humans }}</x-table.cell>
                            <x-table.cell>
                                <x-button.link wire:click="edit({{ $transaction->id }})">Edit</x-button.link>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row wire:key="empty-row">
                            <x-table.cell colspan="6">
                                <div class="flex justify-center items-center space-x-2">
                                    <x-icon.inbox class="h-8 w-8 text-gray-400"/>
                                    <span class="font-medium py-8 text-gray-400 text-xl">No transactions found...</span>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot:body>
            </x-table>

            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Delete Transactions Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.confirmation wire:model.defer="showDeleteModal">
            <x-slot name="title">Delete Transaction</x-slot>

            <x-slot name="content">
                <div class="py-8 text-cool-gray-700">Are you sure you? This action is irreversible.</div>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showDeleteModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.confirmation>
    </form>

    <form wire:submit.prevent="save">
        <x-modal.dialog wire:model.defer="showEditModal">
            <x-slot:title>Edit Transaction</x-slot:title>
            <x-slot:content>
                <x-input.group
                    for="title"
                    label="Title"
                    :error="$errors->first('editing.title')">
                    <x-input.text
                        wire:model="editing.title"
                        id="title"
                        placeholder="Title"/>
                </x-input.group>

                <x-input.group
                    for="amount"
                    label="Amount"
                    :error="$errors->first('editing.amount')">
                    <x-input.money
                        wire:model="editing.amount"
                        id="amount"/>
                </x-input.group>

                <x-input.group
                    for="status"
                    label="Status"
                    :error="$errors->first('editing.status')">
                    <x-input.select
                        wire:model="editing.status"
                        id="status">
                        @foreach (App\Models\Transaction::STATUSES as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </x-input.select>
                </x-input.group>

                <x-input.group
                    for="date_for_editing"
                    label="Date"
                    :error="$errors->first('editing.date_for_editing')">
                    @if($editing)
                        <x-input.date
                            wire:model="editing.date_for_editing"
                            id="date_for_editing"/>
                    @endif
                </x-input.group>
            </x-slot:content>
            <x-slot:footer>
                <x-button.secondary wire:click="$set('showEditModal', false)">Cancel</x-button.secondary>
                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot:footer>
        </x-modal.dialog>
    </form>
</div>
