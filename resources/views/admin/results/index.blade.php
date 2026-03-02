@extends('layouts.app')

@section('content')
    <div class="main-content">
        <h2>Управление результатами</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <style>
            .table-container {
                width: 100%;
                overflow-x: auto;
            }
            .table {
                border-collapse: collapse;
                width: 100%;
            }

        </style>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Email</th>
                        <th>Направление</th>
                        <th>Тесты</th>
                        <th>Отчет</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results->groupBy('user_id') as $userId => $userResults)
                        @php
                            $user = $userResults->first()->user;
                            $hasTests = $userResults->count() > 0;
                        @endphp
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->login }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->user_type == 'student' && $user->educationProgram ? $user->educationProgram->code . ' ' . $user->educationProgram->name : 'Нет' }}</td>
                            <td>
                                @if($hasTests)
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach($userResults as $result)
                                            @php $test = App\Models\Test::find($result->test_id); @endphp
                                            <li>
                                                {{ $test ? $test->name : 'Тест #' . $result->test_id }}
                                                <small>({{ $result->created_at->format('d.m.Y') }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">Нет тестов</span>
                                @endif
                            </td>
                            <td>
                                @if($hasTests)
                                    <a href="{{ route('admin.results.download-pdf', $user->id) }}"

                                       target="_blank">
                                        Скачать PDF
                                    </a>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
