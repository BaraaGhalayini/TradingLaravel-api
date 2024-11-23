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
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h3>إدارة العملات الرقمية</h3>
                    <div class="d-flex">
                        <h6> الرصيد الحالي : </h6> {{ $totalCurrentValue }}

                        <!-- Add New Currency Button -->
                        <button @click="createModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center space-x-2">
                            <i class="fas fa-plus-circle"></i>
                            <span>إضافة عملة جديدة</span>
                        </button>

                        <!-- Refresh Button -->
                        <button type="button" class="btn btn-warning" wire:click="updatePricesManually">
                            تحديث يدوي
                        </button>

                        <button type="button" class="btn btn-primary" wire:click="broadcastTest">
                            بث البيانات يدويًا
                        </button>


                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-center">
                        <thead>
                            <tr>
                                <th scope="col"><i class="fas fa-hashtag"></i></th> <!-- رقم -->
                                <th scope="col"><i class="fas fa-coins"></i> اسم العملة</th> <!-- اسم العملة -->
                                <th scope="col"><i class="fas fa-dollar-sign"></i> السعر الحالي</th>
                                <!-- السعر الحالي -->
                                <th scope="col"><i class="fas fa-chart-line"></i> متوسط سعر الشراء</th>
                                <!-- متوسط سعر الشراء -->
                                <th scope="col"><i class="fas fa-percentage"></i> نسبة التغير</th>
                                <!-- نسبة التغير -->
                                <th scope="col"><i class="fas fa-boxes"></i> الكمية التي تم شراؤها</th>
                                <!-- الكمية التي تم شراؤها -->
                                <th scope="col"><i class="fas fa-wallet"></i> مبلغ الشراء</th> <!-- مبلغ الشراء -->
                                <th scope="col"><i class="fas fa-money-bill-wave"></i> قيمة المبلغ الآن</th>
                                <!-- قيمة المبلغ الآن -->
                                <th scope="col"><i class="fas fa-cogs"></i> إجراءات</th>
                                <!-- العمود لإظهار أزرار التعديل والحذف -->
                            </tr>
                        </thead>

                        <tbody id="pricesTableBody">
                            @foreach ($pricesSymbols as $Price_Symbol)
                                @php
                                    $percentageChange = $Price_Symbol->percentage_change;
                                    $percentageChangeClass =
                                        $percentageChange >= 0 ? 'bg-success text-light' : 'bg-danger text-light';
                                    $currentPriceClass = 'bg-info text-dark fw-bold';
                                    // $currentValueClass = 'bg-primary text-light';
                                    $currentValueClass =
                                        $Price_Symbol->current_value >= $Price_Symbol->purchase_amount
                                            ? 'bg-success text-light'
                                            : 'bg-danger text-light fw-bold';
                                @endphp
                                <tr>
                                    <td scope="row">{{ $loop->iteration }}</td>
                                    <td scope="row" class="symbol ">{{ $Price_Symbol->currency_name }}USDT</td>
                                    <td scope="row" class="{{ $currentPriceClass }}">
                                        {{ number_format($Price_Symbol->current_price ?? 0, 3) }} $</td>
                                    <td scope="row">{{ number_format($Price_Symbol->average_buy_price, 3) }} $</td>
                                    <td scope="row" class="{{ $percentageChangeClass }}">
                                        {{ number_format($Price_Symbol->percentage_change ?? 0, 2) }}%
                                    </td>
                                    <td scope="row">{{ number_format($Price_Symbol->quantity, 2) }}</td>
                                    <td scope="row">{{ number_format($Price_Symbol->purchase_amount, 1) }} $</td>
                                    <td scope="row" class="{{ $currentValueClass }}">
                                        {{ number_format($Price_Symbol->current_value, 1) }} $</td>
                                    <td scope="row">
                                        <!-- Edit Currency Button -->
                                        <button wire:click="editCurrency({{ $Price_Symbol->id }})"
                                            @click="editModal = true"
                                            class="px-3 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center space-x-2 text-sm">
                                            <i class="fas fa-edit"></i>
                                            {{-- <span>تعديل</span> --}}
                                        </button>

                                        <!-- Delete Currency Button -->
                                        <button wire:click="confirmDelete({{ $Price_Symbol->id }})"
                                            @click="deleteModal = true"
                                            class="px-3
                                            py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center
                                            space-x-2 text-sm">
                                            <i class="fas fa-trash-alt"></i>
                                            {{-- <span>حذف</span> --}}
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
</div>


@push('script')
    <!-- Include Bootstrap JS and jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script> --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // عرض رسالة مخزنة في localStorage باستخدام Toastr
        $(document).ready(function() {
            const toastrMessage = localStorage.getItem('toastrMessage');
            if (toastrMessage) {
                toastr.success(toastrMessage);
                localStorage.removeItem('toastrMessage'); // إزالة الرسالة بعد عرضها
            }
        });

        // Livewire.on('close-modal', () => {
        //     document.querySelector('[x-data]').__x.$data.editModal = false;
        // });


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

        // إظهار إشعارات بناءً على جلسة Laravel
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    </script>
@endpush
