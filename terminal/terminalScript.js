const terminal = document.querySelector('.terminal');
const command_line = document.querySelector('.command-line');
const command = document.querySelector('.input');
const history = document.querySelector('.history');

terminal.addEventListener('click',()=>{
   command_line.focus();
})

const keyHandler = {
    'Backspace': (e)=>backspace(e),
    'Meta': ()=> specialKeys(),
    'Enter': ()=> enter(),

    'ArrowLeft': (e)=> caretMovePos(e),
    'ArrowRight': (e)=> caretMovePos(e),

    'ArrowUp': ()=> arrowUp(),
    'ArrowDown': ()=> arrowDown(),
}

function specialKeys(){
    console.log(`special key used`);
    return;
}

function enter(){
    if(temp === commandHistory.at(commandHistory.length-2) || temp === ""){
        // do not push to array but execute command
        commandHistory[commandHistoryIndex] = commandHistoryClean[commandHistoryIndex];
        output(temp);
        temp = "";
        commandHistoryIndex = commandHistory.length-1;
        console.log(commandHistory);

    }
    else{
        commandHistoryClean[commandHistoryClean.length-1] = temp;
        commandHistoryClean.push("");
        commandHistory[commandHistory.length-1] = temp;
        commandHistory.push("");
        commandHistory[commandHistoryIndex] = commandHistoryClean[commandHistoryIndex];
        output(temp);
        temp = "";
        commandHistoryIndex = commandHistory.length-1;
        console.log(commandHistory);

    }
}

function output(command){
    const prevCommand = document.createElement("div");
    const prevOutput = document.createElement("div")

    prevCommand.classList.toggle("history-command");
    prevOutput.classList.toggle("history-output");

    const prompt = `<span class="prompt">$</span>`

    prevCommand.innerHTML= prompt + `${command}`;
    prevOutput.innerText = `shl: command not found: ${command}`;

    history.appendChild(prevCommand);
    history.appendChild(prevOutput);
}



function arrowUp(){
    if(commandHistory.length==0 || commandHistoryIndex==0)return;
    commandHistoryIndex-=1;
    temp = commandHistory.at(commandHistoryIndex);
    caretPos = temp.length;
}

function arrowDown(){
    if(commandHistory.length-1 === commandHistoryIndex)return;
    commandHistoryIndex+=1;
    temp = commandHistory.at(commandHistoryIndex);
    caretPos = temp.length;
}

function caretMovePos(e){
    if (e.key === `ArrowLeft`){
        if(caretPos === 0)return;
        caretPos-=1;
    }
    else{
        if(caretPos>=temp.length)return;
        caretPos+=1;
    }

}
function backspace(e){
    // option+del 
    if(e.metaKey){temp = ""; caretPos=0; 
        commandHistory[commandHistoryIndex] = temp;
        return;
    }
    // meta+del 
    if(e.ctrlKey){temp = ""; caretPos=temp.length;
        commandHistory[commandHistoryIndex] = temp;
        return;
    }
    // temp = temp.slice(0,temp.length - 1);
    inputLeft = temp.slice(0,caretPos-1);
    inputRight= temp.slice(caretPos,temp.length);
    temp = inputLeft+inputRight;
    commandHistory[commandHistoryIndex] = temp;
    caretPos-=1;
    return;
}

function ctrtDel(){
    temp = "ctrlDel";
    return;
}

function optDel(){
    temp ="";
    return;
}

input = "";
temp = input; 
char = " ";
cursor = `<span class="caret-block">${char}</span>`;
caretPos= 0;

inputLeft = "";
inputRight= "";

commandHistoryIndex = 3;

const commandHistory = [
    "ls | xargs code",
    "hello world",
    "this is test",
    "",
];
const commandHistoryClean= [
    "ls | xargs code",
    "hello world",
    "this is test",
    "",
];

command_line.addEventListener( "keydown",(e)=>{
    e.preventDefault()
    terminal.scrollTop = terminal.scrollHeight - terminal.clientHeight;
    const handler = keyHandler[e.key];
    if(handler) handler(e);
    else if(e.key.length==1 &&!e.altKey){
        // temp = temp + e.key;
        inputText(e.key);
        caretPos+=1 ;
    }
    renderCaret();
});

function inputText(value){
    inputLeft = temp.slice(0,caretPos);
    inputRight= temp.slice(caretPos,temp.length);
    temp = inputLeft + value + inputRight;
    commandHistory[commandHistoryIndex] = temp;
}


function renderCaret(){
    if(temp === ""){
        caretPos = 0;
        char = " ";
        cursor = `<span class="caret-block">${char}</span>`;
        command.innerHTML= temp+cursor;
    }
    else{
        if(temp.length === caretPos)char = " ";
            else {char = temp.charAt(caretPos);}
            console.log(`tempLength:${temp.length}\ncaretPos: ${caretPos}`)
            cursor = `<span class="caret-block">${char}</span>`;
    }
    // temp.at(caretPos) = `[]`
    inputLeft = temp.slice(0,caretPos);
    inputRight= temp.slice(caretPos+1,temp.length);

    // console.log(`left: ${inputLeft}`);
    // console.log(`right: ${inputRight}`);
    
    command.innerHTML= inputLeft+cursor+inputRight;
    // console.log(command.innerHTML);
    // console.log(command.innerText);

}