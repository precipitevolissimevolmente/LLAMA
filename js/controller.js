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

    myApp.controller('mainController', ['$scope', '$http',
        function ($scope, $http) {
            //initial state
            const REST_SERVICE_URL = 'restService.php';
            const START_IMG_PATH = "img/start.png";
            const NEXT_IMG_PATH = "img/next.png";
            const CHOSE_IMG_PATH = "img/chose.png";
            const END_IMG_PATH = "img/end.png";

            const NOT_STARTED = "NOT_STARTED";
            const LEARN_PHASE = "LEARN_PHASE";
            const TEST_PHASE = "TEST_PHASE";
            const TEST_PHASE_STARTED = "TEST_PHASE_STARTED";

            var result = {}; //Obj.
            var vocabulary; // MAP<PictureId,PictureName>
            var testCases; // Array
            var testCase = 0; // Integer

            var startTime;

            disableNextBtn();
            disablePictureButtons();
            setPictureName("");
            setNextActionIMG(START_IMG_PATH);
            $scope.randomisationSequence = 2003;
            $scope.nrOfSeconds = 120;
            $scope.progress = 0;
            $scope.score = "";
            $scope.PROGRAM_PHASE = NOT_STARTED;

            $scope.startTest = function () {
                disableStartBtn();
                var nrOfSeconds = $scope.nrOfSeconds;
                var randomisationSequence = $scope.randomisationSequence;
                var participantName = $scope.participantName;

                if (participantName == null || participantName == "" ||
                    nrOfSeconds == null || nrOfSeconds == "" || randomisationSequence == null || randomisationSequence == "") {
                    alert("Please Fill All Required Field");
                    enableStartBtn();
                    return false;
                }
                initResult(participantName, randomisationSequence, nrOfSeconds);

                var parameter = {
                    data: {
                        name: participantName,
                        nrOfSeconds: nrOfSeconds,
                        randomisationSequence: randomisationSequence,
                        action: "START"
                    }
                };

                var req = buildGETRequest(parameter);
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.PROGRAM_PHASE = LEARN_PHASE;
                    enablePictureButtons();
                    var data = JSON.parse(window.atob(result.data.data));
                    vocabulary = data.vocabulary;
                    testCases = data.testCases;
                });

                setTimeout(function () {
                    disablePictureButtons();
                    $scope.PROGRAM_PHASE = TEST_PHASE;
                    setPictureName("");
                    setNextActionIMG(NEXT_IMG_PATH);
                    $scope.$apply();
                    playChord();
                    enableNextBtn();
                }, nrOfSeconds * 1000);
            };

            $scope.process = function (picId) {
                if ($scope.PROGRAM_PHASE == LEARN_PHASE) {
                    $scope.pictureName = vocabulary[picId];
                }
                if ($scope.PROGRAM_PHASE == TEST_PHASE_STARTED) {
                    disablePictureButtons();
                    $scope.PROGRAM_PHASE = TEST_PHASE;
                    var end = new Date().getTime();
                    var time = end - startTime;
                    setPictureName("");
                    setNextActionIMG(NEXT_IMG_PATH);

                    var answer = vocabulary[picId];
                    var isCorrect = false;
                    if (vocabulary[picId] === testCases[testCase - 1]) {
                        playDing();
                        isCorrect = true;
                        $scope.progress = $scope.progress + 1;
                        result.finalResult++;
                    } else {
                        playChord();
                        isCorrect = false;
                    }

                    var testCaseResult = {
                        questionNumber: testCase - 1,
                        question: testCases[testCase - 1],
                        answer: answer,
                        isCorrect: isCorrect,
                        answerTimeSeconds: time / 1000
                    };
                    result.testResults.push(testCaseResult);
                    enableNextBtn();
                }
            };

            $scope.next = function () {
                disableNextBtn();
                if (testCase === testCases.length) {
                    disablePictureButtons();
                    setPictureName("");
                    setNextActionIMG(END_IMG_PATH);
                    playChord();
                    result.finalResult = ((result.finalResult * 100) / 20) + "%";
                    $scope.score = result.finalResult;
                    var req = buildPOSTRequest(window.btoa(JSON.stringify(result)));
                    makeRequest(req);
                    return;
                }
                $scope.PROGRAM_PHASE = TEST_PHASE_STARTED;
                setPictureName(testCases[testCase]);
                setNextActionIMG(CHOSE_IMG_PATH);
                enablePictureButtons();
                testCase++;
                startTime = new Date().getTime();
            };

            $scope.close = function () {
                window.close();
            };

            function initResult(participantName, randomisationSequence, nrOfSeconds) {
                result.name = participantName;
                result.randomisationSequence = randomisationSequence;
                result.nrOfSeconds = nrOfSeconds;
                result.testResults = [];
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

            function playChord() {
                new Audio('sounds/CHORD.WAV').play();
            }

            function playDing() {
                new Audio('sounds/DING.WAV').play();
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

            var getData = function (req) {
                // Angular $http() and then() both return promises themselves
                return $http(req).then(function (result) {
                    // What we return here is the data that will be accessible
                    // to us after the promise resolves
                    return result;
                });
            };

            function setNextActionIMG(imgPath) {
                $scope.next_action = imgPath;
            }

            function setPictureName(name) {
                $scope.pictureName = name;
            }

            function enableStartBtn() {
                document.getElementById("startBtn").disabled = false;
            }

            function disableStartBtn() {
                document.getElementById("startBtn").disabled = true;
            }

            function enableNextBtn() {
                document.getElementById("next").disabled = false;
            }

            function disableNextBtn() {
                document.getElementById("next").disabled = true;
            }

            function disableButton(button) {
                button.disabled = true;
            }

            function enableButton(button) {
                button.disabled = false;
            }

            function enablePictureButtons() {
                var soundButtons = document.getElementsByClassName("pictureB"); //returns NodeList
                Array.from(soundButtons).forEach(enableButton);
            }

            function disablePictureButtons() {
                var soundButtons = document.getElementsByClassName("pictureB"); //returns NodeList
                Array.from(soundButtons).forEach(disableButton);
            }
        }]);
})(window.angular);