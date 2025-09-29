@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Цветовая схема для {{ $isa->individual_style_of_activity }}</h1>
    
    @forelse($rectangles as $wallId => $wallRectangles)
        <div class="wall-section mb-5">
            <h3>{{ $wallNames[$wallId] ?? 'Неизвестная поверхность' }}</h3>
            
            <div class="color-grid">
                @foreach($wallRectangles->sortBy([['z', 'asc'], ['y', 'asc']]) as $rect)
                    <div class="color-item" 
                         style="background-color: {{ $rect->color }};
                                width: 30px; /* Фиксированный размер */
                                height: 30px;">
                         <div class="coordinates">
                             {{ $rect->y }},{{ $rect->z }}
                         </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="alert alert-warning">
            Нет данных для отображения
        </div>
    @endforelse
</div>
@endsection

@section('styles')
<style>
.color-grid {
    display: grid;
    grid-template-columns: repeat(12, 30px); /* Явное задание размера */
    gap: 2px;
    margin-bottom: 20px;
}

.color-item {
    position: relative;
    border: 1px solid rgba(0,0,0,0.1);
    transition: all 0.2s;
    overflow: hidden;
}

.coordinates {
    position: absolute;
    bottom: 2px;
    right: 2px;
    font-size: 6px;
    background: rgba(255,255,255,0.8);
    padding: 1px 3px;
    border-radius: 2px;
}
</style>
@endsection

@section('styles')
<style>
.color-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr); /* Для z от 1 до 12 */
    gap: 2px;
    margin-bottom: 20px;
}

.color-item {
    aspect-ratio: 1;
    min-width: 30px;
    border: 1px solid rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.color-item:hover {
    transform: scale(1.2);
    z-index: 2;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.wall-section {
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация тултипов
    $('[data-toggle="tooltip"]').tooltip();
    
    // Анимация при наведении
    document.querySelectorAll('.color-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.zIndex = 10;
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.zIndex = 1;
        });
    });
});
</script>
@endsection