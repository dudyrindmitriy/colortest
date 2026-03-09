@extends('layouts.app')

@section('content')
    <h1>
        Управление покупками</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $packageNames = [
            'basic' => 'Базовый',
            'standard' => 'Стандарт',
            'pro' => 'Расширенный',
        ];
    @endphp
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Пакет</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->id }}</td>
                    <td>{{ $purchase->user->login }} ({{ $purchase->user->email }})</td>
                    <td>{{ $packageNames[$purchase->package->code] }}</td>
                    <td>
                        @if ($purchase->amount == 0)
                            <span style="color: #27ae60;">Бесплатно</span>
                        @elseif($purchase->amount < $purchase->package->price)
                            <span
                                style="text-decoration: line-through; color: #999;">{{ number_format($purchase->package->price, 0, ',', ' ') }}
                                ₽</span>
                            <span
                                style="color: #e67e22; font-weight: bold;">{{ number_format($purchase->amount, 0, ',', ' ') }}
                                ₽</span>
                        @else
                            {{ number_format($purchase->amount, 0, ',', ' ') }} ₽
                        @endif
                    </td>
                    <td>
                        @if ($purchase->payment_status == 'paid')
                            <span style="color: green;">Оплачено</span>
                        @elseif($purchase->payment_status == 'pending')
                            <span style="color: orange;">Ожидает</span>
                        @else
                            <span style="color: red;">Отменено</span>
                        @endif
                    </td>
                    <td>{{ $purchase->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if ($purchase->payment_status == 'pending')
                            <form action="{{ route('admin.purchases.verify', $purchase) }}" method="POST">
                                @csrf
                                <button type="submit">Подтвердить оплату</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
