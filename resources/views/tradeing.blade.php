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
                {{-- <x-welcome /> --}}

                <div class="table-responsive" >
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Entry Price</th>
                                <th scope="col">TP1</th>
                                <th scope="col">TP2</th>
                                <th scope="col">TP3</th>
                                <th scope="col">TP4</th>
                                <th scope="col">TP5</th>
                                <th scope="col">SL</th>
                                <th scope="col">Status</th>
                                <th scope="col">SGY Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Currencie as $currency)
                            <tr>
                                <td scope="row">{{ $loop->iteration }}</td>
                                <td scope="row">{{ $currency->name }}</td>
                                <td scope="row">{{ $currency->entry_price }}</td>
                                <td scope="row">{{ $currency->tp1 }}</td>
                                <td scope="row">{{ $currency->tp2 }}</td>
                                <td scope="row">{{ $currency->tp3 }}</td>
                                <td scope="row">{{ $currency->tp4 }}</td>
                                <td scope="row">{{ $currency->tp5 }}</td>
                                <td scope="row">{{ $currency->sl }}</td>
                                <td scope="row">{{ ucfirst($currency->status) }}</td> <!-- ucfirst يجعل أول حرف كبير (capital) -->
                                <td scope="row">{{ $currency->sgy_type }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
