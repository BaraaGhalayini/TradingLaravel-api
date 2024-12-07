<!-- Modal لإضافة عملة جديدة -->
<div class="fixed inset-0 z-10 flex items-center justify-center bg-black bg-opacity-50" x-show="createModal" x-cloak
    @click.away="createModal = false; $wire.dispatch('close-modal')">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h5 class="text-lg font-bold">إضافة عملة جديدة</h5>
            <button class="text-gray-400 hover:text-gray-600" @click="createModal = false">
                &times;
            </button>
        </div>
        <div class="px-6 py-4">
            <form wire:submit="addCurrency">
                <div class="mb-4">
                    <label for="currencyName" class="block text-sm font-medium text-gray-700">اسم العملة</label>
                    <input type="text" id="currencyName"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        wire:model.live="newCurrency.currency_name">
                    @error('newCurrency.
                    ')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="averageBuyPrice" class="block text-sm font-medium text-gray-700">متوسط سعر
                        الشراء</label>
                    <input type="number" id="averageBuyPrice"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        step="0.0001" wire:model.live="newCurrency.average_buy_price">
                    @error('newCurrency.average_buy_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية</label>
                    <input type="number" id="quantity"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        step="0.0001" wire:model.live="newCurrency.quantity">
                    @error('newCurrency.quantity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="purchaseAmount" class="block text-sm font-medium text-gray-700">مبلغ الشراء</label>
                    <input type="text" id="purchaseAmount"
                        class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm"
                        value="{{ $this->getCalculatedValueProperty() }}" readonly>

                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal لتعديل العملة -->
<div x-show="editModal" x-cloak>
    <div class="fixed inset-0 z-10 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h5 class="text-lg font-bold">تعديل العملة</h5>
                <button class="text-gray-400 hover:text-gray-600" @click="editModal = false">
                    &times;
                </button>
            </div>
            <div class="px-6 py-4">

                @if ($editCurrency)
                    <form wire:submit="updateCurrency">
                        <div class="mb-4">
                            <label for="editCurrencyName" class="block text-sm font-medium text-gray-700">اسم
                                العملة</label>
                            <input type="text" id="editCurrencyName"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                wire:model.live="editCurrency.currency_name">
                            @error('editCurrency.currency_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="editAverageBuyPrice" class="block text-sm font-medium text-gray-700">متوسط
                                سعر الشراء</label>
                            <input type="number" id="editAverageBuyPrice"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                step="0.0001" wire:model.live="editCurrency.average_buy_price">
                            @error('editCurrency.average_buy_price')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="editQuantity" class="block text-sm font-medium text-gray-700">الكمية</label>
                            <input type="number" id="editQuantity"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                step="0.0001" wire:model.live="editCurrency.quantity">
                            @error('editCurrency.quantity')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="editPurchaseAmount" class="block text-sm font-medium text-gray-700">مبلغ
                                الشراء</label>
                            <input type="text" id="editPurchaseAmount"
                                class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm"
                                value="{{ @$editCurrency['average_buy_price'] * @$editCurrency['quantity'] ?? 0 }}"
                                readonly>
                        </div>
                        <div class="mb-4">
                            <label for="target" class="block text-sm font-medium text-gray-700">الهدف</label>
                            <input type="number" id="target"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                step="0.0001" wire:model.live="editCurrency.target">
                            @error('editCurrency.target')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">حفظ
                                التعديلات</button>
                        </div>

                    </form>
                @else
                    <p class="text-center text-gray-500">لا توجد بيانات لتحريرها.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal for Deleting Currency -->
<div class="fixed inset-0 z-10 flex items-center justify-center bg-black bg-opacity-50" x-show="deleteModal" x-cloak>
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h5 class="text-lg font-bold">تأكيد الحذف</h5>
            <button class="text-gray-400 hover:text-gray-600" @click="deleteModal = false">
                &times;
            </button>
        </div>
        <div class="px-6 py-4 text-center">
            <p class="text-gray-700">هل أنت متأكد أنك تريد حذف هذه العملة؟</p>
        </div>
        <div class="flex justify-end px-6 py-4">
            <button class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300"
                @click="deleteModal = false">إلغاء</button>
            <button wire:click="deleteCurrency"
                class="ml-2 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">حذف</button>
        </div>
    </div>
</div>
