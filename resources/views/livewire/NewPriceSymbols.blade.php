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
        var pusher = new Pusher('3df0992a1e99199d1e07', {
            cluster: 'eu',
            encrypted: true // استخدام اتصال مشفر
        });

        // الاشتراك في القناة
        var channel = pusher.subscribe('my-channel');

        // الاستماع إلى حدث priceUpdated
        channel.bind('priceUpdated', function(data) {
            if (data.prices) {
                console.log('Received prices:', data.prices); // عرض البيانات في وحدة التحكم
                Livewire.emit('priceUpdated', data.prices); // إرسال البيانات إلى Livewire
            } else {
                console.error('No prices received', data); // التعامل مع البيانات الفارغة
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


<div>
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

                <div class="flex justify-between items-center p-6">
                    <h3 class="text-xl font-bold text-gray-800">إدارة العملات الرقمية</h3>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <h6 class="text-gray-600 font-medium">الرصيد الحالي:</h6>
                        <span
                            class="text-lg font-semibold text-gray-800">{{ number_format($totalCurrentValue ?? 0, 0) }}
                            $</span>


                        <!-- Add New Currency Button -->
                        <button @click="createModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                            <i class="fas fa-plus-circle"></i>
                            <span>إضافة عملة جديدة</span>
                        </button>

                        <!-- Refresh Button -->
                        <button type="button" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600"
                            wire:click="updatePricesManually" wire:loading.attr="disabled">
                            تحديث يدوي
                        </button>

                        <button type="button" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600"
                            wire:click="broadcastTest" wire:loading.attr="disabled">
                            بث البيانات يدويًا
                        </button>
                    </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pricesSymbols as $Price_Symbol)
                                @php
                                    $percentageChange = $Price_Symbol->percentage_change;
                                    $percentageChangeClass =
                                        $percentageChange >= 0
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800';
                                    $currentPriceClass = 'bg-blue-100 text-blue-800 font-semibold';
                                    $currentValueClass =
                                        $Price_Symbol->current_value >= $Price_Symbol->purchase_amount
                                            ? 'bg-green-100 text-green-800'
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
                                </tr>
                            @endforeach
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

            // عرض إشعار جانبي باستخدام Toastr
            toastr.info('تم تحديث الأسعار بنجاح!');

            prices.forEach(function(price) {
                let row = document.querySelector(`#row-${price.symbol}`);
                if (row) {
                    row.classList.add('bg-success'); // إضافة تأثير بصري
                    setTimeout(() => row.classList.remove('bg-success'), 2000); // إزالة التأثير بعد 2 ثانية

                    // تحديث الصف باستخدام Livewire
                    Livewire.dispatch('updateRow', price.symbol, price.price);
                }
            });
        });
    </script>
@endpush
