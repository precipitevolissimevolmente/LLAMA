<?php
include('php/config.php');
include('php/session.php');
$userDetails=$userClass->userDetails($session_uid);
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LLAMA D</title>
    <link rel="stylesheet" href="css/app.css">
    <script src="js/util.js"></script>
    <script src="js/angular-1.5.5/angular.js"></script>
    <script src="js/controllerD.js"></script>
    <script src="js/preloadjs-0.4.1.min.js"></script>
    <script src="js/soundjs-0.5.2.min.js"></script>
</head>
<body ng-app="llamab" class="blue">
<div ng-controller="mainController" class="container llamad">
    <div id="loading" ng-init="loadTest()">
        Loading test...<br/> Please wait.
        <p id="dots"> </p>
    </div>
    <div class="header">
        <div class="top-header grey group">
            <p class="left title">Llama_D</p>
            <sup class="left">v1.0</sup>
            <p class="right company">_lognostics</p>
        </div>
    </div>
    <div class="main grey group">
        <div class="options grey2 group group-border">
            <form name="inputParams">
                <div>
                    <img src="img/pencil2.jpg" class="left"/>
                    <div class="left padding4">
                        <input ng-model="participantName" required id="participantName" type="text"
                               ng-init="participantName = '<?php echo $userDetails->name; ?>'"
                               class="input-large input-blue" disabled/>
                    </div>
                </div>
                <div class="right">
                    <button ng-click="inputParams.$valid && start()" id="startBtn"
                            class="btn left btn-small margin-top5">
                        <img src="img/start.png">
                    </button>
                </div>
            </form>
        </div>

        <div class="group group-border margin-top7 padding5-10">
            <div>
                <img src="img/check.png" class="img-mini-s left">
                <div class="right">
                    <div class="progress progress-bar-width-short heightNormal">
                        <div id="progress-result" class="progress-bar-div" role="progressbar">
                            {{progressUI}}%
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="right">
                    <div class="progress progress-bar-width-short slim margin-top7">
                        <div id="progress-items" class="progress-bar-div " role="progressbar">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="group group-border margin-top7 padding5-10 grey3">
            <button ng-click="process('new')" id="newword" class="btn left width40p" disabled="disabled">
                <img src="img/newword.png">
            </button>
            <button ng-click="next()" id="btn-next" class="btn left width20p" disabled="disabled">
                <img src="img/next.png">
            </button>
            <button ng-click="process('familiar')" id="familiarword" class="btn left width40p" disabled="disabled">
                <img src="img/familiarword.png">
            </button>
        </div>
    </div>

    <div class="bottom-controller grey group">
        <div>
            <div id="status-section-mini" class="left">
                <div id="status" class="yellow left">
                    <img ng-src="{{next_action}}">
                </div>
            </div>
            <div class="margin-top7 left">
                <input ng-model="score" id="score" type="text" disabled class="input-medium"/>
            </div>
            <button class="btn right btn-small margin-top5" ng-click="close()">
                <img src="img/end.png">
            </button>
        </div>
    </div>

    <footer class="footer grey group">
        <p class="left author"> Paul Meara </p>
        <p class="right">&copy;2005 University of Wales Swansea</p>
    </footer>
</div>

</body>
</html>