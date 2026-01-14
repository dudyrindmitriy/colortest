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
                overflow-x: auto
            }

            .table {
                border-collapse: collapse;
                width: 100%;
            }

            /*
                        .table th,
                        .table td {
                            border: 1px solid #000;
                            padding: 8px;
                            text-align: left;
                        } */
        </style>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Направление</th>
                        <th>Изображение</th>
                        <th>Результат</th>

                        {{-- <th>ISA</th>
                <th>CHESS</th>
                <th>Рекомендации</th> --}}
                        {{-- <th>Редактирование</th> --}}
                        <th>Удаление</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $result)
                        <tr>
                            <td>{{ $result->id }}</td>
                            <td>{{ $result->user->login }}</td>
                            <td>{{ $result->user->user_type == 'student' ? $result->user->educationProgram->code . ' ' . $result->user->educationProgram->name : 'Нет' }}
                            </td>
                            <td>{!! $result->user_image !!}</td>

                            <td>
                                @if ($result->ml_predictions && count($result->ml_predictions) > 0)
                                    @php
                                        $predictions = collect($result->ml_predictions)->sortBy('rank')->take(5);

                                        // Получаем коды программ
                                        $codes = $predictions->pluck('class')->toArray();
                                        $programs = App\Models\EducationProgram::whereIn('code', $codes)
                                            ->get()
                                            ->keyBy('code');
                                    @endphp

                                    @foreach ($predictions as $prediction)
                                        @php $program = $programs[$prediction['class']] ?? null; @endphp
                                            <p>{{ $program ? $program->code . ' ' . $program->name : $prediction['class'] }} -
                                           {{ round($prediction['probability'], 1) }}%</p>
                                    @endforeach
                                @else
                                    <span class="text-muted">Не анализировано</span>
                                @endif
                            </td>
                            {{-- <td>{{ $result->isa->individual_style_of_activity }}</td> --}}
                            {{-- <td>{{ $result->chess_structure }}</td> --}}
                            {{-- <td>{!! $result->recommendation !!}</td> --}}

                            {{-- <td>
                    <a href="{{ route('admin.results.edit', $result) }}" class="nav-button">Редактировать</a>
                </td> --}}
                            <td>
                                <form action="{{ route('admin.results.destroy', $result) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button_red">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
