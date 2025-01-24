const tunnelContainer = document.querySelector('.tunnel-container');
const numSections = 40; // Количество секций туннеля
const sectionDepth = 20;  // "Глубина" каждой секции

for (let i = 0; i < numSections; i++) {
    // Создаем секции
    createSection('top', i);
    createSection('bottom', i);
    createSection('left', i);
    createSection('right', i);
}

function createSection(side, index) {
    const section = document.createElement('div');
    section.classList.add('section', side);

    let scale = 1 - (index / numSections) * 0.5;
    let z = index * -sectionDepth;
    let rotation = 0;

    switch(side){
        case 'top':
            rotation = -90
            break;
        case 'bottom':
            rotation = 90;
            break;
        case 'left':
            rotation = 90;
            break;
        case 'right':
            rotation = -90;
            break;
    }
    
    section.style.transform = `rotateX(${side === 'top' || side === 'bottom' ? rotation : 0}deg) rotateY(${side === 'left' || side === 'right' ? rotation : 0}deg) translateZ(${z}px) scale(${scale})`;
    tunnelContainer.appendChild(section);
}