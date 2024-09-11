<div>

    @section('script_head')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- تأكد من إضافة jQuery -->
    <script>
        $(document).ready(function() {

            // Enable pusher logging - don't include this in production
            // Pusher.logToConsole = true;

            // إعداد الاتصال بـ Pusher
            var pusher = new Pusher('e1d45d9b703669bce3ca', {
                cluster: 'eu'
            });

            var channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function(data) {
                console.log('Received data:', data); // إضافة هذا السطر للتأكد من تلقي البيانات
                // تحقق من صحة البيانات قبل التحديث
                if (data && Array.isArray(data)) {
                    updatePricesTable(data);
                }
            });

            // دالة لتحديث جدول الأسعار بناءً على البيانات الجديدة
            function updatePricesTable(data) {
                var $pricesTableBody = $('#pricesTableBody');
                $pricesTableBody.empty(); // مسح الجدول الحالي

                data.forEach(function(item, index) {
                    // التأكد من وجود جميع الحقول الضرورية
                    if (item.currency_name && item.current_price !== undefined && item.average_buy_price !== undefined && item.percentage_change !== undefined && item.quantity !== undefined && item.purchase_amount !== undefined && item.current_value !== undefined) {
                        var row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.currency_name}USDT</td>
                                <td class="bg-info text-dark fw-bold">${parseFloat(item.current_price).toFixed(3)} $</td>
                                <td>${parseFloat(item.average_buy_price).toFixed(3)} $</td>
                                <td class="${item.percentage_change >= 0 ? 'bg-success text-light' : 'bg-danger text-light'}">${parseFloat(item.percentage_change).toFixed(2)}%</td>
                                <td>${parseFloat(item.quantity).toFixed(2)}</td>
                                <td>${parseFloat(item.purchase_amount).toFixed(1)} $</td>
                                <td class="${item.current_value >= item.purchase_amount ? 'bg-success text-light' : 'bg-danger text-light fw-bold'}">${parseFloat(item.current_value).toFixed(1)} $</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-id="${item.id}" data-bs-target="#editModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="/price-symbols/destroy/${item.id}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="${item.id}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                        $pricesTableBody.append(row);
                    }
                });
            }
        });
    </script>
    @endsection

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h3>إدارة العملات الرقمية</h3>
                    <div class="d-flex">
                        <!-- زر إضافة عملة جديدة -->
                        <a href="#" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fas fa-plus-circle"></i> إضافة عملة جديدة
                        </a>
                        <!-- زر التحديث -->
                        <button type="button" class="btn btn-secondary" id="refreshButton">
                            <i class="fas fa-sync"></i> تحديث
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-center">
                        <thead>
                            <tr>
                                <th scope="col"><i class="fas fa-hashtag"></i></th>
                                <th scope="col"><i class="fas fa-coins"></i> اسم العملة</th>
                                <th scope="col"><i class="fas fa-dollar-sign"></i> السعر الحالي</th>
                                <th scope="col"><i class="fas fa-chart-line"></i> متوسط سعر الشراء</th>
                                <th scope="col"><i class="fas fa-percentage"></i> نسبة التغير</th>
                                <th scope="col"><i class="fas fa-boxes"></i> الكمية التي تم شراؤها</th>
                                <th scope="col"><i class="fas fa-wallet"></i> مبلغ الشراء</th>
                                <th scope="col"><i class="fas fa-money-bill-wave"></i> قيمة المبلغ الآن</th>
                                <th scope="col"><i class="fas fa-cogs"></i> إجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="pricesTableBody">
                            @foreach ($pricesSymbols as $Price_Symbol)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $Price_Symbol->currency_name }}USDT</td>
                                    <td class="bg-info text-dark fw-bold">{{ number_format($Price_Symbol->current_price, 3) }} $</td>
                                    <td>{{ number_format($Price_Symbol->average_buy_price, 3) }} $</td>
                                    <td class="{{ $Price_Symbol->percentage_change >= 0 ? 'bg-success text-light' : 'bg-danger text-light' }}">{{ number_format($Price_Symbol->percentage_change, 2) }}%</td>
                                    <td>{{ number_format($Price_Symbol->quantity, 2) }}</td>
                                    <td>{{ number_format($Price_Symbol->purchase_amount, 1) }} $</td>
                                    <td class="{{ $Price_Symbol->current_value >= $Price_Symbol->purchase_amount ? 'bg-success text-light' : 'bg-danger text-light fw-bold' }}">{{ number_format($Price_Symbol->current_value, 1) }} $</td>
                                    <td>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-id="{{ $Price_Symbol->id }}" data-bs-target="#editModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('price-symbols.destroy', $Price_Symbol->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $Price_Symbol->id }}">
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

        <!-- Modal for Adding New Currency -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createForm" method="POST" action="{{ route('price-symbols.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="currency_name" class="form-label">اسم العملة</label>
                                <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                                <span class="text-danger" id="currency_name_error"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                                <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                                <span class="text-danger" id="average_buy_price_error"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                                <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                                <span class="text-danger" id="quantity_error"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                                <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount">
                                <span class="text-danger" id="purchase_amount_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Modal for Editing Currency -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">تعديل العملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="edit_id" name="id">
                            <div class="col-md-4 mb-3">
                                <label for="edit_currency_name" class="form-label">اسم العملة</label>
                                <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                                <span class="text-danger" id="edit_currency_name_error"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                                <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                                <span class="text-danger" id="edit_average_buy_price_error"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                                <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                                <span class="text-danger" id="edit_quantity_error"></span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                                <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount">
                                <span class="text-danger" id="edit_purchase_amount_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Modal for Deleting Currency -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        هل أنت متأكد أنك تريد حذف هذه العملة؟
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
