// ==================== THEME MANAGER ====================
const ThemeManager = {
    currentTheme: null,
    toggler: null,

    init() {
        this.toggler = document.getElementById('themeToggler');
        this.currentTheme = this.getStoredTheme();
        this.applyTheme(this.currentTheme);
        this.setupEventListeners();
    },

    getStoredTheme() {
        const storedTheme = localStorage.getItem('theme');
        return storedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    },

    applyTheme(theme) {
        document.documentElement.dataset.theme = theme;
        this.updateThemeIcon(theme);
    },

    updateThemeIcon(theme) {
        if (!this.toggler) return;

        this.toggler.innerHTML = theme === 'dark'
            ? this.getMoonIcon()
            : this.getSunIcon();
    },

    getMoonIcon() {
        return `<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#EFEFEF">
                <path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q8 0 17 .5t23 1.5q-36 32-56 79t-20 99q0 90 63 153t153 63q52 0 99-18.5t79-51.5q1 12 1.5 19.5t.5 14.5q0 150-105 255T480-120Zm0-60q109 0 190-67.5T771-406q-25 11-53.67 16.5Q688.67-384 660-384q-114.69 0-195.34-80.66Q384-545.31 384-660q0-24 5-51.5t18-62.5q-98 27-162.5 109.5T180-480q0 125 87.5 212.5T480-180Zm-4-297Z"/>
            </svg>`;
    },

    getSunIcon() {
        return `<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#EFEFEF">
                <path d="M480-340q58 0 99-41t41-99q0-58-41-99t-99-41q-58 0-99 41t-41 99q0 58 41 99t99 41Zm0 60q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-450H40v-60h160v60Zm720 0H760v-60h160v60ZM450-760v-160h60v160h-60Zm0 720v-160h60v160h-60ZM262-658l-100-97 43-44 96 100-39 41Zm494 496-98-100 41-41 99 98-42 43Zm-99-537 98-99 44 42-99 98-43-41ZM162-205l99-98 42 42-98 99-43-43Zm318-275Z"/>
            </svg>`;
    },

    toggleTheme() {
        const newTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
        this.currentTheme = newTheme;
        this.applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    },

    setupEventListeners() {
        if (this.toggler) {
            this.toggler.addEventListener('click', () => this.toggleTheme());
        }
    }
};

// ==================== FORM MANAGER ====================
const FormManager = {
    init() {
        this.setupRegistrationForm();
        this.setupProfileEditForm();
    },

    setupRegistrationForm() {
        const studentRadio = document.getElementById('student-radio');
        const applicantRadio = document.getElementById('applicant-radio');
        const programField = document.getElementById('program-field');

        if (studentRadio && applicantRadio && programField) {
            // Исправленная версия - передаем функцию, а не результат вызова
            studentRadio.addEventListener('change', () => this.toggleProgramVisibility(studentRadio, programField));
            applicantRadio.addEventListener('change', () => this.toggleProgramVisibility(studentRadio, programField));

            // Инициализация при загрузке
            this.toggleProgramVisibility(studentRadio, programField);
        }
    },

    setupProfileEditForm() {
        const editStudentRadio = document.getElementById('edit_student_radio');
        const editApplicantRadio = document.getElementById('edit_applicant_radio');
        const editProgramField = document.getElementById('edit_program-field');

        if (editStudentRadio && editApplicantRadio && editProgramField) {
            editStudentRadio.addEventListener('change', () => this.toggleProgramVisibility(editStudentRadio, editProgramField));
            editApplicantRadio.addEventListener('change', () => this.toggleProgramVisibility(editStudentRadio, editProgramField));

            // Инициализация при загрузке
            this.toggleProgramVisibility(editStudentRadio, editProgramField);
        }
    },

    toggleProgramVisibility(studentRadio, programField) {
        programField.style.display = studentRadio.checked ? 'block' : 'none';
    }
};

// ==================== MODAL MANAGER ====================
const ModalManager = {
    showAlert(message, title = 'Уведомление') {
        const modal = document.getElementById('alertModal');
        document.getElementById('alertTitle').textContent = title;
        document.getElementById('alertMessage').textContent = message;
        openModal(modal);
    },

    createQuestion(message, rejectText, acceptText, rejectFunction, acceptFunction) {

        const confirmModal = document.createElement('dialog');
        confirmModal.innerHTML = `
            <article>
                <header>
                    <button aria-label="Close" rel="prev" class="close-modal-btn"></button>
                    <p>Внимание</p>
                </header>
                <p>${message}</p>
                <footer>
                    <button class="secondary reject-modal-btn">${rejectText}</button>
                    <button class="accept-modal-btn">${acceptText}</button>

                </footer>
            </article>
        `;

        document.body.appendChild(confirmModal);

        const closeBtn = confirmModal.querySelector('.close-modal-btn');
        const rejectBtn = confirmModal.querySelector('.reject-modal-btn');
        const acceptBtn = confirmModal.querySelector('.accept-modal-btn');

        const closeHandler = () => {
            closeModal(confirmModal);
        };

        const rejectHandler = () => {
            closeModal(confirmModal);
            if (typeof rejectFunction === 'function') {
                rejectFunction();
            }
        };

        const acceptHandler = () => {
            closeModal(confirmModal);
            if (typeof acceptFunction === 'function') {
                acceptFunction();
            }
        };

        closeBtn.addEventListener('click', closeHandler);
        rejectBtn.addEventListener('click', rejectHandler);
        acceptBtn.addEventListener('click', acceptHandler);
        openModal(confirmModal);


    }

};

