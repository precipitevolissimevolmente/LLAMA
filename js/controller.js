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

    function playChord() {
        var audio = new Audio('sounds/CHORD.WAV');
        audio.play();
    }

    function playDing() {
        var audio = new Audio('sounds/DING.WAV');
        audio.play();
    }

    myApp.controller('mainController', ['$scope', '$http',
        function ($scope, $http) {
            //initial state
            const CORRECT = 'CORRECT';
            const WRONG = 'WRONG';
            const END_TEST_SESSION = 'END_TEST_SESSION';
            const GUESS = 'GUESS';

            const NOT_STARTED = "NOT_STARTED";
            const LEARN_PHASE_STARTED = "LEARN_PHASE_STARTED";
            const TEST_PHASE = "TEST_PHASE";
            const TEST_PHASE_STARTED = "TEST_PHASE_STARTED";

            disableNextBtn();
            disablePictureButtons();
            $scope.data = {next_action: "img/start.png", data: ""};
            $scope.randomisationSequence = 2003;
            $scope.nrOfSeconds = 120;
            $scope.progress = 0;
            $scope.score = "";
            $scope.PROGRAM_PHASE = NOT_STARTED;

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

            var getData = function (req) {
                // Angular $http() and then() both return promises themselves
                return $http(req).then(function (result) {
                    // What we return here is the data that will be accessible
                    // to us after the promise resolves
                    return result;
                });
            };

            $scope.start = function () {
                disableStartBtn();
                enablePictureButtons();
                var nrOfSeconds = $scope.nrOfSeconds;
                var randomisationSequence = $scope.randomisationSequence;
                var participantName = $scope.participantName;

                if (participantName == null || participantName == "" ||
                    nrOfSeconds == null || nrOfSeconds == "" || randomisationSequence == null || randomisationSequence == "") {
                    alert("Please Fill All Required Field");
                    enableStartBtn();
                    disablePictureButtons();
                    return false;
                }
                $scope.PROGRAM_PHASE = LEARN_PHASE_STARTED;

                var parameter = JSON.stringify({
                    name: participantName,
                    nrOfSeconds: nrOfSeconds,
                    randomisationSequence: randomisationSequence,
                    action: "START"
                });
                var req = {
                    method: 'POST',
                    url: 'restService.php',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: parameter
                };
                var reqData = {next_action: "img/hourglass.png", data: ""};
                makeRequestWithData(req, reqData);

                setTimeout(function () {
                    playChord();
                    var startTestJSON = JSON.stringify({
                        action: "START_TEST"
                    });
                    var reqStartTest = {
                        method: 'POST',
                        url: 'restService.php',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: startTestJSON
                    };
                    var reqData = {next_action: "img/next.png", data: ""};
                    makeRequestWithData(reqStartTest, reqData);
                    $scope.PROGRAM_PHASE = TEST_PHASE;
                    disablePictureButtons();
                    enableNextBtn();
                }, nrOfSeconds * 1000);
            };


            $scope.process = function (picId) {
                if ($scope.PROGRAM_PHASE == TEST_PHASE) {
                    disablePictureButtons();
                }
                $scope.method = 'GET';
                $scope.url = 'restService.php?pic-id=' + picId;
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    if (result.data.result == CORRECT) {
                        $scope.progress = $scope.progress + 1;
                        enableNextBtn();
                        playDing();
                    }
                    if (result.data.result == WRONG) {
                        enableNextBtn();
                        playChord();
                    }
                });
            };

            $scope.next = function () {
                disableNextBtn();
                $scope.method = 'GET';
                $scope.url = 'restService.php?next=true';
                $scope.code = null;
                $scope.response = null;

                var req = {method: $scope.method, url: $scope.url};
                var myDataPromise = getData(req);
                myDataPromise.then(function (result) {
                    $scope.data = result.data;
                    enablePictureButtons();
                    if (result.data.result == END_TEST_SESSION) {
                        $scope.score = (($scope.progress * 100) / 20) + " %";
                        disablePictureButtons();
                        playChord();
                    }
                });
            };

            $scope.close = function () {
                var parameter = JSON.stringify({
                    action: "CLOSE"
                });
                var req = {
                    method: 'POST',
                    url: 'restService.php',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: parameter
                };
                makeRequest(req);
                window.close();
            };

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