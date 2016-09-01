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
            const REST_SERVICE_URL = 'restServiceD.php';

            const LOADING = "LOADING";
            const NOT_STARTED = "NOT_STARTED";
            const LEARN_PHASE = "LEARN_PHASE";
            const TEST_PHASE = "TEST_PHASE";
            const TEST_PHASE_STARTED = "TEST_PHASE_STARTED";

            const CORRECT = 'CORRECT';
            const WRONG = 'WRONG';
            const END_TEST_SESSION = 'END_TEST_SESSION';
            const GUESS = 'GUESS';

            $scope.PROGRAM_PHASE = NOT_STARTED;

            var soundMap; // MAP<PictureId,PictureName>
            var testOrder; // Array

            $scope.data = {next_action: "img/start.png", data: ""};
            setProgressResultBar(0);
            setProgressItemsBar(0);
            $scope.score = "";

            $scope.loadTest = function () {
                $scope.PROGRAM_PHASE = LOADING;

                var parameter = {
                    data: {action: "START"}
                };

                var req = buildGETRequest(parameter);
                var myDataPromise = getData(req);

                myDataPromise.then(function (result) {
                    var data = JSON.parse(window.atob(result.data.data));
                    soundMap = data.soundMap;
                    testOrder = data.testOrder;

                    // Load the sounds
                    var queue = new createjs.LoadQueue();
                    createjs.Sound.alternateExtensions = ["wav"];
                    queue.installPlugin(createjs.Sound);

                    var sounds = []; //List of songs, id vs sound path; ex: [{id: "latd01.wav", src: "dsounds/latd01.wav"},..]
                    for (var key in soundMap) {
                        sounds.push({id: key, src: "dsounds/" + key});
                    }

                    queue.addEventListener("fileload", handleFileLoad);
                    queue.addEventListener("complete", handleComplete);
                    queue.loadManifest(sounds);
                });


            };

            $scope.start = function () {
                disableStartButton();
                var participantName = $scope.participantName;

                if (participantName == null || participantName == "") {
                    alert("Please Fill All Required Field");
                    return false;
                }

                playTrainingSoundsAndStartTestB();
            };

            function handleFileLoad(event) {
                // Update the UI
                document.getElementById('dots').innerHTML += '.';
            }

            function handleComplete(event) {
                //hide loading div
                document.getElementById('loading').style.display = 'none';
            }

            function playTrainingSoundsAndStartTestB() {
                var filtered = [];
                for (var key in soundMap) {
                    if (soundMap[key] === 'familiar') {
                        filtered.push(soundMap[key])
                    }
                }

                var instance = createjs.Sound.play(filtered[0]);
                instance.on("complete", handleCompleteS);

                function handleCompleteS(event) {

                    createjs.Sound.play("latd02.wav");
                }
            }

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
                disableNextButton();
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
                        enableResponseButtons();
                        setProgressItemsBar($scope.progressItmes + 2.5);

                    }
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

            function buildGETRequest(parameters) {
                return {method: 'GET', url: REST_SERVICE_URL, params: parameters};
            }

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
                setProgressItemsBar(2.5);
                audio.addEventListener("ended", function () {
                    if (playlist_index < sounds.length - 1) {
                        switchTrack();
                        setProgressItemsBar($scope.progressItmes + 2.5);
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

            function setProgressResultBar(progressResult) {
                $scope.progress = progressResult;
                if ($scope.progress > 0) {
                    $scope.progressUI = $scope.progress;
                } else {
                    $scope.progressUI = 0;
                }
                document.getElementById("progress-result").style.width = progressResult + "%";
            }

            function setProgressItemsBar(progressItems) {
                $scope.progressItmes = progressItems;
                document.getElementById("progress-items").style.width = progressItems + "%";
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