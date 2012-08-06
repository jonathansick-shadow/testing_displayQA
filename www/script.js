window.onload = initAll;

function initAll() {
    show();
    showSql();
    document.getElementById("displayText").onclick = toggle;
    document.getElementById("displaySql").onclick = toggleSql;
}

function show() {
    var ele = document.getElementById("toggleText");
    var text = document.getElementById("displayText");
    ele.style.display = "none";
    text.innerHTML = "Show"
        }
function toggle() {
    var ele = document.getElementById("toggleText");
    var text = document.getElementById("displayText");
    if(ele.style.display == "block") {
        ele.style.display = "none";
        text.innerHTML = "Show"
            }
    else {
        ele.style.display = "block";
        text.innerHTML = "Hide"
            }
} 




function showSql() {
    var ele = document.getElementById("toggleSql");
    var text = document.getElementById("displaySql");
    ele.style.display = "none";
    text.innerHTML = "Show"
        }
function toggleSql() {
    var ele = document.getElementById("toggleSql");
    var text = document.getElementById("displaySql");
    if(ele.style.display == "block") {
        ele.style.display = "none";
        text.innerHTML = "Show"
            }
    else {
        ele.style.display = "block";
        text.innerHTML = "Hide"
            }
} 
