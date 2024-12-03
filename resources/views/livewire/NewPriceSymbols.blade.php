@push('style')
    <style>
        .bg-green-100 {
            background-color: #d4edda;
            transition: background-color 1s ease-in-out;
        }

        .table-row {
            transition: background-color 1s ease-in-out;
        }
    </style>
@endpush

@push('script_head')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // تمكين تسجيل Pusher - يجب تعطيله في الإنتاج
        Pusher.logToConsole = true;

        // إعداد اتصال Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true // استخدام اتصال مشفر
        });

        // الاشتراك في القناة
        var channel = pusher.subscribe('my-channel');

        // الاستماع إلى حدث priceUpdated
        channel.bind('priceUpdated', function(data) {
            if (data.prices && Array.isArray(data.prices)) {
                console.log('Received prices:', data.prices);
                Livewire.dispatch('priceUpdated', data.prices);
            } else {
                console.error('Invalid prices data:', data);
            }
        });


        let sidebarNotification = document.createElement('div');
        sidebarNotification.id = 'sidebar-notification';
        sidebarNotification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4caf50;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        display: none;
        z-index: 9999;
    `;
        sidebarNotification.innerHTML = 'جاري تحديث الأسعار...';
        // document.body.appendChild(sidebarNotification);
    </script>
@endpush


{{-- <div>
    <div class="py-12" x-data="{
        createModal: false,
        editModal: false,
        deleteModal: false,
        init() {
            Livewire.on('close-modal', () => {
                this.createModal = false;
                this.editModal = false;
                this.deleteModal = false;
            });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg">
                <div id="sidebar-notification"
                    class="hidden fixed top-20 right-10 bg-green-500 text-white p-4 rounded shadow-lg">
                    جاري تحديث الأسعار...
                </div>

                <div class="flex flex-wrap items-center justify-between space-y-2 sm:space-y-0">
                    <!-- معلومات الرصيد -->
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <h6 class="text-gray-600 font-medium">الرصيد الحالي:</h6>
                        <span class="text-lg font-semibold text-gray-800">
                            {{ number_format($totalCurrentValue ?? 0, 0) }} $
                        </span>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                        <!-- زر إضافة عملة جديدة -->
                        <button @click="createModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2 rtl:space-x-reverse">
                            <i class="fas fa-plus-circle"></i>
                            <span>إضافة عملة جديدة</span>
                        </button>

                        <!-- زر التحديث اليدوي -->
                        <button type="button"
                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center space-x-2 rtl:space-x-reverse"
                            wire:click="updatePricesManually" wire:loading.attr="disabled">
                            <i class="fas fa-sync-alt"></i>
                            <span>تحديث يدوي</span>
                        </button>

                        <!-- زر بث البيانات -->
                        <button type="button"
                            class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 flex items-center space-x-2 rtl:space-x-reverse"
                            wire:click="broadcastTest" wire:loading.attr="disabled">
                            <i class="fas fa-broadcast-tower"></i>
                            <span>بث البيانات</span>
                        </button>
                    </div>
                </div>

                <!-- أزرار تغيير الترتيب -->
                <div class="flex items-center justify-end space-x-2 rtl:space-x-reverse mt-4">
                    <!-- زر ترتيب حسب السعر الحالي -->
                    <button wire:click="sortBy('current_value')"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-dollar-sign"></i>
                        <span>ترتيب حسب السعر الحالي</span>
                    </button>

                    <!-- زر ترتيب حسب نسبة التغير -->
                    <button wire:click="sortBy('percentage_change')"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-chart-line"></i>
                        <span>ترتيب حسب نسبة التغير</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 text-sm text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                            <tr>
                                <th class="py-3 px-4 border-b">#</th>
                                <th class="py-3 px-4 border-b">اسم العملة</th>
                                <th class="py-3 px-4 border-b">السعر الحالي</th>
                                <th class="py-3 px-4 border-b">متوسط سعر الشراء</th>
                                <th class="py-3 px-4 border-b">نسبة التغير</th>
                                <th class="py-3 px-4 border-b">الكمية التي تم شراؤها</th>
                                <th class="py-3 px-4 border-b">مبلغ الشراء</th>
                                <th class="py-3 px-4 border-b">قيمة المبلغ الآن</th>
                                <th class="py-3 px-4 border-b">إجراءات</th>
                                <th class="py-3 px-4 border-b"> هدف معلق</th>
                                <th class="py-3 px-4 border-b"> ربح المعلق</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pricesSymbols as $Price_Symbol)
                                @php
                                    $percentageChange = $Price_Symbol->percentage_change;

                                    if ($percentageChange >= 0 && $percentageChange < 50) {
                                        $percentageChangeClass = 'bg-green-100 text-green-800';
                                    } elseif ($percentageChange >= 50 && $percentageChange < 100) {
                                        $percentageChangeClass = 'bg-green-200 text-green-800';
                                    } elseif ($percentageChange >= 100 && $percentageChange < 300) {
                                        $percentageChangeClass = 'bg-green-300 text-green-800';
                                    } elseif ($percentageChange >= 300) {
                                        $percentageChangeClass = 'bg-green-500 text-green-800';
                                    } else {
                                        $percentageChangeClass = 'bg-red-100 text-red-800';
                                    }

                                    $currentPriceClass = 'bg-blue-100 text-blue-800 font-semibold';
                                    $currentValueClass =
                                        $Price_Symbol->current_value >= $Price_Symbol->purchase_amount
                                            ? 'bg-green-100 text-green-800 font-semibold'
                                            : 'bg-red-100 text-red-800 font-semibold';
                                @endphp
                                <tr class="border-b text-center" x-data="{ visible: true }" x-show="visible"
                                    x-transition.duration.500ms>
                                    <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-4">{{ $Price_Symbol->currency_name }}USDT</td>
                                    <td class="py-3 px-4 {{ $currentPriceClass }}">
                                        {{ number_format($Price_Symbol->current_price ?? 0, 3) }} $</td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->average_buy_price, 3) }} $
                                    </td>
                                    <td class="py-3 px-4 font-black text-lg {{ $percentageChangeClass }}">
                                        {{ number_format($Price_Symbol->percentage_change ?? 0, 1) }}%</td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->quantity, 2) }}</td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->purchase_amount, 1) }} $</td>
                                    <td class="py-3 px-4 font-black {{ $currentValueClass }}">
                                        {{ number_format($Price_Symbol->current_value, 0) }} $</td>
                                    <td class="py-3 px-4 flex space-x-2 rtl:space-x-reverse">
                                        <!-- Edit Currency Button -->
                                        <button wire:click="editCurrencyR({{ $Price_Symbol->id }})"
                                            @click="editModal = true"
                                            class="px-3 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center space-x-2 text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Delete Currency Button -->
                                        <button wire:click="confirmDelete({{ $Price_Symbol->id }})"
                                            @click="deleteModal = true"
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center space-x-2 text-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->target, 3) }}$</td>
                                    <td class="py-3 px-4 bg-green-100 text-green-800">
                                        {{ number_format($Price_Symbol->afterSell, 0) }}$</td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-900 text-white">
                                <td class="py-3 px-4 text-end fw-bold" colspan="2">إجمالي مبلغ الشراء</td>
                                <td class="py-3 px-4 text-center fw-bold" colspan="1">
                                    {{ number_format($totalPurchaseAmount, 0) }} $</td>
                                <td class="py-3 px-4 text-end fw-bold" colspan="2">إجمالي المبلغ الحالي</td>
                                <td class="py-3 px-4 text-center fw-bold" colspan="2">
                                    {{ number_format($totalCurrentValue, 0) }} $</td>
                                <td class="py-3 px-4 text-end fw-bold" colspan="2">إجمالي المبلغ المستهدف</td>
                                <td class="py-3 px-4 text-center fw-bold" colspan="1">
                                    {{ number_format($totalAfterSell, 0) }} $</td>
                                <td class="py-3 px-4 text-center" colspan="1"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        @include('models.modals')
        <!-- مؤشر تحميل في أعلى الصفحة -->
        <div wire:loading wire:target="updatePricesManually, broadcastTest"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="loader"></div> <!-- يمكن تخصيص شكل المؤشر -->
            <span class="text-white font-bold mt-4">جاري تحديث البيانات...</span>
        </div>
    </div>
</div> --}}

<div class="py-12" x-data="{
    createModal: false,
    editModal: false,
    deleteModal: false,
    init() {
        Livewire.on('close-modal', () => {
            this.createModal = false;
            this.editModal = false;
            this.deleteModal = false;
        });
    },
    columns: {
        name: true,
        currentPrice: true,
        avgPrice: true,
        percentageChange: true,
        quantity: true,
        purchaseAmount: true,
        currentValue: true,
        target: true,
        profit: true,
        actions: true,
    }
}">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <!-- شريط الإشعار -->
            <div id="sidebar-notification"
                class="hidden fixed top-20 right-10 bg-green-500 text-white p-4 rounded shadow-lg">
                جاري تحديث الأسعار...
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <!-- قسم الإجماليات -->
                <div class="flex flex-wrap items-center justify-around mb-6">
                    <div class="flex flex-col items-center space-y-1">
                        <span class="text-2xl font-bold text-gray-800">
                            {{ number_format($totalPurchaseAmount ?? 0, 0) }} $
                        </span>
                        <h6 class="text-gray-600 font-medium text-lg">إجمالي مبلغ الشراء</h6>
                    </div>
                    <div class="flex flex-col items-center space-y-1">
                        <span class="text-2xl font-bold text-gray-800">
                            {{ number_format($totalAfterSell ?? 0, 0) }} $
                        </span>
                        <h6 class="text-gray-600 font-medium text-lg">إجمالي المبلغ المستهدف</h6>
                    </div>
                    <div class="flex flex-col items-center space-y-1">
                        <span class="text-2xl font-bold text-gray-800">
                            {{ number_format($totalCurrentValue ?? 0, 0) }} $
                        </span>
                        <h6 class="text-gray-600 font-medium text-lg">الرصيد الحالي</h6>
                    </div>
                </div>

                <!-- قسم أزرار الإجراءات -->
                <div class="flex flex-wrap items-center justify-center space-x-2 rtl:space-x-reverse">
                    <!-- زر إضافة عملة جديدة -->
                    <button @click="createModal = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2 rtl:space-x-reverse">
                        <i class="fas fa-plus-circle"></i>
                        <span>إضافة عملة جديدة</span>
                    </button>

                    {{-- <!-- زر التحديث اليدوي -->
                    <button type="button"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center space-x-2 rtl:space-x-reverse"
                        wire:click="updatePricesManually" wire:loading.attr="disabled">
                        <i class="fas fa-sync-alt"></i>
                        <span>تحديث يدوي</span>
                    </button> --}}

                    <!-- زر بث البيانات -->
                    <button type="button"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center space-x-2 rtl:space-x-reverse"
                        wire:click="updatePrices" wire:loading.attr="disabled">
                        <i class="fas fa-broadcast-tower"></i>
                        <span>بث البيانات</span>
                    </button>
                </div>
            </div>

            <!-- جدول البيانات -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="py-3 px-4 border-b"></th>
                            <template x-for="(visible, column) in columns" :key="column">
                                <th class="py-3 px-4 border-b">
                                    <button @click="columns[column] = !columns[column]"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center space-x-2 rtl:space-x-reverse">
                                        <i :class="columns[column] ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
                                        {{-- <span x-text="column"></span> --}}
                                    </button>
                                </th>
                            </template>
                        </tr>
                        <tr>
                            <th class="py-1 px-2 border-b">#</th>
                            <th class="py-2 px-2 border-b" x-show="columns.name">اسم العملة</th>
                            <th class="py-3 px-4 border-b" x-show="columns.currentPrice">السعر الحالي</th>
                            <th class="py-3 px-4 border-b" x-show="columns.avgPrice">متوسط سعر الشراء</th>
                            <th class="py-3 px-4 border-b" x-show="columns.percentageChange">
                                <button wire:click="sortBy('percentage_change')"
                                    class="flex items-center justify-center space-x-1 rtl:space-x-reverse text-green-600 hover:underline">
                                    <i class="fas fa-chart-line"></i>
                                    <span>نسبة التغير</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b" x-show="columns.quantity">الكمية التي تم شراؤها</th>
                            <th class="py-3 px-4 border-b" x-show="columns.purchaseAmount">مبلغ الشراء</th>
                            <th class="py-3 px-4 border-b" x-show="columns.currentValue">
                                <button wire:click="sortBy('current_value')"
                                    class="flex items-center justify-center space-x-1 rtl:space-x-reverse text-blue-600 hover:underline">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span> قيمة المبلغ الآن</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b" x-show="columns.target">هدف معلق</th>
                            <th class="py-3 px-4 border-b" x-show="columns.profit">
                                <button wire:click="sortBy('afterSell')"
                                    class="flex items-center justify-center space-x-1 rtl:space-x-reverse text-green-600 hover:underline">
                                    <i class="fas fa-chart-line"></i>
                                    <span> ربح المعلق</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b" x-show="columns.actions">إجراءات</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pricesSymbols as $Price_Symbol)
                            @php
                                $percentageChange = $Price_Symbol->percentage_change;
                                $percentageChangeClass = match (true) {
                                    $percentageChange >= 0 && $percentageChange < 50 => 'bg-green-100 text-green-800',
                                    $percentageChange >= 50 && $percentageChange < 100 => 'bg-green-200 text-green-800',
                                    $percentageChange >= 100 && $percentageChange < 300
                                        => 'bg-green-300 text-green-800',
                                    $percentageChange >= 300 => 'bg-green-500 text-green-800',
                                    default => 'bg-red-100 text-red-800',
                                };
                                $currentPriceClass = 'bg-blue-100 text-blue-800 font-semibold';
                                $currentValueClass =
                                    $Price_Symbol->current_value >= $Price_Symbol->purchase_amount
                                        ? 'bg-green-100 text-green-800 font-semibold'
                                        : 'bg-red-100 text-red-800 font-semibold';
                            @endphp
                            <tr class="border-b text-center">
                                <td class="py-1 px-2">{{ $loop->iteration }}</td>
                                <td class="py-2 px-2" x-show="columns.name">{{ $Price_Symbol->currency_name }}USDT</td>
                                <td class="py-3 px-4 {{ $currentPriceClass }}" x-show="columns.currentPrice">
                                    {{ number_format($Price_Symbol->current_price ?? 0, 3) }} $</td>
                                <td class="py-3 px-4" x-show="columns.avgPrice">
                                    {{ number_format($Price_Symbol->average_buy_price, 3) }} $</td>
                                <td class="py-3 px-4 font-black text-lg {{ $percentageChangeClass }}"
                                    x-show="columns.percentageChange">
                                    {{ number_format($Price_Symbol->percentage_change ?? 0, 1) }}%</td>
                                <td class="py-3 px-4" x-show="columns.quantity">
                                    {{ number_format($Price_Symbol->quantity, 2) }}</td>
                                <td class="py-3 px-4" x-show="columns.purchaseAmount">
                                    {{ number_format($Price_Symbol->purchase_amount, 1) }} $</td>
                                <td class="py-3 px-4 font-black {{ $currentValueClass }}"
                                    x-show="columns.currentValue">{{ number_format($Price_Symbol->current_value, 0) }}
                                    $</td>
                                <td class="py-3 px-4" x-show="columns.target">
                                    {{ number_format($Price_Symbol->target, 3) }}$</td>
                                <td class="py-3 px-4  bg-green-100 text-green-800" x-show="columns.profit">
                                    {{ number_format($Price_Symbol->afterSell, 0) }}$</td>
                                <td class="py-3 px-4 flex space-x-2 rtl:space-x-reverse" x-show="columns.actions">
                                    <!-- Edit Button -->
                                    <button wire:click="editCurrency({{ $Price_Symbol->id }})"
                                        @click="editModal = true"
                                        class="px-2 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center space-x-2 text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Delete Button -->
                                    <button wire:click="confirmDelete({{ $Price_Symbol->id }})"
                                        @click="deleteModal = true"
                                        class="px-2 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center space-x-2 text-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- مؤشر تحميل -->
            <div wire:loading wire:target="updatePricesManually, broadcastTest"
                class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="loader"></div>
                <span class="text-white font-bold mt-4">جاري تحديث البيانات...</span>
            </div>
        </div>
    </div>
    @include('models.modals')
</div>



@push('script')
    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script>
        Livewire.on('success-message', (message) => {
            toastr.success(event.detail.message);
        });
        Livewire.on('error-message', (message) => {
            toastr.error(event.detail.message);
        });
        Livewire.on('closeLoading', (message) => {
            toastr.success(event.detail.message);
            sidebarNotification.style.display = 'none'; // إخفاء الإشعار الجانبي إذا كان ظاهرًا
        });
        Livewire.on('currency-added', (id) => {
            const newRow = document.getElementById(`row-${event.detail.id}`);
            if (newRow) {
                // تأكد من إزالة أي تأثيرات سابقة
                newRow.classList.remove('bg-green-100');

                // أضف التأثير
                newRow.classList.add('bg-green-100');

                // إزالة التأثير بعد 2 ثانية
                setTimeout(() => {
                    newRow.classList.remove('bg-green-100');
                }, 2000);
            }
        });

        Livewire.on('currency-deleted', (id) => {
            const deletedRow = document.getElementById(`row-${event.detail.id}`);
            if (deletedRow) {
                // تقليل الشفافية تدريجيًا
                deletedRow.style.transition = 'opacity 0.5s ease';
                deletedRow.style.opacity = '0';

                // إزالة العنصر بعد انتهاء التأثير
                setTimeout(() => {
                    deletedRow.remove();
                }, 500);
            }
        });



        // الاستماع للحدث priceUpdated من Pusher وتحديث Livewire
        Livewire.on('priceUpdated', (prices) => {
            const sidebarNotification = document.getElementById('sidebar-notification');
            if (sidebarNotification) {
                sidebarNotification.style.display = 'block';
                setTimeout(() => {
                    sidebarNotification.style.display = 'none';
                }, 3000);
            }

            // تحديث الصفوف بناءً على الأسعار الجديدة
            prices.forEach((price) => {
                let row = document.querySelector(`#row-${price.symbol}`);
                if (row) {
                    // تأثيرات بصرية لتحديث الصف
                    row.classList.add('bg-green-100');
                    setTimeout(() => row.classList.remove('bg-green-100'), 2000);
                }
            });

            // عرض إشعار جانبي باستخدام Toastr
            // toastr.info('تم تحديث الأسعار بنجاح لحظيا !!');

        });
    </script>
@endpush
