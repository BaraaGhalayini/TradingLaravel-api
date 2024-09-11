

<x-app-layout>
    <x-slot name="header">
        <ul class="nav font-semibold text-xl text-gray-800" >
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('dashboard') }}" aria-current="page">  الصفحة الرئيسية </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tradeing') }}" aria-current="page"> الصفقات  </a>
            </li>

            <li class="nav-item">
                <a class="nav-link disabled" href="#"> اخرى</a>
            </li>
        </ul>
    </x-slot>

    @livewire('price-symbols-new')




    @yield('script')

    {{-- <script>
        $(document).ready(function() {
            function updatePrices() {
                $.ajax({
                    url: "{{ url('/api/prices') }}",
                    type: 'GET',
                    success: function(response) {
                        // تحديث البيانات في الصفحة
                        $.each(response, function(symbol, price) {
                            // تنسيق السعر إلى عدد معين من الخانات العشرية
                            var formattedPrice = parseFloat(price).toFixed(3);

                            $('tr').each(function() {
                                var rowSymbol = $(this).find('td.symbol').text().trim(); // تأكد من استخدام المعرف الصحيح
                                if (rowSymbol === symbol) {
                                    $(this).find('td.price').text(formattedPrice);
                                }
                            });
                        });

                        // تحديث الأسعار في قاعدة البيانات
                        $.ajax({
                            url: "{{ url('/api/update-prices') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: { prices: response },
                            success: function() {
                                console.log('Prices updated in the database');
                            },
                            error: function() {
                                console.log('Error updating prices in the database');
                            }
                        });
                    },
                    error: function() {
                        console.log('Error fetching prices');
                    }
                });
            }

            // تحديث الأسعار كل 30 ثانية (30000 مللي ثانية)
            setInterval(updatePrices, 10000);

            // تحديث الأسعار عند تحميل الصفحة لأول مرة
            updatePrices();
        });
    </script>

    <script>
        // Refresh button click handler
        $('#refreshButton').click(function() {
            updatePrices();
        });

        // Function to update prices via AJAX
        function updatePrices() {
            $.ajax({
                url: "{{ url('/api/update-prices') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}' // CSRF token for security
                },
                success: function(response) {
                    // Handle success response
                    toastr.success('تم تحديث الأسعار بنجاح');
                    // Optionally, you can update the UI with the new data
                },
                error: function(xhr) {
                    // Handle error response
                    toastr.error('حدث خطأ أثناء تحديث الأسعار');
                }
            });
        }
    </script> --}}


    {{-- <!-- Modal for Adding New Currency -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createForm" method="POST">
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
    </div> --}}

    {{-- <script>
        $(document).ready(function() {
            $('#createForm').on('submit', function(e) {
                e.preventDefault(); // منع إرسال النموذج بالطريقة التقليدية

                $.ajax({
                    url: "{{ route('price-symbols.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // تخزين رسالة الإشعار في localStorage
                        localStorage.setItem('toastrMessage', 'تم تحديث الأسعار بنجاح');

                        // إغلاق الموديل وإعادة تحميل البيانات
                        $('#createModal').modal('hide');
                        location.reload(); // إعادة تحميل الصفحة
                    },
                    error: function(xhr) {
                        // عرض الأخطاء داخل الموديل
                        let errors = xhr.responseJSON.errors;
                        $('.text-danger').text(''); // مسح الرسائل السابقة
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]); // عرض الرسالة المناسبة
                        });
                    }
                });
            });

            // عرض إشعار Toastr إذا كان هناك رسالة مخزنة
            const toastrMessage = localStorage.getItem('toastrMessage');
            if (toastrMessage) {
                toastr.success(toastrMessage);
                localStorage.removeItem('toastrMessage'); // إزالة الرسالة بعد عرضها
            }
        });
    </script> --}}
    <!-- End for Adding  New Currency -->


    {{-- <!-- Modal for Editing Currency -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="row">
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
                                <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                                <span class="text-danger" id="edit_purchase_amount_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    {{-- <script>
        $(document).ready(function() {
            $('#editModal').on('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var form = $(this).find('form');

                if (form.length) {
                    var action = form.attr('action');
                    if (action) {
                        form.attr('action', action.replace('placeholder', id));
                    } else {
                        console.error('Form action is not defined.');
                    }
                } else {
                    console.error('Form element is not found.');
                }

                $('#edit_currency_name').val(button.getAttribute('data-name'));
                $('#edit_current_price').val(button.getAttribute('data-current-price'));
                $('#edit_average_buy_price').val(button.getAttribute('data-average-buy-price'));
                $('#edit_percentage_change').val(button.getAttribute('data-percentage-change'));
                $('#edit_quantity').val(button.getAttribute('data-quantity'));
                $('#edit_purchase_amount').val(button.getAttribute('data-purchase-amount'));
                $('#edit_current_value').val(button.getAttribute('data-current-value'));
            });

            $('#editModal').find('form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        $('.text-danger').text('');
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').text(value[0]);
                        });
                    }
                });
            });
        });
    </script> --}}
    <!-- End for Editing Currency -->


    {{-- <!-- Modal for Deleting Currency -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        هل أنت متأكد أنك تريد حذف هذه العملة؟ هذه العملية لا يمكن التراجع عنها.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <!-- JavaScript لتحديث action الخاص بالنموذج داخل النافذة المنبثقة -->
    {{-- <script>
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = deleteModal.querySelector('form');
            form.action = `/price-symbols/${id}`;
        });
    </script> --}}

    <!-- End for Deleting Currency -->


{{-- @if (session('success'))
    <script>
        toastr.success('{{ session('success') }}');
    </script>
@endif --}}


{{-- <script>
    $(document).ready(function() {
        // عرض إشعار Toastr إذا كان هناك رسالة مخزنة
        const toastrMessage = localStorage.getItem('toastrMessage');
        if (toastrMessage) {
            toastr.success(toastrMessage);
            localStorage.removeItem('toastrMessage'); // إزالة الرسالة بعد عرضها
        }
    });
</script> --}}



</x-app-layout>
