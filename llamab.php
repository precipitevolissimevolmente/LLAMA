<?php
include('php/config.php');
include('php/session.php');
$userDetails=$userClass->userDetails($session_uid);
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LLAMA B</title>
    <link rel="stylesheet" href="css/app.css">
    <script src="js/util.js"></script>
    <script src="js/angular-1.5.5/angular.js"></script>
    <script src="js/controller.js"></script>
    <script src="js/countdown.js"></script>
</head>
<body ng-app="llamab" class="blue">

<div ng-controller="mainController" class="container llamab">
    <div class="header">
        <div class="top-header grey group">
            <p class="left title">Llama_B</p>
            <sup class="left">v1.0</sup>
            <p class="right company">_lognostics</p>
        </div>

        <div class="options grey2 group padding5-10 margin-top7">
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
                    <img src="img/shuffle2.jpg" class="img-small left"/>
                    <div class="padding4 left">
                        <input ng-model="randomisationSequence" required id="randomisationSequence" type="number"
                               class="input-small input-blue"/>
                    </div>
                    <img src="img/timer2.jpg" class="img-small left"/>
                    <div class="padding4 left">
                        <input ng-model="nrOfSeconds" required id="nrOfSeconds" type="number"
                               class="input-small input-blue"/>
                    </div>
                    <button ng-click="inputParams.$valid && startTest()" id="startBtn"
                            class="btn left btn-small margin-top5"
                            onclick="startTimer()">
                        <img src="img/start.png">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="main grey group">
        <table class="main-table">
            <tr>
                <td>
                    <input type="image" src="pic/4261f33b8997a679c50288981384ce1e4171744c79d3551a3f57d68fa9baf9d2.jpg"
                         class="img-medium pictureB"
                         ng-click="process('4261f33b8997a679c50288981384ce1e4171744c79d3551a3f57d68fa9baf9d2')"/></td>
                <td>
                    <input type="image" src="pic/a9314df8e7a97a83ae264d6a94792aa0eb997b9498ddf4a3c4ca739575caf898.jpg"
                         class="img-medium pictureB"
                         ng-click="process('a9314df8e7a97a83ae264d6a94792aa0eb997b9498ddf4a3c4ca739575caf898')"/></td>
                <td>
                    <input type="image" src="pic/6704255ed4c764417f8af0edc9f68df89ead3eb7aaf80ccce2d77a2210639807.jpg"
                         class="img-medium pictureB"
                         ng-click="process('6704255ed4c764417f8af0edc9f68df89ead3eb7aaf80ccce2d77a2210639807')"/></td>
                <td>
                    <input type="image" src="pic/c0dab1ee754c9ab4f2fc589ecf77976e284ded9c8916a60add01d898f05684e1.jpg"
                         class="img-medium pictureB"
                         ng-click="process('c0dab1ee754c9ab4f2fc589ecf77976e284ded9c8916a60add01d898f05684e1')"/></td>
                <td>
                    <input type="image" src="pic/27156e49489b105f4eece80613640b0ceceab64016c50c4cd2fe6ca31fa5aa01.jpg"
                         class="img-medium pictureB"
                         ng-click="process('27156e49489b105f4eece80613640b0ceceab64016c50c4cd2fe6ca31fa5aa01')"/></td>
                <td>
                    <input type="image" src="pic/f642f0ef54be7314b8d2dda3ea7259279faa25a75f51553639a9a183c21803e4.png"
                         class="img-medium pictureB"
                         ng-click="process('f642f0ef54be7314b8d2dda3ea7259279faa25a75f51553639a9a183c21803e4')"/></td>
            </tr>
            <tr>
                <td>
                    <input type="image" src="pic/3a386d2bb69adffcbcfea4538faca407290740802a8aab640f8572e4f5895604.jpg"
                         class="img-medium pictureB"
                         ng-click="process('3a386d2bb69adffcbcfea4538faca407290740802a8aab640f8572e4f5895604')"/></td>
                <td>
                    <input type="image" src="pic/ef7b8ea786269de0ac3af0c64a73132ca467e754e3c762afb0f740f0b967066d.jpg"
                         class="img-medium pictureB"
                         ng-click="process('ef7b8ea786269de0ac3af0c64a73132ca467e754e3c762afb0f740f0b967066d')"/></td>
                <td colspan="2" rowspan="2">
                    <div class="central-controller">
                        <input type="text" disabled id="pic-name" class="input-medium" value="{{pictureName}}"/>
                        <button ng-click="next()" id="next" class="btn left btn-large">
                            <img src="img/next.png">
                        </button>
                        <canvas id="timer" width="50" height="50" class="right margin-top5"></canvas>
                    </div>
                </td>
                <td>
                    <input type="image" src="pic/3d5a1e840f63f5de620a3420773d47d232ff0afd0256d1f2c88f68f08d9ad0b4.jpg"
                         class="img-medium pictureB"
                         ng-click="process('3d5a1e840f63f5de620a3420773d47d232ff0afd0256d1f2c88f68f08d9ad0b4')"/></td>
                <td>
                    <input type="image" src="pic/cfba9de3e226afcf414addeb854657a1f45b4573763a5899701e2c13fa3bc791.png"
                         class="img-medium pictureB"
                         ng-click="process('cfba9de3e226afcf414addeb854657a1f45b4573763a5899701e2c13fa3bc791')"/></td>
            </tr>
            <tr>
                <td>
                    <input type="image" src="pic/5eb1f0f9433a0b6f61dd40a6c4369a77cbeaa0a98402d5b4f1add182dd7f4e23.jpg"
                         class="img-medium pictureB"
                         ng-click="process('5eb1f0f9433a0b6f61dd40a6c4369a77cbeaa0a98402d5b4f1add182dd7f4e23')"/></td>
                <td>
                    <input type="image" src="pic/dc1813c6b54ade95acef0024fd158f0f6aa37e61d06503c7a499705e2dae2852.jpg"
                         class="img-medium pictureB"
                         ng-click="process('dc1813c6b54ade95acef0024fd158f0f6aa37e61d06503c7a499705e2dae2852')"/></td>
                <td>
                    <input type="image" src="pic/48b3a2299f9dfb4e57069e6f43bea4b91cef8a68668b9d3250119a9d1bc624a7.jpg"
                         class="img-medium pictureB"
                         ng-click="process('48b3a2299f9dfb4e57069e6f43bea4b91cef8a68668b9d3250119a9d1bc624a7')"/></td>
                <td>
                    <input type="image" src="pic/91597823f4fe25e64d3bb6ac364d0fa044745a59235c6924ba75071eff63997f.png"
                         class="img-medium pictureB"
                         ng-click="process('91597823f4fe25e64d3bb6ac364d0fa044745a59235c6924ba75071eff63997f')"/></td>
            </tr>
            <tr>
                <td>
                    <input type="image" src="pic/2d433cf8bdb5ffa2ea1de2b9122c7598e903a0ce5c1a7fee1349b58bb698b722.jpg"
                         class="img-medium pictureB"
                         ng-click="process('2d433cf8bdb5ffa2ea1de2b9122c7598e903a0ce5c1a7fee1349b58bb698b722')"/></td>
                <td>
                    <input type="image" src="pic/8583b687d11e5dafb1fb91ed35e240303f12b196852c60d40d8a8a65acf6b62e.jpg"
                         class="img-medium pictureB"
                         ng-click="process('8583b687d11e5dafb1fb91ed35e240303f12b196852c60d40d8a8a65acf6b62e')"/></td>
                <td>
                    <input type="image" src="pic/bbb0ff7f67bbfadc00d07b211ea0b3eb6c45648a6572c2ca0b64399b0a99e4a6.jpg"
                         class="img-medium pictureB"
                         ng-click="process('bbb0ff7f67bbfadc00d07b211ea0b3eb6c45648a6572c2ca0b64399b0a99e4a6')"/></td>
                <td>
                    <input type="image" src="pic/ec751a71391e89c20ea6eab482d5ffbd1d11278ea11ff9c54737d1a8be536f69.jpg"
                         class="img-medium pictureB"
                         ng-click="process('ec751a71391e89c20ea6eab482d5ffbd1d11278ea11ff9c54737d1a8be536f69')"/></td>
                <td>
                    <input type="image" src="pic/58e1cbbfb34b4b51602060ac78d9516c669682f87fc9384317c46418e0201868.png"
                         class="img-medium pictureB"
                         ng-click="process('58e1cbbfb34b4b51602060ac78d9516c669682f87fc9384317c46418e0201868')"/></td>
                <td>
                    <input type="image" src="pic/5721bbe880e9d30bb4caf6455363bd3c5be9e844239019e91d175277dce0420b.png"
                         class="img-medium pictureB"
                         ng-click="process('5721bbe880e9d30bb4caf6455363bd3c5be9e844239019e91d175277dce0420b')"/></td>
            </tr>
        </table>
    </div>

    <div class="bottom-controller grey group">
        <div class="left">
            <img src="img/smile.png" class="img-mini left">
            <div class="margin-top7 left">
                <progress id="progress" class="progress-bar progress-bar-width-normal" ng-model="progress"
                          value="{{progress}}" max="20"></progress>
            </div>
        </div>
        <div class="right">
            <div class="margin-top7 left">
                <input ng-model="score" id="score" type="text" disabled class="input-medium margin-right15"/>
            </div>
            <div id="status-section-mini" class="left">
                <div id="status" class="yellow left">
                    <img ng-src="{{next_action}}">
                </div>
            </div>
            <button class="btn left btn-small margin-top5" ng-click="close()">
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
<script>
    function startTimer() {
        var nrOfSeconds = document.getElementById("nrOfSeconds").value;
        var randomisationSequence = document.getElementById("randomisationSequence").value;
        var participantName = document.getElementById("participantName").value;

        if (participantName == null || participantName == "" ||
                nrOfSeconds == null || nrOfSeconds == "" || randomisationSequence == null || randomisationSequence == "") {
            return false;
        }
        startTimerCanvas(nrOfSeconds);
    }
</script>
</html>