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

    myApp.controller('mainController', ['$scope', '$http',
        function ($scope, $http) {
            //initial state
            const CORRECT = 'CORRECT';
            const WRONG = 'WRONG';
            const END_TEST_SESSION = 'END_TEST_SESSION';
            const GUESS = 'GUESS';

            $scope.data = {next_action: "img/start.png", data: ""};
            setProgressResultBar(0);
            $scope.score = "";
            $scope.nrOfSeconds = 120;
            $scope.left_spelling = "";
            $scope.right_spelling = "";

            $scope.start = function () {
                disableStartButton();
                var participantName = $scope.participantName;
                var nrOfSeconds = $scope.nrOfSeconds;

                if (participantName == null || participantName == "") {
                    alert("Please Fill All Required Field");
                    return false;
                }

                var parameter = JSON.stringify({
                    name: participantName,
                    nrOfSeconds: nrOfSeconds,
                    action: "START"
                });
                var req = buildPOSTRequest(parameter);
                var reqData = {next_action: "img/listen.png", data: ""};
                makeRequestWithData(req, reqData);
            };


            $scope.process = function (response) {
                $scope.method = 'GET';
                $scope.url = 'restServiceE.php?test-case-response=' + response;
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == CORRECT) {
                        setProgressResultBar($scope.progress + 2.5);
                        playDing();
                    }
                    if (result.data.result == WRONG) {
                        setProgressResultBar($scope.progress - 2.5);
                        playChord();
                    }
                    disableResponseButtons();
                    enableNextButton()
                });
            };


            $scope.next = function () {
                $scope.method = 'GET';
                $scope.url = 'restServiceE.php?next=true';
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == END_TEST_SESSION) {
                        playChord();
                        disableNextButton();
                        $scope.score = ($scope.progress) + " %";
                    } else {
                        var sound = result.data.data;
                        var audioFile = new Audio();
                        audioFile.src = "dsounds/" + sound;
                        audioFile.loop = false;
                        audioFile.play();
                        audioFile.addEventListener("ended", function () {
                            // $scope.data.next_action = "img/chose.png";
                            document.getElementById("next-action").src = "img/chose.png";
                        });
                        disableNextButton();
                        enableResponseButtons();

                    }
                });
            };

            $scope.getSound = function (index) {
                $scope.method = 'GET';
                $scope.url = 'restServiceE.php?soundIndex=' + index;
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    var soundFileName = result.data.data;
                    var audioFile = new Audio();
                    audioFile.src = "resources/esounds/" + soundFileName;
                    audioFile.loop = false;
                    audioFile.play();
                });
            };

            $scope.close = function () {
                var parameter = JSON.stringify({
                    action: "CLOSE"
                });
                var req = {
                    method: 'POST',
                    url: 'restServiceE.php',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: parameter
                };
                makeRequest(req);
                window.close();
            };

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
                    url: 'restServiceE.php',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: reqData
                };
            }
        }]);

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
        document.getElementById("newword").disabled = true;
        document.getElementById("familiarword").disabled = true;
    }

    function enableResponseButtons() {
        document.getElementById("newword").disabled = false;
        document.getElementById("familiarword").disabled = false;
    }
})(window.angular);