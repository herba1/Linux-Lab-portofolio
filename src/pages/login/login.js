const buttonLogin = document.querySelector('.auth__option--login');
const buttonSignup= document.querySelector('.auth__option--signup');
const loginSection = document.querySelector('.auth__form--login')
const signupSection= document.querySelector('.auth__form--signup')
const signupForm = document.querySelector('#signup__form');
const loginForm = document.querySelector('#login__form');


buttonLogin.addEventListener('click',()=>{
    buttonLogin.classList.add('active')
    buttonSignup.classList.remove('active');
    showLogin();
})
buttonSignup.addEventListener('click',()=>{
    buttonSignup.classList.add('active')
    buttonLogin.classList.remove('active');
    showSignup();
})

function showLogin(){
    signupSection.classList.add('hidden');
    loginSection.classList.remove('hidden');
}

function showSignup(){
    loginSection.classList.add('hidden');
    signupSection.classList.remove('hidden');
}

// No authentication check needed for static site
// Adding event listeners for buttons
document.addEventListener('DOMContentLoaded', () => {
    const loginButton = document.querySelector('.auth__form--login .styled-button');
    const signupButton = document.querySelector('.auth__form--signup .styled-button');
    const resetButton = document.querySelector('.auth__forgot');
    
    loginButton.addEventListener('click', () => {
        window.location.href = '../dashboard/dashboard.html';
    });
    
    signupButton.addEventListener('click', () => {
        window.location.href = '../dashboard/dashboard.html';
    });
    
    resetButton.addEventListener('click', () => {
        alert('Password reset is not available in this static demo.');
    });
});