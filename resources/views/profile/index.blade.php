@extends('layouts.app')

@section('content')
    @php
        use App\Models\PackagePurchase;
        use App\Services\AnalysisService;
        $analysisService = new AnalysisService();
        $hasPaidPackage = PackagePurchase::where('user_id', Auth::id())->where('payment_status', 'paid')->exists();
        if ($hasPaidPackage) {
            $analysis = $analysisService->getFullAnalysis(Auth::user());
        }
    @endphp
    <h2>Профиль пользователя</h2>

    <article class="text-center">
        <p><strong>Логин:</strong> {{ $user->login }}</p>
        @if ($user->email)
            <p><strong>Email:</strong> {{ $user->email }}</p>
        @else
            <p><strong>Email:</strong> не указан</p>
        @endif
        <p><strong>Тип учётной записи:</strong> {{ $user->user_type == 'applicant' ? 'абитуриент' : 'студент' }}</p>
        @if ($user->user_type == 'student' && $user->educationProgram)
            <p><strong>Направление подготовки:</strong>
                {{ $user->educationProgram->code . ' ' . $user->educationProgram->name }}</p>
        @endif
        <button onclick="openModal(editProfileModal)">Редактировать</button>
    </article>
    @include('components.packages')
    @if ($hasPaidPackage)
        <div>
            <a href="{{ route('profile.download-pdf') }}" class="button">
                Скачать отчет PDF
            </a>
<br>
            <a href="{{ route('profile.download-doc') }}" class="button outline">
                Скачать отчет DOC
            </a>
        </div>
    @endif
    {{-- <h3>Результаты тестирований</h3>

    <article>

        @if ($results->count() > 0)
            <ul>
                @foreach ($results as $result)
                    <li>
                        <a href="{{ route('result', $result->id) }}">
                            <p>{{ date('d.m.Y H:i', strtotime($result->created_at)) }} -
                                {{ $result->test ? $result->test->name : '' }} </p>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p>У вас пока нет результатов тестирований.</p>
        @endif
    </article> --}}
    <br>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width: 100%;">
            Выйти
        </button>
    </form>
    <dialog id="editProfileModal">
        <article>
            <header>
                <button aria-label="Close" rel="prev" onclick="closeModal(editProfileModal)"></button>
                <h3>Редактирование профиля</h3>
            </header>

            <form id="editProfileForm" action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div>
                    <label for="edit_login"><strong>Логин</strong></label>
                    <input type="text" id="edit_login" name="login" value="{{ $user->login }}" required>
                </div>

                <div>
                    <label for="edit_email"><strong>Email</strong></label>
                    <input type="email" id="edit_email" name="email" value="{{ $user->email }}">
                </div>

                <div>
                    <label><strong>Тип учётной записи</strong></label>
                    <div>
                        <label>
                            <input type="radio" name="user_type" value="applicant" id="edit_applicant_radio"
                                {{ $user->user_type == 'applicant' ? 'checked' : '' }}> Абитуриент
                        </label>
                        <label>
                            <input type="radio" name="user_type" value="student" id="edit_student_radio"
                                {{ $user->user_type == 'student' ? 'checked' : '' }}> Студент
                        </label>
                    </div>
                </div>

                <div id="edit_program-field">
                    <label for="edit_education_program"><strong>Направление подготовки</strong></label>
                    <select id="edit_education_program" name="education_program">
                        <option value="">Выберите направление</option>
                        @foreach ($educationPrograms as $program)
                            <option value="{{ $program->id }}"
                                {{ $user->education_program_id == $program->id ? 'selected' : '' }}>
                                {{ $program->code }} {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <footer>
                    <button type="button" class="secondary" onclick="closeModal(editProfileModal)">Отмена</button>
                    <button type="submit">
                        Сохранить изменения
                    </button>
                </footer>
            </form>

        </article>
    </dialog>
@endsection
