

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


</x-app-layout>
