const faculties = document.querySelectorAll('[data-faculty]');
const results = new Map();
faculties.forEach(function (faculty) {
    faculty.querySelectorAll('tbody tr').forEach(function (programm) {
        const codeSelector = programm.querySelector('.abiturs__position-group_left > div.abiturs-code-form > div.abiturs__position_left');
        const nameSelector = programm.querySelector('.abiturs__position-group_left > div.abiturs__position_left');
        if (codeSelector && nameSelector) {
            const code = codeSelector.textContent.trim();
            const name = nameSelector.textContent.trim();
            const facultyName = faculty.dataset.faculty;
            if (!results.has(code)) {
                results.set(code, {
                    name: name,
                    faculty: facultyName,
                    code: code
                })
            }
        }
    })
})

const programms = Array.from(results.values());
let output = '$programs = [\n';
programms.forEach(item => {
    output += `    [\n`;
    output += `        'faculty' => '${item.faculty.replace(/'/g, "\\'")}',\n`;
    output += `        'code' => '${item.code.replace(/'/g, "\\'")}',\n`;
    output += `        'name' => '${item.name.replace(/'/g, "\\'")}',\n`;
    output += `    ],\n`;
});
output += '];';

// Создаем временный textarea для копирования
const textarea = document.createElement('textarea');
textarea.value = output;
document.body.appendChild(textarea);
textarea.select();
document.execCommand('copy');
document.body.removeChild(textarea);

