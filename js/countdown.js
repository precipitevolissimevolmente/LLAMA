var totalNumberOfSeconds = 0;
var currentNrOfSeconds = 0;
var counterClockwise = true;
var circ = Math.PI * 2;
var radius = 18;

function startTimerCanvas(nrOfSeconds) {
    totalNumberOfSeconds = nrOfSeconds;
    currentNrOfSeconds = totalNumberOfSeconds - 1;
    fullCircle();
    var myTimer = setInterval(draw, 1000);
    setTimeout(function () {
        clearInterval(myTimer);
        clearCanvas();
    }, totalNumberOfSeconds * 1000);
}

function draw() {
    (function () {
        var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
            window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
        window.requestAnimationFrame = requestAnimationFrame;
    })();

    if(currentNrOfSeconds == 0) {
        currentNrOfSeconds = currentNrOfSeconds + 0.01;
    }
    animate(currentNrOfSeconds, totalNumberOfSeconds);
    currentNrOfSeconds--;
}

function animate(current, totalNumberOfSeconds) {
    var canvas = document.getElementById('timer');
    var context = canvas.getContext('2d');
    var x = canvas.width / 2;
    var y = canvas.height / 2;

    context.lineWidth = 8;
    context.strokeStyle = '#ad2323';
    context.shadowOffsetX = 0;
    context.shadowOffsetY = 0;
    context.shadowBlur = 1;
    context.shadowColor = '#656565';


    context.beginPath();
    const startAngle = 1.5 * Math.PI;
    context.arc(x, y, radius, startAngle, ((current * circ) / totalNumberOfSeconds) + startAngle, counterClockwise);
    context.stroke();
}

function fullCircle() {
    var canvas = document.getElementById('timer');
    var context = canvas.getContext('2d');
    var x = canvas.width / 2;
    var y = canvas.height / 2;

    context.lineWidth = 8;
    context.strokeStyle = '#010080';
    context.shadowOffsetX = 0;
    context.shadowOffsetY = 0;
    context.shadowBlur = 1;
    context.shadowColor = '#656565';


    context.clearRect(0, 0, canvas.width, canvas.height);
    context.beginPath();
    const startAngle = 1.5 * Math.PI;
    context.arc(x, y, radius, 0, circ, counterClockwise);
    context.stroke();
}

function clearCanvas() {
    var canvas = document.getElementById('timer');
    var context = canvas.getContext('2d');

    context.clearRect(0, 0, canvas.width, canvas.height);
}
