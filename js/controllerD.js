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

    myApp.controller('mainController', ['$scope', '$http',
        function ($scope, $http) {
            //initial state
            const REST_SERVICE_URL = 'restServiceD.php';
            const START_IMG_PATH = "img/start.png";
            const NEXT_IMG_PATH = "img/next.png";
            const CHOSE_IMG_PATH = "img/chose.png";
            const LISTEN_IMG_PATH = "img/listen.png";
            const END_IMG_PATH = "img/end.png";

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
            setNextActionIMG(START_IMG_PATH);
            var result = {}; //Obj.
            var soundMap; // MAP<PictureId,PictureName>
            var testCases; // Array
            var startTime;

            setProgressResultBar(0);
            setProgressItemsBar(0);
            $scope.score = "";
            var testCase = 0; // Integer

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
                    testCases = data.testOrder;

                    // Load sounds
                    var queue = new createjs.LoadQueue();
                    createjs.Sound.alternateExtensions = ["wav"];
                    queue.installPlugin(createjs.Sound);

                    var sounds = []; //List of songs, id vs sound path; ex: [{id: "latd01.wav", src: "dsounds/latd01.wav"},..]
                    for (var key in soundMap) {
                        sounds.push({id: key, src: "dsounds/" + key});
                    }
                    sounds.push({id: "CHORD", src: "sounds/CHORD.WAV"});
                    sounds.push({id: "DING", src: "sounds/DING.WAV"});

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
                initResult(participantName);
                setNextActionIMG(LISTEN_IMG_PATH);

                playTrainingSoundsAndStartTestB();
            };

            function playTrainingSoundsAndStartTestB() {
                var trainingSounds = [];
                for (var key in soundMap) {
                    if (soundMap[key] === 'familiar') {
                        trainingSounds.push(key)
                    }
                }

                var playlist_index = 0;
                var instance = playSound(trainingSounds[playlist_index]);
                instance.on("complete", handleCompleteS);
                setProgressItemsBar(2);
                function handleCompleteS(event) {
                    if (playlist_index < trainingSounds.length - 1) {
                        playNext();
                    } else {
                        playChord();
                        $scope.PROGRAM_PHASE = TEST_PHASE;
                        enableNextButton();
                        setNextActionIMG(NEXT_IMG_PATH);
                        $scope.$apply();
                    }
                }

                function playNext() {
                    setTimeout(function () {
                        playlist_index++;
                        var instance = playSound(trainingSounds[playlist_index]);
                        instance.on("complete", handleCompleteS);
                        setProgressItemsBar($scope.progressItmes + 2);
                    }, 1000);
                }
            }

            $scope.process = function (response) {
                disableResponseButtons();
                enableNextButton();

                $scope.PROGRAM_PHASE = TEST_PHASE;
                var time = new Date().getTime() - startTime;
                setNextActionIMG(NEXT_IMG_PATH);

                var isCorrect = false;
                if (response === soundMap[testCases[testCase - 1]]) {
                    playDing();
                    isCorrect = true;
                    setProgressResultBar($scope.progress + 2.5);
                    result.finalResult++;
                } else {
                    playChord();
                    isCorrect = false;
                    setProgressResultBar($scope.progress - 2.5);
                }

                var testCaseResult = {
                    questionNumber: testCase - 1,
                    question: testCases[testCase - 1],
                    answer: response,
                    isCorrect: isCorrect,
                    answerTimeSeconds: time / 1000
                };
                result.testResults.push(testCaseResult);
            };

            $scope.next = function () {
                disableNextButton();
                setNextActionIMG(LISTEN_IMG_PATH);
                if (testCase === testCases.length) {
                    setNextActionIMG(END_IMG_PATH);
                    playChord();
                    result.finalResult = $scope.progress + "%";
                    $scope.score = result.finalResult;
                    var req = buildPOSTRequest(window.btoa(JSON.stringify(result)));
                    makeRequest(req);
                    return;
                }
                $scope.PROGRAM_PHASE = TEST_PHASE_STARTED;
                setProgressItemsBar($scope.progressItmes + 2);
                var instance = playSound(testCases[testCase]);
                instance.on("complete", function () {
                    setNextActionIMG(CHOSE_IMG_PATH);
                    $scope.$apply();
                });
                testCase++;
                startTime = new Date().getTime();
                enableResponseButtons();
            };

            $scope.close = function () {
                window.close();
            };

            function buildGETRequest(parameters) {
                return {method: 'GET', url: REST_SERVICE_URL, params: parameters};
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

            function initResult(participantName) {
                result.name = participantName;
                result.testResults = [];
                result.startDateTime = getUTCDateNow();
                result.finalResult = "";
            }


            function handleFileLoad(event) {
                // Update the UI
                document.getElementById('dots').innerHTML += '.';
            }

            function handleComplete(event) {
                //hide loading div
                document.getElementById('loading').style.display = 'none';
            }

            function setNextActionIMG(imgPath) {
                $scope.next_action = imgPath;
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