<?php

return [
    // 'show_warnings' => true, // Включаем отображение ошибок

    'options' => [
        'temp_dir' => storage_path('app/temp'), // Явно указываем temp
        'enable_remote' => true,
        'enable_html5_parser' => true,
        'default_font' => 'DejaVu Sans',
    ],
];
