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
            const NEXT_IMG_PATH = "img/next.png";
            const CHOSE_IMG_PATH = "img/chose.png";
            const LISTEN_IMG_PATH = "img/listen.png";
            const END_IMG_PATH = "img/end.png";

            //initial state
            const REST_SERVICE_URL = 'restServiceE.php';
            const CORRECT = 'CORRECT';
            const WRONG = 'WRONG';
            const END_TEST_SESSION = 'END_TEST_SESSION';
            const GUESS = 'GUESS';

            const LOADING = "LOADING";
            const NOT_STARTED = "NOT_STARTED";
            const LEARN_PHASE = "LEARN_PHASE";
            const TEST_PHASE = "TEST_PHASE";
            const TEST_PHASE_STARTED = "TEST_PHASE_STARTED";

            var result = {}; //Obj.
            var trainingSounds; // ["latE01.wav", ...]
            var testQuestionsOrder; // ["LatEpata.wav", ...]
            var testQuestions; // {"LatEpata.wav": {v1, v2}
            var testCase = 0; // Integer
            var startTime;

            $scope.data = {next_action: "img/start.png", data: ""};
            setProgressResultBar(0);
            disableSoundButtons();
            $scope.score = "";
            $scope.nrOfSeconds = 120;
            setLeftSpelling("");
            setRightSpelling("");

            $scope.loadTest = function () {
                // $scope.PROGRAM_PHASE = LOADING;

                var parameter = {
                    data: {action: "START"}
                };

                var req = buildGETRequest(parameter);
                var myDataPromise = getData(req);

                myDataPromise.then(function (result) {
                    var data = JSON.parse(window.atob(result.data.data));
                    trainingSounds = data.trainingSounds;
                    testQuestionsOrder = data.testQuestionsOrder;
                    testQuestions = data.testQuestions;

                    // Load sounds
                    var queue = new createjs.LoadQueue();
                    createjs.Sound.alternateExtensions = ["wav"];
                    queue.installPlugin(createjs.Sound);

                    var sounds = []; //List of songs, id vs sound path; ex: [{id: "latd01.wav", src: "dsounds/latd01.wav"},..]
                    trainingSounds.forEach(function (sound) {
                        sounds.push({id: sound, src: "resources/esounds/" + sound});
                    });

                    testQuestionsOrder.forEach(function (sound) {
                        sounds.push({id: sound, src: "resources/esounds/" + sound});
                    });

                    sounds.push({id: "CHORD", src: "sounds/CHORD.WAV"});
                    sounds.push({id: "DING", src: "sounds/DING.WAV"});

                    queue.addEventListener("fileload", handleFileLoad);
                    queue.addEventListener("complete", handleComplete);
                    queue.loadManifest(sounds);
                });
            };

            $scope.start = function () {
                var participantName = $scope.participantName;
                var nrOfSeconds = $scope.nrOfSeconds;

                if (participantName == null || participantName == "" || nrOfSeconds == null || nrOfSeconds == "") {
                    alert("Please Fill All Required Field");
                    return false;
                }
                initResult(participantName, nrOfSeconds);
                disableStartButton();
                enableSoundButtons();

                setTimeout(function () {
                    playChord();
                    enableNextButton();
                    disableSoundButtons();
                }, nrOfSeconds * 1000);
            };


            $scope.process = function (response) {
                disableResponseButtons();
                enableNextButton();

                var userResponseTime = new Date().getTime() - startTime;
                var testSound = testQuestionsOrder[testCase - 1];

                var userResponse = testQuestions[testSound][response];
                $scope.PROGRAM_PHASE = TEST_PHASE;
                setNextActionIMG(NEXT_IMG_PATH);

                var isCorrect = false;
                if (userResponse["isCorrect"] == true) {
                    isCorrect = true;
                    setProgressResultBar($scope.progress + 5);
                    result.finalResult++;
                    playDing();
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
            };


            $scope.next = function () {
                disableNextButton();
                setNextActionIMG(LISTEN_IMG_PATH);
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
                $scope.PROGRAM_PHASE = TEST_PHASE_STARTED;
                const qSound = testQuestionsOrder[testCase];
                var instance = playSound(qSound);
                instance.on("complete", function () {
                    setNextActionIMG(CHOSE_IMG_PATH);
                    $scope.$apply();
                });
                setLeftSpelling(testQuestions[qSound].v1.text);
                setRightSpelling(testQuestions[qSound].v2.text);
                testCase++;
                startTime = new Date().getTime();
                enableResponseButtons();
            };

            $scope.getSound = function (index) {
                var soundFileName = trainingSounds[index];
                playSound(soundFileName);
            };

            $scope.close = function () {
                window.close();
            };

            function buildGETRequest(parameters) {
                return {method: 'GET', url: REST_SERVICE_URL, params: parameters};
            }

            function setLeftSpelling(value) {
                $scope.left_spelling = $sce.trustAsHtml(value);
            }

            function setRightSpelling(value) {
                $scope.right_spelling = $sce.trustAsHtml(value);
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

            function makeRequest(req) {
                $http(req).success(function (data, status) {
                    $scope.status = status;
                    $scope.data = data;
                }).error(function (data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
                });
            }

            function buildPOSTRequest(reqData) {
                return {
                    method: 'POST',
                    url: 'restServiceE.php',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: reqData
                };
            }

            function initResult(participantName, nrOfSeconds) {
                result.name = participantName;
                result.nrOfSeconds = nrOfSeconds;
                result.testResults = [];
                result.startDateTime = getUTCDateNow();
                result.finalResult = "";
            }

            function handleFileLoad(event) {
                // Update the UI
                document.getElementById('dots').innerHTML += '.';
            }

            function setNextActionIMG(imgPath) {
                $scope.next_action = imgPath;
            }

            function handleComplete(event) {
                //hide loading div
                document.getElementById('loading').style.display = 'none';
            }

        }]);

    function disableSoundButtons() {
        var soundButtons = document.getElementsByClassName("sound-btn"); //returns NodeList
        Array.from(soundButtons).forEach(disableButton);
    }

    function enableSoundButtons() {
        var soundButtons = document.getElementsByClassName("sound-btn"); //returns NodeList
        Array.from(soundButtons).forEach(enableButton);
    }

    function disableButton(button) {
        button.disabled = true;
    }

    function enableButton(button) {
        button.disabled = false;
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
        document.getElementById("left-spelling").disabled = true;
        document.getElementById("right-spelling").disabled = true;
    }

    function enableResponseButtons() {
        document.getElementById("left-spelling").disabled = false;
        document.getElementById("right-spelling").disabled = false;
    }
})(window.angular);