// ==================== TEST FORM MANAGER ====================
const TestFormManager = {
    init() {
        this.setupClearButtons();
        this.setupTestForms();
    },

    setupClearButtons() {
        document.querySelectorAll('[id="clearForm"]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const form = e.target.closest('form');
                if (!form) return;

                const clearAnswers = () => {
                    form.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(radio => {
                        radio.checked = false;
                    });
                };

                if (typeof ModalManager !== 'undefined') {
                    ModalManager.createQuestion(
                        'Вы уверены, что хотите очистить все ответы?',
                        'Нет',
                        'Да',
                        null,
                        clearAnswers
                    );
                } else {
                    if (confirm('Вы уверены, что хотите очистить все ответы?')) {
                        clearAnswers();
                    }
                }
            });
        });
    },

    setupTestForms() {
        document.querySelectorAll('form[id$="TestForm"]').forEach(form => {
            form.addEventListener('submit', (e) => this.handleTestSubmit(e));
        });
    },

    handleTestSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const hasCheckboxes = form.querySelectorAll('input[type="checkbox"]').length > 0;

        if (hasCheckboxes) {
            const allCheckboxes = form.querySelectorAll('input[type="checkbox"]');
            const checkedCheckboxes = form.querySelectorAll('input[type="checkbox"]:checked');

            const questionNames = new Set();
            allCheckboxes.forEach(cb => {
                // Из "object[1.1]" получаем "object"
                const questionName = cb.name.split('[')[0];
                questionNames.add(questionName);
            });

            let allAnswered = true;
            questionNames.forEach(questionName => {
                const checkedInQuestion = Array.from(checkedCheckboxes).filter(
                    cb => cb.name.startsWith(questionName)
                );
                if (checkedInQuestion.length === 0) {
                    allAnswered = false;
                }
            });

            if (!allAnswered) {
                ModalManager.showAlert('Пожалуйста, ответьте на все вопросы', 'Внимание');
                return;
            }

            const answers = {};
            questionNames.forEach(questionName => {
                const checked = Array.from(checkedCheckboxes).filter(
                    cb => cb.name.startsWith(questionName)
                );
                answers[questionName] = checked.map(cb => cb.value);
            });

            this.submitForm(form, answers);

        } else {
            const selectedRadios = form.querySelectorAll('input[type="radio"]:checked');

            const allRadios = form.querySelectorAll('input[type="radio"]');
            const totalQuestions = new Set([...allRadios].map(radio => radio.name)).size;

            if (selectedRadios.length !== totalQuestions) {
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.showAlert('Пожалуйста, ответьте на все вопросы', 'Внимание');
                } else {
                    alert('Пожалуйста, ответьте на все вопросы');
                }
                return;
            }

            const answers = {};
            selectedRadios.forEach(radio => {
                answers[radio.name] = radio.value;
            });

            console.log('Answers:', answers);
            this.submitForm(form, answers);
        }
    },

    submitForm(form, answers) {
        const saveBtn = form.querySelector('[id="saveResult"]');
        if (!saveBtn) return;

        const route = saveBtn.dataset.route;
        const redirect = saveBtn.dataset.redirect;
        const loading = document.getElementById('loading');

        if (loading) loading.style.display = 'flex';
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ answers: answers })
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || `Ошибка: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.createQuestion(
                        'Результат успешно сохранен! Ознакомиться с ним можно в профиле'+
                        ((data.notification && !data.notification.success) ? '\n\n\n\n' + data.notification
                            .message : ''),
                        'Остаться',
                        'Перейти',
                        null,
                        () => { window.location.href = redirect; }
                    );
                } else {
                    alert('Результат успешно сохранен!');
                    window.location.href = redirect;
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.showAlert('Ошибка сохранения результата', 'Ошибка');
                } else {
                    alert('Ошибка сохранения результата');
                }
            })
            .finally(() => {
                if (loading) loading.style.display = 'none';
            });
    }
};

const PhoneMaskManager = {
    init() {
        document.querySelectorAll('.phone').forEach(input => {
            input.addEventListener('input', this.maskPhone);
        });
    },

    maskPhone(e) {
        let digits = e.target.value.replace(/\D/g, '');

        // нормализация начала номера
        if (digits.startsWith('8')) digits = '7' + digits.slice(1);
        if (!digits.startsWith('7')) digits = '7' + digits;

        digits = digits.slice(0, 11);

        let result = '+7';

        if (digits.length > 1) {
            result += ' (' + digits.slice(1, 4);
        }
        if (digits.length >= 5) {
            result += ') ' + digits.slice(4, 7);
        }
        if (digits.length >= 8) {
            result += '-' + digits.slice(7, 9);
        }
        if (digits.length >= 10) {
            result += '-' + digits.slice(9, 11);
        }

        e.target.value = result;
    }
};


document.addEventListener('DOMContentLoaded', function () {

    // ==================== INITIALIZATION ====================
    ThemeManager.init();
    FormManager.init();
    TestFormManager.init();
    PhoneMaskManager.init();
    // ModalManager.init();

    console.log('All modules initialized successfully');


});
