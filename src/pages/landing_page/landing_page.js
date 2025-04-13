import { Navigation } from "../../components/Navigation/index.js";
import { Footer } from "../../components/Footer/index.js";

// Initialize navigation and footer
const navigation = new Navigation('.navigation__container');
const footer = new Footer('.footer__container');

// Mock user data for presentation
const mockUserData = {
    isLoggedIn: false,
    username: 'guest'
};

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    // Allow button to work without authentication
    const getStartedButton = document.querySelector('.hero__button');
    if (getStartedButton) {
        getStartedButton.addEventListener('click', () => {
            window.location.href = '../login/login.html';
        });
    }
});