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
                <div class="flex justify-between items-center p-6">
                    <h3 class="text-xl font-bold text-gray-800">إدارة العملات الرقمية</h3>
                    <div class="flex space-x-4 rtl:space-x-reverse">
                        <h6 class="text-gray-600 font-medium">الرصيد الحالي:</h6>
                        <span class="text-lg font-semibold text-gray-800">{{ $totalCurrentValue }}</span>

                        <!-- Add New Currency Button -->
                        <button @click="createModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                            <i class="fas fa-plus-circle"></i>
                            <span>إضافة عملة جديدة</span>
                        </button>

                        <!-- Refresh Button -->
                        <button type="button" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600"
                            wire:click="updatePricesManually">
                            تحديث يدوي
                        </button>

                        <button type="button" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600"
                            wire:click="broadcastTest">
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
                                <tr class="border-b text-center">
                                    <td class="py-3 px-4">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-4">{{ $Price_Symbol->currency_name }}USDT</td>
                                    <td class="py-3 px-4 {{ $currentPriceClass }}"> {{ number_format($Price_Symbol->current_price ?? 0, 3) }} $</td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->average_buy_price, 3) }} $</td>
                                    <td class="py-3 px-4 font-black text-lg {{ $percentageChangeClass }}">{{ number_format($Price_Symbol->percentage_change ?? 0, 1) }}%</td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->quantity, 2) }}</td>
                                    <td class="py-3 px-4">{{ number_format($Price_Symbol->purchase_amount, 1) }} $</td>
                                    <td class="py-3 px-4 font-black {{ $currentValueClass }}">{{ number_format($Price_Symbol->current_value, 0) }} $</td>
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
    </div>
    <

</div>

@push('script')
    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });

        // الاستماع للحدث priceUpdated من Pusher وتحديث Livewire
        Livewire.on('priceUpdated', (prices) => {
            prices.forEach(function(price) {
                let row = document.querySelector(`#row-${price.symbol}`);
                if (row) {
                    row.classList.add('bg-success'); // إضافة تأثير بصري
                    setTimeout(() => row.classList.remove('bg-success'), 2000); // إزالة التأثير بعد 2 ثانية

                    // تحديث الصف باستخدام Livewire
                    Livewire.emit('updateRow', price.symbol, price.price);
                }
            });

            toastr.success('تم تحديث الأسعار بنجاح!');
        });
    </script>
@endpush

