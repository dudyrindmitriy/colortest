<?php

return [
    'path' => env(
        'PYTHON_PATH', 
        DIRECTORY_SEPARATOR === '\\' 
            ? 'D:\\python\\python.exe'  
            : '/usr/bin/python3'          
    ),
    
    'encoding' => 'utf-8',
    'timeout' => 300,
];