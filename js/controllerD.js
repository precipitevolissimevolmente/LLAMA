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
            $scope.randomisationSequence = 2003;
            $scope.nrOfSeconds = 120;
            $scope.progress = 0;
            $scope.score = "";

            $scope.start = function () {
                disableStartButton();
                var participantName = $scope.participantName;

                if (participantName == null || participantName == "") {
                    alert("Please Fill All Required Field");
                    return false;
                }

                var parameter = JSON.stringify({
                    name: participantName,
                    action: "START"
                });
                var req = buildPOSTRequest(parameter);
                var reqData = {next_action: "img/hourglass.png", data: ""};
                makeRequestWithData(req, reqData);
                playTrainingSoundsAndStartTest();
            };


            $scope.process = function (response) {
                $scope.method = 'GET';
                $scope.url = 'restServiceD.php?test-case-response=' + response;
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == CORRECT) {
                        $scope.progress = $scope.progress + 1;
                        playDing();
                    }
                    if (result.data.result == WRONG) {
                        playChord();
                    }
                    disableResponseButtons();
                    enableNextButton()
                });
            };


            $scope.next = function () {
                $scope.method = 'GET';
                $scope.url = 'restServiceD.php?next=true';
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == END_TEST_SESSION) {
                        playChord();
                        $scope.score = (($scope.progress * 100) / 20) + " %";
                    }
                    var sound = result.data.data;
                    playSound("dsounds/" + sound);
                    disableNextButton();
                    enableResponseButtons()
                });
            };

            $scope.close = function () {
                var parameter = JSON.stringify({
                    action: "CLOSE"
                });
                var req = {
                    method: 'POST',
                    url: 'restServiceD.php',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: parameter
                };
                makeRequest(req);
                window.close();
            };

            function playTrainingSoundsAndStartTest() {
                var sounds = [
                    "dsounds/latd01.wav",
                    "dsounds/latd02.wav",
                    "dsounds/latd03.wav",
                    "dsounds/latd04.wav",
                    "dsounds/latd05.wav",
                    "dsounds/latd06.wav",
                    "dsounds/latd07.wav",
                    "dsounds/latd08.wav",
                    "dsounds/latd09.wav",
                    "dsounds/latd10.wav"
                ];

                var playlist_index = 0;
                var audio = new Audio();
                audio.src = sounds[playlist_index];
                audio.loop = false;
                audio.play();
                audio.addEventListener("ended", function () {
                    if (playlist_index < sounds.length - 1) {
                        switchTrack();
                    } else {
                        playChord();
                        var startTestJSON = JSON.stringify({action: "START_TEST"});
                        var reqStartTest = buildPOSTRequest(startTestJSON);
                        var reqData = {next_action: "img/next.png", data: ""};
                        makeRequestWithData(reqStartTest, reqData);
                        enableNextButton();
                    }
                });

                function switchTrack() {
                    setTimeout(function () {
                        playlist_index++;
                        audio.src = sounds[playlist_index];
                        audio.play();
                    }, 1000);
                }
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
                    url: 'restServiceD.php',
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