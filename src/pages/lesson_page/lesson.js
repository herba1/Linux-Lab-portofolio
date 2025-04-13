import VanillaTerminal from "./termC/term.js";
import { NavigationLesson } from "../../components/NavigationLesson/index.js";

const sectionLesson = document.querySelector(".section--lesson");

class LessonManager {
  lesson = 1;
  currentSection = "basics";
  sectionSize = 0;
  user = 0;
  lessons = [];
  constructor() {
    this.lesson = 1;
    this.currentSection = "basics";
    this.setupListeners();
    this.user = 0;
    this.fetchUserInfoInit();
  }

  setupListeners() {
    document.addEventListener("section:update", this.handleLessonSectionChange);
    document.addEventListener("completed:update", this.handleLessonCompleted);
  }
  
  handleLessonCompleted = () => {
    // Update our user completed json in memory
    this.lessons[this.currentSection][this.lesson].completed = true;
    // Broadcast update to UI
    this.broadcastUpdate();
  };

  async fetchUserInfoInit() {
    try {
      // Static mock data for user info
      const mockUserData = {
        0: {
          "lesson": 1,
          "section": "basics"
        }
      };
      
      this.lesson = mockUserData[this.user]["lesson"];
      this.currentSection = mockUserData[this.user]["section"];
      this.fetchLessonsInit();
    } catch (error) {
      console.error(error);
    }
  }

  async fetchUserInfo() {
    try {
      // For static site, just update UI with current values
      this.broadcastUpdate();
    } catch (error) {
      console.error(error);
    }
  }

  async fetchLessonsInit() {
    try {
      // Fetch the lessons.json for static site
      const request = await fetch("../../testAPI/lessons.json");
      if (!request.ok) {
        throw new Error(`could not get lessons ${request.status}`);
      }
      const data = await request.json();
      this.lessons = data;
      this.sectionSize = this.lessons[this.currentSection][0][`section__size`];
      this.broadcastUpdate();
    } catch (error) {
      console.error(error);
    }
  }

  broadcastUpdate() {
    const event = new CustomEvent(`state:update`, {
      detail: {
        user: {
          currentLessonId: this.lesson,
          currentSection: this.currentSection,
        },
        lessons: this.lessons,
      },
    });
    document.dispatchEvent(event);
  }

  // handle lesson and section changes bundled
  handleLessonSectionChange = (e) => {
    let { section, lessonId, action } = e.detail;
    if (action === "next") {
      // if we are at last lesson or beyond lesson scope
      if (lessonId >= this.sectionSize) {
        lessonId = this.sectionSize;
        console.log(`cant go over section size`);
      }
    }

    if (action === "prev") {
      // if we are at last lesson or beyond lesson scope
      if (lessonId <= 1) {
        lessonId = 1;
        console.log(`cant go under section size`);
      }
    }
    if (action === "change") {
      // make sure for new section change lessonid is within the new bounds
      if (this.lessons[section][0].section__size <= lessonId)
        lessonId = this.lessons[section][0].section__size;
    }

    // Update locally for static site
    this.lesson = lessonId;
    this.currentSection = section;
    this.fetchUserInfo();
  };
}

class LessonNav {
  isOpen = false;
  constructor(container = ".lesson__nav") {
    this.container = document.querySelector(container);
    this.mount();
    this.openCloseButton = document.querySelector(".lesson__nav__button--open");
    this.dropdownContainer = document.querySelector(".lesson__nav__dropdown");
    this.listContainer = document.querySelector(".lesson__nav__links");
    this.initListeners();
  }

  mount() {
    this.container.innerHTML = `
      <button class="lesson__nav__button--open"><span>loading...</span></button>
      <div class="lesson__nav__dropdown hidden">
          <ul class="lesson__nav__links">
          </ul>
      </div>
    `;
  }

  initListeners() {
    document.addEventListener("state:update", this.render);
    this.openCloseButton.addEventListener("click", this.handleToggle);
    this.dropdownContainer.addEventListener("click", this.handleClick);
  }

  handleClick = (e) => {
    const idStr = e.target.id;
    const id = parseInt(idStr.slice(idStr.indexOf(":") + 1, idStr.length));
    const subSection = idStr.slice(0, idStr.indexOf(":"));
    document.dispatchEvent(
      new CustomEvent("section:update", {
        detail: {
          lessonId: id,
          section: "basics",
          action: "change",
        },
      })
    );

    this.dropdownContainer.classList.add("hidden");
    this.isOpen = false;
  };

