
@extends('layouts.app')

@section('content')
<div class="main-content">
    <h2>Редактировать результат</h2>
<form action="{{ route('admin.results.update', $result) }}" method="POST">
    @csrf
    @method('PUT')

   

    <div class="form-group">
        <label for="user_id">Пользователь</label>
        <select name="user_id" id="user_id" class="form-control">
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ $user->id == $result->user_id ? 'selected' : '' }}>
                    {{ $user->login }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="isa_id">ISA</label>
        <select name="isa_id" id="isa_id" class="form-control">
            @foreach($isas as $isa)
                <option value="{{ $isa->id }}" {{ $isa->id == $result->isa_id ? 'selected' : '' }}>
                    {{ $isa->individual_style_of_activity }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="chess_structure_id">Chess Structure</label>
        <select name="chess_structure_id" id="chess_structure_id" class="form-control">
            @foreach($chessStructures as $chessStructure)
                <option value="{{ $chessStructure->id }}" {{ $chessStructure->id == $result->chess_structure_id ? 'selected' : '' }}>
                    {{ $chessStructure->chess_structure }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
            <label for="recommendation">Рекомендация:</label>
            <textarea name="recommendation" id="recommendation" class="form-control" required>{{ old('recommendation', $result->recommendation) }}</textarea>
        </div>
    <button type="submit">Сохранить изменения</button>
</form>
</div>