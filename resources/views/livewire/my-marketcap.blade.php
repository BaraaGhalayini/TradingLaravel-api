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
        marketCapRank: true,
        marketCap: true,
        volume24h: true,
        priceChange24h: true,
        circulatingSupply: true,
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
                {{-- <div class="flex flex-wrap items-center justify-around mb-6">
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
                </div> --}}

                <!-- قسم أزرار الإجراءات -->
                <div class="flex flex-wrap items-center justify-center space-x-2 rtl:space-x-reverse">
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
            <div x-data="{ sortField: @entangle('sortField'), sortDirection: @entangle('sortDirection') }" class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 text-sm text-gray-600">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="py-3 px-4 border-b">#</th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('currency_name')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'currency_name' && sortDirection === 'asc' ? 'fa-sort-up' :
                                            'fa-sort-down'"></i>
                                    <span>اسم العملة</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('current_price')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'sector' && sortDirection === 'asc' ? 'fa-sort-up' :
                                            'fa-sort-down'"></i>
                                    <span>القطاع</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('current_price')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'current_price' && sortDirection === 'asc' ? 'fa-sort-up' :
                                            'fa-sort-down'"></i>
                                    <span>السعر الحالي</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('market_cap_rank')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'market_cap_rank' && sortDirection === 'asc' ? 'fa-sort-up' :
                                            'fa-sort-down'"></i>
                                    <span>الترتيب</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('market_cap')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'market_cap' && sortDirection === 'asc' ? 'fa-sort-up' :
                                            'fa-sort-down'"></i>
                                    <span>القيمة السوقية</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('volume_24h')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'volume_24h' && sortDirection === 'asc' ? 'fa-sort-up' :
                                            'fa-sort-down'"></i>
                                    <span>حجم التداول (24 ساعة)</span>
                                </button>
                            </th>
                            <th class="py-3 px-4 border-b">
                                <button wire:click="sortBy('price_change_24h')"
                                    class="flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fas"
                                        :class="sortField === 'price_change_24h' && sortDirection === 'asc' ?
                                            'fa-sort-up' : 'fa-sort-down'"></i>
                                    <span>التغيير (24 ساعة)</span>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pricesSymbols as $Price_Symbol)
                            @php
                                $priceChangeClass =
                                    $Price_Symbol->price_change_24h >= 0
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800';
                            @endphp

                            <tr class="border-b text-center">
                                <td class="py-1 px-2">{{ $loop->iteration }}</td>
                                <td class="py-3 px-4">{{ $Price_Symbol->currency_name }}</td>
                                <td class="py-3 px-4">{{ $Price_Symbol->sector ?? 'غير محدد' }}</td>
                                <td class="py-3 px-4">{{ number_format($Price_Symbol->current_price, 2) }} $</td>
                                <td class="py-3 px-4">{{ $Price_Symbol->market_cap_rank }}</td>
                                <td class="py-3 px-4">{{ number_format($Price_Symbol->market_cap, 0) }} $</td>
                                <td class="py-3 px-4">{{ number_format($Price_Symbol->volume_24h, 0) }} $</td>
                                <td class="py-3 px-4 {{ $priceChangeClass }}">{{ number_format($Price_Symbol->price_change_24h, 2) }}%</td>
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