  handleToggle = (e) => {
    if (!this.isOpen) {
      this.dropdownContainer.classList.remove("hidden");
      this.isOpen = true;
    } else if (this.isOpen) {
      this.dropdownContainer.classList.add("hidden");
      this.isOpen = false;
    }
  };

  render = (e) => {
    const lessons = e.detail.lessons;
    const lesson = e.detail.user.currentLessonId;
    const section = e.detail.user.currentSection;
    const openButtonText = `${lessons[section][lesson].section} - ${lessons[section][lesson].title}`;
    let lastSection = "";
    const listElements = lessons[section]
      .filter((item) => item.id > 0)
      .map((lessonData) => {
        let returnString = "";
        if (lastSection != lessonData.section) {
          lastSection = lessonData.section;
          returnString = `<li class="lesson__nav__subtitle"><h4>${lessonData.section}</h4></li>`;
        }
        if (lesson === lessonData.id) {
          return (
            returnString +
            `<li class="lesson__nav__lesson "><button class="lesson__nav__button active" id="${`${lessonData.section}:${lessonData.id}`}"" >${
              lessonData.title
            }</button></li>`
          );
        } else
          return (
            returnString +
            `<li class="lesson__nav__lesson "><button class="lesson__nav__button" id="${`${lessonData.section}:${lessonData.id}`}"" >${
              lessonData.title
            }</button></li>`
          );
      });
    let listElementsString = listElements.join("");
    this.openCloseButton.innerHTML = `<span>${openButtonText}</span>`;
    this.listContainer.innerHTML = `${listElementsString}`;
  };
}

class lessonDisplay {
  curSection = "basics";
  curLesson = 1;
  sectionSize = 0;
  modules = {};

  constructor(container, lessons = null) {
    this.container = document.querySelector(container);
    this.nextButton = document.querySelector(".lesson__button--next");
    this.prevButton = document.querySelector(".lesson__button--prev");
    this.progBar = document.querySelector(".progress-bar__meter");
    this.misc = document.querySelector(".lesson");
    this.statusCross = document.querySelector(".lesson__status--cross");
    this.statusCheck = document.querySelector(".lesson__status--check");
    this.lessonNavContainer = document.querySelector(".lesson__nav");

    document.addEventListener("state:update", this.handleChange);
    this.initListeners();
  }

  handleChange = (e) => {
    const data = e.detail;
    this.curSection = data[`user`][`currentSection`];
    this.curLesson = data["user"]["currentLessonId"];
    this.modules = data["lessons"];
    console.log('data updated');
    this.sectionSize = this.modules[this.curSection][0]["section__size"];
    this.update();
  };

  initListeners() {
    this.nextButton.addEventListener("click", this.nextLesson);
    this.prevButton.addEventListener("click", this.prevLesson);
    document.addEventListener("command-success", this.handleCorrectEvent);
  }

  update() {
    this.updateMeter();
    this.updateStatus();
    this.render();
  }

  render() {
    console.log('rendering')
    this.container.replaceChildren();
    this.container.innerHTML = `<h1 class="lesson__title">${
      this.modules[this.curSection][this.curLesson]["title"]
    }</h1>
        <div class="lesson__content">${
          this.modules[this.curSection][this.curLesson][`content`]
        }</div>
    `;

    // conditional render multichoice questions
    let completed = false;
    if (
      this.modules[this.curSection][this.curLesson].content_type ===
      "multichoice"
    ) {
      const questionsContainer = document.querySelector(".question__container");
      const questionEntries = Object.entries(
        this.modules[this.curSection][this.curLesson].questions
      );
      const questions = [];
      // render correct questions
      for (const [key, value] of questionEntries) {
        if (
          this.modules[this.curSection][this.curLesson].completed &&
          this.modules[this.curSection][this.curLesson].answer === key
        ) {
          completed = true;
          questions.push(`
            <button type="button" id="${key}" class="question__button question--correct"><span class="question__key">${key})</span><span class="question__value">${value}</span></button>
          `);
        } else {
          questions.push(`
          <button type="button" id="${key}" class="question__button"><span class="question__key">${key})</span><span class="question__value">${value}</span></button>
        `);
        }
      }
      questionsContainer.innerHTML = questions.join("");
      if (completed)
        questionsContainer.classList.add("question__container--correct");
      if (!completed)
        questionsContainer.addEventListener("click", this.handleQuestion);
    }
  }

