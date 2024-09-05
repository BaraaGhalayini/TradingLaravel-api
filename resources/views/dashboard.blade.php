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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h3>إدارة العملات الرقمية</h3>
                    {{-- <a href="{{ route('price-symbols.create') }}" class="btn btn-primary">إضافة عملة جديدة</a> --}}
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">إضافة عملة جديدة</a>

                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">اسم العملة</th>
                                <th scope="col">السعر الحالي</th>
                                <th scope="col">متوسط سعر الشراء</th>
                                <th scope="col">نسبة التغير</th>
                                <th scope="col">الكمية التي تم شراؤها</th>
                                <th scope="col">مبلغ الشراء</th>
                                <th scope="col">قيمة المبلغ الآن</th>
                                <th scope="col">إجراءات</th> <!-- العمود لإظهار أزرار التعديل والحذف -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Prices_Symbols as $Price_Symbol)
                            <tr>
                                <td scope="row">{{ $loop->iteration }}</td>
                                <td scope="row">{{ $Price_Symbol->currency_name }}</td>
                                <td scope="row">{{ $Price_Symbol->current_price }}</td>
                                <td scope="row">{{ $Price_Symbol->average_buy_price }}</td>
                                <td scope="row">{{ $Price_Symbol->percentage_change }}%</td>
                                <td scope="row">{{ $Price_Symbol->quantity }}</td>
                                <td scope="row">{{ $Price_Symbol->purchase_amount }}</td>
                                <td scope="row">{{ $Price_Symbol->current_value }}</td>
                                <td scope="row">
                                    <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{ $Price_Symbol->id }}"
                                    data-name="{{ $Price_Symbol->currency_name }}"
                                    data-current-price="{{ $Price_Symbol->current_price }}"
                                    data-average-buy-price="{{ $Price_Symbol->average_buy_price }}"
                                    data-percentage-change="{{ $Price_Symbol->percentage_change }}"
                                    data-quantity="{{ $Price_Symbol->quantity }}"
                                    data-purchase-amount="{{ $Price_Symbol->purchase_amount }}"
                                    data-current-value="{{ $Price_Symbol->current_value }}">تعديل</button>


                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $Price_Symbol->id }}">حذف</button>

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
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
</div>
<!-- Modal for Adding New Currency -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">إضافة عملة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('price-symbols.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency_name" class="form-label">اسم العملة</label>
                            <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_price" class="form-label">السعر الحالي</label>
                            <input type="number" step="0.00000001" class="form-control" id="current_price" name="current_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="average_buy_price" class="form-label">متوسط سعر الشراء</label>
                            <input type="number" step="0.00000001" class="form-control" id="average_buy_price" name="average_buy_price" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="percentage_change" class="form-label">نسبة التغير</label>
                            <input type="number" step="0.01" class="form-control" id="percentage_change" name="percentage_change" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">الكمية التي تم شراؤها</label>
                            <input type="number" step="0.00000001" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="purchase_amount" class="form-label">مبلغ الشراء</label>
                            <input type="number" step="0.01" class="form-control" id="purchase_amount" name="purchase_amount" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="current_value" class="form-label">قيمة المبلغ الآن</label>
                            <input type="number" step="0.01" class="form-control" id="current_value" name="current_value" required>
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
                    <h5 class="modal-title" id="editModalLabel">تعديل عملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('price-symbols.update', 'placeholder') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_currency_name" class="form-label">اسم العملة</label>
                                <input type="text" class="form-control" id="edit_currency_name" name="currency_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_current_price" class="form-label">السعر الحالي</label>
                                <input type="number" step="0.00000001" class="form-control" id="edit_current_price" name="current_price" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_average_buy_price" class="form-label">متوسط سعر الشراء</label>
                                <input type="number" step="0.00000001" class="form-control" id="edit_average_buy_price" name="average_buy_price" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_percentage_change" class="form-label">نسبة التغير</label>
                                <input type="number" step="0.01" class="form-control" id="edit_percentage_change" name="percentage_change" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_quantity" class="form-label">الكمية التي تم شراؤها</label>
                                <input type="number" step="0.00000001" class="form-control" id="edit_quantity" name="quantity" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_purchase_amount" class="form-label">مبلغ الشراء</label>
                                <input type="number" step="0.01" class="form-control" id="edit_purchase_amount" name="purchase_amount" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_current_value" class="form-label">قيمة المبلغ الآن</label>
                                <input type="number" step="0.01" class="form-control" id="edit_current_value" name="current_value" required>
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
    </div>

    <!-- Modal for Deleting Currency -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('price-symbols.destroy', 'placeholder') }}" method="POST">
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
    </div>

    <script>
        // Handle edit button click
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = document.getElementById('editModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var name = button.getAttribute('data-name');
                var currentPrice = button.getAttribute('data-current-price');
                var averageBuyPrice = button.getAttribute('data-average-buy-price');
                var percentageChange = button.getAttribute('data-percentage-change');
                var quantity = button.getAttribute('data-quantity');
                var purchaseAmount = button.getAttribute('data-purchase-amount');
                var currentValue = button.getAttribute('data-current-value');

                var form = editModal.querySelector('form');
                form.action = form.action.replace('placeholder', id);
                editModal.querySelector('#edit_currency_name').value = name;
                editModal.querySelector('#edit_current_price').value = currentPrice;
                editModal.querySelector('#edit_average_buy_price').value = averageBuyPrice;
                editModal.querySelector('#edit_percentage_change').value = percentageChange;
                editModal.querySelector('#edit_quantity').value = quantity;
                editModal.querySelector('#edit_purchase_amount').value = purchaseAmount;
                editModal.querySelector('#edit_current_value').value = currentValue;
            });

            // Handle delete button click
            var deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');

                var form = deleteModal.querySelector('form');
                form.action = form.action.replace('placeholder', id);
            });
        });
    </script>

</x-app-layout>
