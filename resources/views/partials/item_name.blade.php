<div class="col-md-6">
    @php
        $selectedItemMasters = [];
        $availableItemMasters = $itemNames->sortBy('item_name.name', SORT_NATURAL | SORT_FLAG_CASE);

        if (!empty($item)) {
            // For edit mode - get selected item masters from database
            $selectedItemMasters = is_array($item->item_master_id) ? $item->item_master_id : [];

            // dd($selectedItemMasters);
            // Filter out selected items from available list
            $availableItemMasters = $itemNames
                ->whereNotIn('id', $selectedItemMasters)
                ->whereNotIn('id', $old_item_ids)
                ->sortBy('item_name.name', SORT_NATURAL | SORT_FLAG_CASE);
        }

        // dd($availableItemMasters, $selectedItemMasters);

    @endphp
    @if ($itemNames->isEmpty() || $availableItemMasters->isEmpty())
        <h6 class="mb-0 mt-3" style="color: red">Alert : All item sizes are used in all groups</h6>
    @endif


    <style>
        .ingredient-multi-select {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }

        .ingredient-column {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }

        .ingredient-list {
            height: 300px;
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid #eee;
            padding: 5px;
        }

        .ingredient-item {
            padding: 8px 10px;
            margin: 5px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ingredient-item:hover {
            background-color: #e9ecef;
        }

        .selected-list .ingredient-item {
            background-color: #d1e7dd;
        }

        .btn-select-all,
        .btn-deselect-all {
            width: 100%;
            margin-bottom: 10px;
            background-color: #0d6efd;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-deselect-all {
            background-color: #dc3545;
        }

        .required-asterisk {
            color: red;
        }

        .error-message {
            color: red;
            margin-top: 5px;
            font-size: 0.875em;
            display: none;
        }

        .selected-list:has(+ .error-message[style*="display: block"]) {
            border: 1px solid red !important;
        }

        /* Search input styles */
        .search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>

    <!-- Your existing CSS styles remain the same -->

    <div class="ingredient-multi-select">


        <!-- Available Item Masters -->
        <div class="ingredient-column">
            <h4>Available Item Size Masters</h4>
            <input type="text" class="search-input available-search" placeholder="Search available items...">
            <button type="button" class="btn btn-sm btn-primary btn-select-all">Select All <i
                    class="fa-solid fa-arrow-right"></i></button>
            <div class="ingredient-list available-list">
                @foreach ($availableItemMasters as $item)
                    <div class="ingredient-item" data-id="{{ $item->id }}"
                        data-search="{{ strtolower($item->item_name->name . ' ' . $item->size) }}"
                        data-name="{{ strtolower($item->item_name->name) }}" data-number="{{ strtolower($item->size) }}"
                        data-wight="{{ strtolower($item->weight) }}">
                        <span>{{ $item->item_name->name }} ( {{ $item->size ?? '' }} ) </span>
                        <i class="fa-solid fa-circle-check" style="font-size: 24px; color: green;"></i>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Selected Item Masters -->
        <div class="ingredient-column">
            <h4>Selected Item Size Masters <span class="required-asterisk">*</span></h4>
            <input type="text" class="search-input selected-search" placeholder="Search selected items...">
            <button type="button" class="btn btn-sm btn-danger btn-deselect-all"><i class="fa-solid fa-arrow-left"></i>
                Remove All</button>
            <div class="ingredient-list selected-list">
                @foreach ($selectedItemMasters as $id)
                    @if ($item = $itemNames->firstWhere('id', $id))
                        <div class="ingredient-item" data-id="{{ $id }}"
                            data-search="{{ strtolower($item->item_name->name . ' ' . $item->size) }}"
                            data-name="{{ strtolower($item->item_name->name) }}"
                            data-number="{{ strtolower($item->size) }}" data-wight="{{ strtolower($item->weight) }}">

                            <span>{{ $item->item_name->name }} ( {{ $item->size ?? '' }} )
                                
                            </span>
                            <i class="fa-solid fa-circle-xmark" style="font-size: 24px; color: #e43d4d;"></i>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="error-message">Please select at least one item</div>
        </div>

        <!-- Hidden select element -->
        <select class="hidden-select" name="item_master_id[]" multiple required style="display:none;">
            @foreach ($selectedItemMasters as $id)
                <option value="{{ $id }}" selected></option>
            @endforeach
        </select>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.ingredient-multi-select');
        const availableList = container.querySelector('.available-list');
        const selectedList = container.querySelector('.selected-list');
        const selectAllBtn = container.querySelector('.btn-select-all');
        const deselectAllBtn = container.querySelector('.btn-deselect-all');
        const hiddenSelect = container.querySelector('.hidden-select');
        const errorMessage = container.querySelector('.error-message');

        // Search functionality
        const availableSearch = container.querySelector('.available-search');
        const selectedSearch = container.querySelector('.selected-search');

        // Enhanced filter function
        function filterItems(list, searchTerm) {
            const items = list.querySelectorAll('.ingredient-item');
            const searchLower = searchTerm.toLowerCase().trim();
            let visibleItems = 0;

            items.forEach(item => {
                const name = item.dataset.name;
                const number = item.dataset.number;
                const fullText = item.dataset.search;

                const matches = name.includes(searchLower) ||
                    number.includes(searchLower) ||
                    fullText.includes(searchLower);

                if (matches) {
                    item.style.display = 'flex';
                    visibleItems++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (list === availableList) {
                selectAllBtn.disabled = visibleItems === 0;
            } else {
                deselectAllBtn.disabled = visibleItems === 0;
            }
        }

        // Search event listeners with debounce
        let searchTimeout;

        function handleSearch(input, list) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterItems(list, input.value);
            }, 300);
        }

        availableSearch.addEventListener('input', () => handleSearch(availableSearch, availableList));
        selectedSearch.addEventListener('input', () => handleSearch(selectedSearch, selectedList));

        // Toggle ingredient selection
        function toggleIngredientSelection(item) {
            if (item.parentElement === availableList) {
                const icon = item.querySelector('i');
                icon.classList.replace('fa-circle-check', 'fa-circle-xmark');
                icon.style.color = '#e43d4d';
                selectedList.appendChild(item);
                addToHiddenSelect(item.dataset.id);
            } else {
                const icon = item.querySelector('i');
                icon.classList.replace('fa-circle-xmark', 'fa-circle-check');
                icon.style.color = 'green';
                returnToAvailableList(item);
                removeFromHiddenSelect(item.dataset.id);
            }
            validateSelection();
            filterItems(availableList, availableSearch.value);
            filterItems(selectedList, selectedSearch.value);
        }

        function returnToAvailableList(item) {
            const items = Array.from(availableList.children);
            const itemName = item.textContent.trim().toLowerCase();
            let insertBefore = null;

            for (const existingItem of items) {
                if (existingItem.textContent.trim().toLowerCase() > itemName) {
                    insertBefore = existingItem;
                    break;
                }
            }

            if (insertBefore) {
                availableList.insertBefore(item, insertBefore);
            } else {
                availableList.appendChild(item);
            }
        }

        function addToHiddenSelect(id) {
            const option = document.createElement('option');
            option.value = id;
            option.selected = true;
            hiddenSelect.appendChild(option);
        }

        function removeFromHiddenSelect(id) {
            const options = hiddenSelect.querySelectorAll(`option[value="${id}"]`);
            options.forEach(option => option.remove());
        }

        selectAllBtn.addEventListener('click', function() {
            const items = Array.from(availableList.querySelectorAll('.ingredient-item'));
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    const icon = item.querySelector('i');
                    icon.classList.replace('fa-circle-check', 'fa-circle-xmark');
                    icon.style.color = '#e43d4d';
                    selectedList.appendChild(item);
                    addToHiddenSelect(item.dataset.id);
                }
            });
            validateSelection();
            filterItems(availableList, availableSearch.value);
            filterItems(selectedList, selectedSearch.value);
        });

        deselectAllBtn.addEventListener('click', function() {
            const items = Array.from(selectedList.querySelectorAll('.ingredient-item'));
            items.forEach(item => {
                if (item.style.display !== 'none') {
                    const icon = item.querySelector('i');
                    icon.classList.replace('fa-circle-xmark', 'fa-circle-check');
                    icon.style.color = 'green';
                    returnToAvailableList(item);
                    removeFromHiddenSelect(item.dataset.id);
                }
            });
            validateSelection();
            filterItems(availableList, availableSearch.value);
            filterItems(selectedList, selectedSearch.value);
        });

        function validateSelection() {
            const hasSelection = selectedList.querySelectorAll('.ingredient-item')
                .length > 0;
            errorMessage.style.display = hasSelection ? 'none' : 'block';
            hiddenSelect.required = !hasSelection;
        }

        document.querySelector('form')?.addEventListener('submit', function(e) {
            if (selectedList.querySelectorAll('.ingredient-item').length ===
                0) {
                e.preventDefault();
                errorMessage.style.display = 'block';
                selectedList.style.border = '1px solid red';
            }
        });

        function setupItemClickHandlers() {
            container.querySelectorAll('.ingredient-item').forEach(item => {
                item.addEventListener('click', function() {
                    toggleIngredientSelection(this);
                });
            });
        }

        // Initialize
        setupItemClickHandlers();
        validateSelection();
        filterItems(availableList, '');
        filterItems(selectedList, '');
    });
</script>
