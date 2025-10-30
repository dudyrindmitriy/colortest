@extends('layouts.auth')

@section('content')
    <div class="main-content">
        <h1>Регистрация</h1>
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="name">Логин</label>
            <input type="text" id="name" name="login">
            {{-- <label for="name">Email</label>

            <input type="email" id="email" name="email"> --}}
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password">
            <fieldset>
                <legend>Выберите тип учетной записи</legend>
                <label>
                    <input id="applicant-radio" type="radio" name="user_type" value="applicant" checked>Абитуриент
                </label>
                <label>
                    <input id="student-radio" type="radio" name="user_type" value="student">Студент
                </label>
            </fieldset>
            <div id="program-field" style="display: none">
                <label for="education_program">Направление подготовки</label>
                <select name="education_program" id="education_program">

                    @foreach ($educationPrograms as $program)
                    <option value="{{$program['id']}}">{{$program['code'].' '.$program['name']}}</option>

                    @endforeach
                </select>
            </div>
              <input type="submit" value="Зарегистрироваться">
        </form>
        <a href="{{ route('login') }}" role="button" class="secondary outline">Войти</a>
    </div>
@endsection
