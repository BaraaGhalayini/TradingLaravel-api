@push('script_head')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('e1d45d9b703669bce3ca', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
@endpush

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h3>إدارة العملات الرقمية</h3>
                    <div class="d-flex">
                        <h6> الرصيد الحالي : </h6> {{ $totalCurrentValue }}

                        <!-- Add New Currency Button -->
                        <a href="#" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fas fa-plus-circle"></i> إضافة عملة جديدة
                        </a>

                        <!-- Refresh Button -->
                        <button type="button" class="btn btn-secondary" id="refreshButton">
                            <i class="fas fa-sync"></i> تحديث
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
                                        {{ number_format($Price_Symbol->current_price, 3) }} $</td>
                                    <td scope="row">{{ number_format($Price_Symbol->average_buy_price, 3) }} $</td>
                                    <td scope="row" class="{{ $percentageChangeClass }}">
                                        {{ number_format($percentageChange, 2) }}%</td>
                                    <td scope="row">{{ number_format($Price_Symbol->quantity, 2) }}</td>
                                    <td scope="row">{{ number_format($Price_Symbol->purchase_amount, 1) }} $</td>
                                    <td scope="row" class="{{ $currentValueClass }}">
                                        {{ number_format($Price_Symbol->current_value, 1) }} $</td>
                                    <td scope="row">
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editModal" data-id="{{ $Price_Symbol->id }}"
                                            data-name="{{ $Price_Symbol->currency_name }}"
                                            data-current-price="{{ $Price_Symbol->current_price }}"
                                            data-average-buy-price="{{ $Price_Symbol->average_buy_price }}"
                                            data-percentage-change="{{ $Price_Symbol->percentage_change }}"
                                            data-quantity="{{ $Price_Symbol->quantity }}"
                                            data-purchase-amount="{{ $Price_Symbol->purchase_amount }}"
                                            data-current-value="{{ $Price_Symbol->current_value }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Button to trigger the delete modal -->
                                        <form action="{{ route('price-symbols.destroy', $Price_Symbol->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-id="{{ $Price_Symbol->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('models.modals')

</div>

@push('script')
    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('build/assets/script.js') }}" crossorigin="anonymous"></script>


    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}');
        </script>
    @endif
@endpush
