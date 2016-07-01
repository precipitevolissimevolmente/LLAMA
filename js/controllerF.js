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

    function playSound(soundFile) {
        new Audio(soundFile).play();
    }

    function playChord() {
        playSound('sounds/CHORD.WAV');
    }

    function playDing() {
        playSound('sounds/DING.WAV');
    }

    myApp.controller('mainController', ['$scope', '$http', '$sce',
        function ($scope, $http, $sce) {
            //initial state
            const REST_SERVICE_URL = 'restServiceF.php';
            const CORRECT = 'CORRECT';
            const WRONG = 'WRONG';
            const END_TEST_SESSION = 'END_TEST_SESSION';
            const GUESS = 'GUESS';

            $scope.data = {next_action: "img/start.png", data: ""};
            setProgressResultBar(0);
            disableNextButton();
            disableLearnButtons();
            $scope.score = "";
            $scope.nrOfSeconds = 300;
            $scope.picture="";
            setSentenceV1("");
            setSentenceV2("");

            $scope.start = function () {
                var participantName = $scope.participantName;
                var nrOfSeconds = $scope.nrOfSeconds;

                if (participantName == null || participantName == "" || nrOfSeconds == null || nrOfSeconds == "") {
                    alert("Please Fill All Required Field");
                    return false;
                }
                disableStartButton();
                enableLearnButtons();

                var parameter = JSON.stringify({
                    name: participantName,
                    nrOfSeconds: nrOfSeconds,
                    action: "START"
                });
                var req = buildPOSTRequest(parameter);
                var reqData = {next_action: "img/hourglass.png", data: ""};
                makeRequestWithData(req, reqData);
                setTimeout(function () {
                    playChord();
                    var startTestJSON = JSON.stringify({
                        action: "START_TEST"
                    });
                    var reqStartTest = {
                        method: 'POST',
                        url: REST_SERVICE_URL,
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: startTestJSON
                    };
                    var reqData = {next_action: "img/next.png", data: ""};
                    makeRequestWithData(reqStartTest, reqData);
                    enableNextButton();
                }, nrOfSeconds * 1000);
            };


            $scope.process = function (response) {
                $scope.method = 'GET';
                $scope.url = REST_SERVICE_URL+'?test-case-response=' + response;
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                disableResponseButtons();
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == CORRECT) {
                        setProgressResultBar($scope.progress + 5);
                        playDing();
                    }
                    if (result.data.result == WRONG) {
                        setProgressResultBar($scope.progress - 5);
                        playChord();
                    }
                    enableNextButton()
                });
            };


            $scope.next = function () {
                $scope.method = 'GET';
                $scope.url = REST_SERVICE_URL+'.php?next=true';
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                disableNextButton();
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == END_TEST_SESSION) {
                        playChord();
                        $scope.score = ($scope.progress) + " %";
                    } else {
                        var testCase = result.data.data;
                        var audioFile = new Audio();
                        audioFile.src = "resources/esounds/" + testCase.soundFileName;
                        audioFile.loop = false;
                        audioFile.play();
                        audioFile.addEventListener("ended", function () {
                            document.getElementById("next-action").src = "img/chose.png";
                            enableResponseButtons();
                        });
                        setSentenceV1(testCase.v1);
                        setSentenceV2(testCase.v2);
                    }
                });
            };

            $scope.getPicture = function (index) {
                $scope.method = 'GET';
                $scope.url = REST_SERVICE_URL+'?pictureIndex=' + index;
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    var pictureName = result.data.data.pictureName;
                    $scope.picture = "resources/fpictures/" + pictureName;
                    var sentence = result.data.data.sentence;
                    setSentenceV1(sentence);
                });
            };

            $scope.close = function () {
                var parameter = JSON.stringify({
                    action: "CLOSE"
                });
                var req = {
                    method: 'POST',
                    url: REST_SERVICE_URL,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: parameter
                };
                makeRequest(req);
                window.close();
            };

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

            function makeRequestWithData(req, reqData) {
                $http(req).success(function (data, status) {
                    $scope.status = status;
                    $scope.data = reqData;
                }).error(function (data, status) {
                    $scope.data = data || "Request failed";
                    $scope.status = status;
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
                    url: REST_SERVICE_URL,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: reqData
                };
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
})(window.angular);