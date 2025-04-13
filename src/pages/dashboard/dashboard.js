import { Navigation } from "../../components/Navigation/index.js";

// Mock user data for static site demo
const mockUserData = {
    username: 'demo_user',
    isLoggedIn: true,
    currentModule: {
        name: 'The Basics',
        lessonId: 3,
        lessonName: 'cat',
    },
    modules: [
        {
            name: 'The Basics',
            completed: 13,
            total: 21,
        },
        {
            name: 'Networking',
            completed: 18,
            total: 21,
        },
        {
            name: 'Advanced Topics',
            completed: 1,
            total: 17,
        }
    ]
};

// Initialize directly with mock data instead of fetching
document.addEventListener('DOMContentLoaded', () => {
    const nav = new Navigation('.navigation__container', true, mockUserData);
    const dashboard = new DashboardManager('.dashboard__container', mockUserData);
});

class DashboardManager {
    constructor(container = '.dashboard__container', data) {
        this.container = document.querySelector(container); 
        this.greeting = document.querySelector('.greeting__text');
        this.data = data;
        this.initialize();
    }

    render() {
        const cards = document.createElement('div');
        cards.classList.add('dashboard__cards');
        const col1 = document.createElement('div');
        const col2 = document.createElement('div');
        col1.classList.add('dashboard__column--1');
        col1.classList.add('dashboard__column');
        col2.classList.add('dashboard__column--2');
        col2.classList.add('dashboard__column');

        col1.innerHTML = this.getCol1Cards();
        col2.innerHTML = this.getCol2Cards();

        cards.appendChild(col1);
        cards.appendChild(col2);

        this.container.appendChild(cards);
        this.greeting.innerHTML = `Welcome, <span class="green">@</span>${this.data.username}`;
    }

    getCol1Cards() {
        const cardContinueLearning = new CardContinueLearning(this.data.currentModule);
        const cardModules = new CardModules(this.data.modules);
        return (cardContinueLearning.getCardTemplate() + cardModules.getCardTemplate());
    }

    getCol2Cards() {
        const cardProgress = new CardProgress(this.data.modules);
        const cardHTML = cardProgress.getCardTemplate();
        return cardHTML;
    }

    initialize() {
        this.render();
    }
}

class CardContinueLearning {
    constructor(data) {
        this.data = data;
    }

    getCardTemplate() {
        return(`
            <div class="dashboard__card dashboard__card--continue">
                <h3 class="card__title">Continue Learning:</h3>
                <div class="card__content">
                    <a href="../lesson_page/lesson.html" class="card__link">
                        <p class="card__text">${this.data.name}</p>
                        <p class="card__text">Lesson: ${this.data.lessonName}</p>
                    </a>
                    <button class="styled-button card__button"><a href="../lesson_page/lesson.html">continue</a></button>
                </div>
            </div>
        `);
    }
}

class CardModules {
    constructor(data) {
        this.data = data;
    }

    getElements(data) {
        const elements = data.map((section) => {
            return (`<li class="card__subtitle"><a href="#" class="card__link">${section.name}</a></li>`);
        });
        return elements;
    }

    getCardTemplate() {
        const elements = this.getElements(this.data);
        return(`
            <div class="dashboard__card dashboard__card--modules">
                <h3 class="no-select">Modules:</h3>
                <div class="card__content">
                    <ul class="card__ul">
                    ${elements.join('')}
                    </ul>
                    <p class="card__message no-select animate-pulse">Coming Soon...</p>
                </div>
            </div>
        `);
    }
}

class CardProgress {
    constructor(data) {
        this.data = data;
        this.getElements();
    }

    getElements() {
        let barSize = 20;
        const elements = this.data.map((section) => {
            return(`
                <p class="card__text">${section.name}</p>
                ${this.getProgressBar(section.completed,section.total)}
                <p class="card__text">${section.completed}/${section.total}</p>
            `);
        });
        return elements;
    }

    getProgressBar(completed, size) {
        let barSize = 20;
        const greenBar = Math.floor((completed/size)*20);
        const empty = barSize-greenBar;
        const blockChar = '█';
        const emptyChar = '░';
        return(`<span class="card__progress-bar">[<span class="green">${blockChar.repeat(greenBar)}</span>${emptyChar.repeat(empty)}]</span>`);
    }

    getCardTemplate() {
        const elements = this.getElements();
        return(`
            <div class="dashboard__card dashboard__card--progress">
                <h3>Your Progress:</h3>
                <div class="card__content">
                    <div class="card__content--progress">
                    ${elements.join('')}
                    </div>
                </div>
            </div>
        `);
    }
}