  handleQuestion = (e) => {
    let button = e.target;
    if (e.target.parentNode.tagName === "BUTTON") button = e.target.parentNode;
    if (button.tagName === "BUTTON") {
      if (this.modules[this.curSection][this.curLesson].answer === button.id) {
        button.classList.add("question--correct");
        button.parentNode.classList.add("question__container--correct");
        this.showSuccessMessage();
        document.dispatchEvent(
          new CustomEvent("completed:update", {
            detail: {
              section: this.curSection,
              lessonId: this.curLesson,
              completed: true,
            },
          })
        );
      } else {
        button.classList.add("question--wrong");
        button.classList.add("animate-shake");
        console.log("wrong");
        setTimeout(() => {
          button.classList.remove("question--wrong");
          button.classList.remove("animate-shake");
          console.log("wrong done");
        }, 1000 * 2);
      }
    }
  };

  nextLesson = () => {
    ++this.curLesson;
    document.dispatchEvent(
      new CustomEvent("section:update", {
        detail: {
          action: "next",
          section: this.curSection,
          lessonId: this.curLesson,
        },
      })
    );
  };

  prevLesson = () => {
    if (this.curLesson <= 1) return;
    --this.curLesson;
    document.dispatchEvent(
      new CustomEvent("section:update", {
        detail: {
          action: "prev",
          section: this.curSection,
          lessonId: this.curLesson,
        },
      })
    );
  };

  changeSection() {
    this.sectionSize = this.modules[this.curSection][0]["section__size"];
    this.sectionSizeInteractive;
  }

  updateMeter() {
    let interactiveCount = this.modules[this.curSection].reduce(
      (sum, lesson) => {
        let value = 0;
        if (
          lesson.content_type === "interactive" ||
          lesson.content_type === "multichoice"
        )
          value = 1;
        return sum + value;
      },
      0
    );
    let completedCount = this.modules[this.curSection].reduce((sum, lesson) => {
      if (
        lesson.content_type != "interactive" &&
        lesson.content_type != "multichoice"
      )
        return sum;
      return sum + (lesson.completed ? 1 : 0);
    }, 0);
    let progValue = (completedCount / interactiveCount) * 100;
    this.progBar.value = progValue;
    if (progValue === 100) {
      document.dispatchEvent(
        new CustomEvent("section:isComplete", {
          detail: {
            userInfo: {
              section: this.curSection,
              lesson: this.curLesson,
            },
          },
        })
      );
    }
  }

  updateStatus() {
    if (this.modules[this.curSection][this.curLesson][`completed`] === true) {
      this.statusCheck.classList.remove(`hidden`);
      this.statusCross.classList.add(`hidden`);
    } else {
      this.statusCheck.classList.add(`hidden`);
      this.statusCross.classList.remove(`hidden`);
    }
  }

  toggleLessonComplete = () => {
    this.modules[this.curSection][this.curLesson][`completed`] =
      !this.modules[this.curSection][this.curLesson][`completed`];
    this.updateMeter();
    this.updateStatus();
  };

  handleCorrectEvent = (e) => {
    this.showSuccessMessage();
  };

  showSuccessMessage() {
    const successMessage = `
            <div class="overlay--success">
                <img src="../assets/SVGs/Tux.svg.png" alt="tux" class="logo">
                <h3 class="overlay__message">Well done!</h3> 
            </div>
            `;
    const overlayContainer = document.createElement("div");
    overlayContainer.classList.add("overlay__container");
    overlayContainer.classList.add("overlay-popup");
    overlayContainer.innerHTML = successMessage;
    sectionLesson.prepend(overlayContainer);
    setTimeout(() => {
      overlayContainer.classList.remove("overlay-popout");
      overlayContainer.classList.add("overlay-fadeout");
    }, 1000 * 2);
    setTimeout(() => {
      overlayContainer.remove();
    }, 1000 * 3);
  }
}

const lessonManager = new LessonManager();
const lessonDisplayController = new lessonDisplay(".lesson");
const terminal = new VanillaTerminal({
  apiEndpoint: "../../../api_commands.php", // No longer used, but kept for compatibility
});

const lessonNav = new LessonNav();

const sidebarLesson = new NavigationLesson(
  ".sidebar__container",
  ".sidebar__button--open"
);

terminal.mount("#terminal__container");