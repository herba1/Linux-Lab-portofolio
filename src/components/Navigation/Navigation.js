import { formatDateLinuxStyle } from "../../utils/dateUtils.js";

export default class Navigation{
    LINKS = [
        {title:'Dashboard',link:'../../pages/dashboard/dashboard.html',svg:'../../pages/assets/SVGs/Control Panel.svg', tag:'dashboard.html'},
        {title:'Home',link:'../../pages/landing_page/landing_page.html',tag:'landing_page.html',svg:'../../pages/assets/SVGs/Home.svg'},
        {title:'Terminal',link:'../../pages/lesson_page/lesson.html',tag:'lesson.html',svg:'../../pages/assets/SVGs/Console.svg'},
        {title:'About',link:'../../pages/about_us/about_us.html',tag:'about_us.html',svg:'../../pages/assets/SVGs/Info Squared.svg'},
        {title:'Contact',link:'../../pages/contact_page/contact.html',tag:'contact.html',svg:'../../pages/assets/SVGs/Address Book.svg'},
    ]
    path=""

    // need to set this up
    isLoggedIn = true;
    constructor(container){
        this.container = document.querySelector(container);
        this.fullPath = window.location.pathname;
        this.path = this.fullPath.substring(this.fullPath.lastIndexOf('/')+1)
        this.date = new Date();
        this.render();
        this.setElements();
        this.setListeners();
        this.startTime();
    }

    startTime(){
        const now = new Date();
        this.navbarDate.innerHTML = formatDateLinuxStyle(now);
        const interval = setInterval(()=>{this.handleTimeChange()},1000)
    }

    handleTimeChange = ()=>{
        const now = new Date();
        this.navbarDate.innerHTML = formatDateLinuxStyle(now);
    }

    setElements(){
        this.sidebarBtnOpen = document.querySelector('.sidebar__button--open');
        this.sidebarBtnClose = document.querySelector('.sidebar__button')
        this.sidebar = document.querySelector('.sidebar');
        this.navbarDate = document.querySelector('.navbar__date')

    }
    setListeners(){
        this.sidebarBtnOpen.addEventListener('click',this.openSidebar);
        this.sidebarBtnClose.addEventListener('click',this.closeSidebar);
    }

    closeSidebar = ()=>{
        this.sidebar.classList.remove('sidebar--open');
        this.sidebar.classList.add('sidebar--close');
    }

    openSidebar = ()=>{
        this.sidebar.classList.remove('sidebar--close');
        this.sidebar.classList.add('sidebar--open');
    }

    createSidebarTemplate(){
        const listElem = this.LINKS.map((elem)=>{
            let cssClass= '';
            if(!this.isLoggedIn && elem.title === 'Dashboard') return '';
            if(this.path === elem.tag){
                cssClass = 'link--active';
            }
            
            return(`
                <li class="navbar__link ${cssClass} "><a href="${elem.link}">${elem.title}</a><Img src="${elem.svg}" alt="${elem.title}"/></li>
                `);
        })

        let cssClass = '';
        let loginText = 'Login'
        // user should go here
        if(this.isLoggedIn){cssClass='--auth'; loginText=`<span class="green">@</span>herb`};

        const sidebar = document.createElement('div');
        sidebar.classList.add('sidebar');
        sidebar.classList.add('sidebar--close');
        sidebar.innerHTML = `
            <div class="sidebar__top">
                <div class="sidebar__header">
                    <h2 class="sidebar__logo">Linux-Lab</h2>
                    <button type="button" class="button sidebar__button sidebar__button--close"><img src="../../pages/assets/SVGs/Close.svg" /></button>
                </div>
                <nav class="sidebar__links">
                    <ul>
                    ${listElem.join('')}
                    </ul>
                </nav>
            </div>
            <div class="sidebar__bottom">
                <button class="sidebar__button--user${cssClass}"><a href="../../pages/login/login.php">${loginText}</a></button>
                <button type="button sidebar__button--settings">
                    <img class="sidebar__img--settings" src="../assets/SVGs/Settings.svg " class="svg" alt="cog">
                </button>
            </div>
        `;
        return sidebar;
    }

    createNavbarTemplate(){
        const listElem = this.LINKS.map((elem)=>{
            let cssClass= '';
            if(!this.isLoggedIn && elem.title === 'Dashboard') return '';
            if(this.path === elem.tag){
                cssClass = 'link--active--desktop';
            }
            
            return(`
                <li class="navbar__link navbar--link--desktop  ${cssClass} "><a href="${elem.link}">${elem.title}</a></li>
                `);
        })
        const nav = document.createElement('nav');
        nav.classList.add('navbar');
        nav.innerHTML = `
            <button type="button" class="sidebar__button--open"><img class="svg" src="../assets/SVGs/sidebar-left-svgrepo-com.svg" alt="idk"></button>
            <div class="navbar__left">
                    <img src="../assets/SVGs/Tux.svg.png" class="logo" alt="Linux-Lab logo">
            </div> 
            <div class="navbar__right">
                <ul class="navbar__links">
                ${listElem.join('')}
                </ul>
                <p class="navbar__date hidden no-select">Sat Mar 29 18:40:41 PDT 2025</p>
            </div> 
        `;
        return nav;
    }

    render(){
        this.container.appendChild(this.createSidebarTemplate());
        this.container.appendChild(this.createNavbarTemplate());
    }
}