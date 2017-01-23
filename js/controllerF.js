(function (angular) {
    'use strict';
    var myApp = angular.module('llamab', []);

    myApp.config(['$httpProvider', function ($httpProvider) {
        delete $httpProvider.defaults.headers.common['X-Requested-With'];
        $httpProvider.defaults.headers.post['Accept'] = 'application/json, text/javascript';
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/json; charset=utf-8';
        $httpProvider.defaults.headers.common['Accept'] = 'application/json, text/javascript';
        $httpProvider.defaults.headers.common['Content-Type'] = 'application/json; charset=utf-8';
        $httpProvider.defaults.useXDomain = true;
    }]);

    function playSound(soundId) {
        return createjs.Sound.play(soundId);
    }

    function playChord() {
        playSound('CHORD');
    }

    function playDing() {
        playSound('DING');
    }

    myApp.controller('mainController', ['$scope', '$http', '$sce',
        function ($scope, $http, $sce) {
            const START_IMG_PATH = "img/start.png";
            const TIMER_IMG_PATH = "img/hourglass.png";
            const NEXT_IMG_PATH = "img/next.png";
            const CHOSE_IMG_PATH = "img/chose.png";
            const END_IMG_PATH = "img/end.png";

            //initial state
            const REST_SERVICE_URL = 'restServiceF.php';
            const CORRECT = 'CORRECT';
            const WRONG = 'WRONG';
            const END_TEST_SESSION = 'END_TEST_SESSION';
            const GUESS = 'GUESS';

            var result = {}; //Obj.
            var trainingQuestions; // [{"pictureName": "bm01.bmp","sentence": "atak-arap-sa"}
            var testQuestions; // "bmt01.bmp": {"v1": {"text": "eket-arap-sa","isCorrect": true}, "v2": {"text": "eket-arap","isCorrect": false}} ...
            var testQuestionsOrder; // ["bmt01.bmp", ...]
            var testCase = 0; // Integer
            var startTime;
            var pictureMap = {};

            $scope.data = {next_action: "img/start.png", data: ""};
            setProgressResultBar(0);
            disableNextButton();
            disableLearnButtons();
            $scope.score = "";
            $scope.nrOfSeconds = 300;
            setSentenceV1("");
            setSentenceV2("");

            $scope.loadTest = function () {
                setNextActionIMG(START_IMG_PATH);

                var parameter = {
                    data: {action: "START"}
                };

                var req = buildGETRequest(parameter);
                var myDataPromise = getData(req);

                myDataPromise.then(function (result) {
                    var data = JSON.parse(window.atob(result.data.data));
                    trainingQuestions = data.trainingQuestions;
                    testQuestions = data.testQuestions;
                    testQuestionsOrder = data.testQuestionsOrder;

                    var queue = new createjs.LoadQueue();
                    createjs.Sound.alternateExtensions = ["wav"];
                    queue.installPlugin(createjs.Sound);

                    var sounds = []; //List of songs, id vs sound path; ex: [{id: "latd01.wav", src: "dsounds/latd01.wav"},..]
                    sounds.push({id: "CHORD", src: "sounds/CHORD.WAV"});
                    sounds.push({id: "DING", src: "sounds/DING.WAV"});

                    // queue.addEventListener("fileload", handleFileLoad);
                    // queue.addEventListener("complete", handleComplete);
                    queue.loadManifest(sounds);

                    var queueImg = new createjs.LoadQueue();
                    queueImg.on("fileload", handleFileLoadImg);
                    queueImg.on("complete", handleComplete);
                    var imgFileArray = [];
                    imgFileArray.push({id: "1px.png", src: "resources/fpictures/1px.png"});
                    testQuestionsOrder.forEach(function (picture) {
                        imgFileArray.push({id: picture, src: "resources/fpictures/" + picture});
                    });
                    trainingQuestions.forEach(function (question) {
                        imgFileArray.push({
                            id: question.pictureName,
                            src: "resources/fpictures/" + question.pictureName
                        });
                    });

                    queueImg.loadManifest(imgFileArray);
                    queueImg.load();
                });
            };

            $scope.start = function () {
                var participantName = $scope.participantName;
                var nrOfSeconds = $scope.nrOfSeconds;
                setNextActionIMG(TIMER_IMG_PATH);
                if (participantName == null || participantName == "" || nrOfSeconds == null || nrOfSeconds == "") {
                    alert("Please Fill All Required Field");
                    return false;
                }
                disableStartButton();
                enableLearnButtons();
                initResult(participantName, nrOfSeconds);

                setTimeout(function () {
                    disableLearnButtons();
                    enableNextButton();
                    playChord();
                    setNextActionIMG(NEXT_IMG_PATH);
                    setSentenceV1("");
                    setPicture("1px.png");
                    $scope.$apply();
                }, nrOfSeconds * 1000);
            };


            $scope.process = function (response) {
                disableResponseButtons();
                var userResponseTime = new Date().getTime() - startTime;
                var testImage = testQuestionsOrder[testCase - 1];

                var isCorrect = false;
                if (testQuestions[testImage][response].isCorrect == true) {
                    setProgressResultBar($scope.progress + 5);
                    result.finalResult++;
                    playDing();
                    isCorrect = true;
                } else {
                    setProgressResultBar($scope.progress - 5);
                    playChord();
                }

                var testCaseResult = {
                    questionNumber: testCase - 1,
                    question: testQuestionsOrder[testCase - 1],
                    answer: response,
                    isCorrect: isCorrect,
                    answerTimeSeconds: userResponseTime / 1000
                };
                result.testResults.push(testCaseResult);
                setNextActionIMG(NEXT_IMG_PATH);
                setPicture("1px.png");
                setSentenceV1("");
                setSentenceV2("");
                enableNextButton();
            };

            $scope.next = function () {
                disableNextButton();
                setNextActionIMG(CHOSE_IMG_PATH);
                if (testCase === testQuestionsOrder.length) {
                    setNextActionIMG(END_IMG_PATH);
                    playChord();
                    if ((result.finalResult - 10) > 0) {
                        result.finalResult = parseFloat(10 * result.finalResult - 100).toFixed(0) + "%";
                    } else {
                        result.finalResult = "0%";
                    }
                    $scope.score = result.finalResult;
                    var req = buildPOSTRequest(window.btoa(JSON.stringify(result)));
                    makeRequest(req);
                    return;
                }
                var pictureName = testQuestionsOrder[testCase];
                setPicture(pictureName);
                setSentenceV1(testQuestions[pictureName].v1.text);
                setSentenceV2(testQuestions[pictureName].v2.text);
                testCase++;
                startTime = new Date().getTime();
                enableResponseButtons();
            };

            $scope.getPicture = function (index) {
                var pictureName = trainingQuestions[index].pictureName;
                setPicture(pictureName);
                var sentence = trainingQuestions[index].sentence;
                setSentenceV1(sentence);
            };

            $scope.close = function () {
                window.opener.location.reload();
                window.close();
            };

            function setPicture(pictureName) {
                var elem = pictureMap[pictureName];
                document.getElementById("picture-placeholder").innerHTML = "";
                document.getElementById("picture-placeholder").appendChild(elem);
            }

            function setSentenceV1(value) {
                $scope.sentenceV1 = value;
            }

            function setSentenceV2(value) {
                $scope.sentenceV2 = value;
            }

            function setProgressResultBar(progressResult) {
                $scope.progress = progressResult;
                if ($scope.progress > 0) {
                    $scope.progressUI = $scope.progress;
                } else {
                    $scope.progressUI = 0;
                }
                document.getElementById("progress-result").style.width = progressResult + "%";
            }

            function getData(req) {
                // Angular $http() and then() both return promises themselves
                return $http(req).then(function (result) {
                    // What we return here is the data that will be accessible
                    // to us after the promise resolves
                    return result;
                });
            }

            function initResult(participantName, nrOfSeconds) {
                result.name = participantName;
                result.nrOfSeconds = nrOfSeconds;
                result.testResults = [];
                result.startDateTime = getUTCDateNow();
                result.finalResult = "";
            }

            function buildGETRequest(parameters) {
                return {method: 'GET', url: REST_SERVICE_URL, params: parameters};
            }

            function buildPOSTRequest(reqData) {
                return {
                    method: 'POST',
                    url: REST_SERVICE_URL,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: reqData
                };
            }

            function makeRequest(req) {
                $http(req).success(function (data, status) {
                    $scope.status = status;
                    $scope.data = data;
                }).error(function (data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });
            }

            function handleFileLoad(event) {
                // Update the UI
                document.getElementById('dots').innerHTML += '.';
            }

            function handleFileLoadImg(event) {
                // Update the UI
                document.getElementById('dots').innerHTML += '.';
                pictureMap[event.item.id] = event.result;
            }

            function setNextActionIMG(imgPath) {
                $scope.next_action = imgPath;
            }

            function handleComplete(event) {
                //hide loading div
                document.getElementById('loading').style.display = 'none';
                setPicture("1px.png");
            }
        }]);

    function disableButton(button) {
        button.disabled = true;
    }

    function enableButton(button) {
        button.disabled = false;
    }

    function disableLearnButtons() {
        var soundButtons = document.getElementsByClassName("learnBtn"); //returns NodeList
        Array.from(soundButtons).forEach(disableButton);
    }

    function enableLearnButtons() {
        var soundButtons = document.getElementsByClassName("learnBtn"); //returns NodeList
        Array.from(soundButtons).forEach(enableButton);
    }

    function disableStartButton() {
        document.getElementById("startBtn").disabled = true;
    }

    function disableNextButton() {
        document.getElementById("btn-next").disabled = true;
    }

    function enableNextButton() {
        document.getElementById("btn-next").disabled = false;
    }

    function disableResponseButtons() {
        document.getElementById("btn-sentence-v1").disabled = true;
        document.getElementById("btn-sentence-v2").disabled = true;
    }

    function enableResponseButtons() {
        document.getElementById("btn-sentence-v1").disabled = false;
        document.getElementById("btn-sentence-v2").disabled = false;
    }

    window.onunload = refreshParent;
    function refreshParent() {
        window.opener.location.reload();
    }
})(window.angular);