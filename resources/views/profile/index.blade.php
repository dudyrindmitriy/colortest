@extends('layouts.app')

@section('content')
        <h2>Профиль пользователя</h2>

<hr>
    <div>
        <p><strong>Логин:</strong> {{ $user->login }}</p>
        {{-- <p><strong>Email:</strong> {{ $user->email }}</p> --}}
        <p><strong>Тип учётной записи:</strong> {{ $user->user_type == 'applicant' ? 'абитуриент' : 'студент' }}</p>
        @if ($user->user_type == 'student' && $user->educationProgram)
            <p><strong>Направление подготовки:</strong>
                {{ $user->educationProgram->code . ' ' . $user->educationProgram->name }}</p>
        @endif

    </div>
    <hr>
    <h3>Результаты тестирований</h3>

    @if ($results->count() > 0)
        <ul>
            @foreach ($results as $result)
                <li>
                    <a href="{{ route('result', $result->id) }}">
                        <p>{{ date('d.m.Y H:i', strtotime($result->created_at)) }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>У вас пока нет результатов тестирований.</p>
    @endif
    <hr>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width: 100%;">
            Выйти
        </button>
    </form>
@endsection
