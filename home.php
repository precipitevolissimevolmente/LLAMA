<?php
include('php/config.php');
include('php/session.php');
$userDetails = $userClass->userDetails($session_uid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LLMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap_3.3.7.js"></script>
    <script language="javascript" type="text/javascript">
        function openPopUp(adr, height, width, windowName) {
            var x = screen.width / 2 - width / 2;
            var y = screen.height / 2 - height / 2;
            window.open(adr, windowName,
                'toolbar=no, menubar=no, location=no, scrollbars=no, resizable=no, height=' + height + ',width=' + width + ',left=' + x + ',top=' + y);
        }
    </script>
</head>
<body>
<div class="container">
    <h3>Welcome <?php echo $userDetails->name; ?></h3>
    <h4>
        The language aptitude test consists of four sub-tests. Take these sub-tests in the order they appear below. Each
        sub-test takes about 5-10 minutes to complete.
    </h4>
    <h4><p class="text-danger">Important:</p> Make sure to read the test instructions very carefully before you take
        each
        sub-test. Do not start a
        test without having read its instructions!
    </h4>
    <br/>
    <div class="clearfix">
        <h4>Subtest 1 - LLAMA B (word-picture)</h4>
        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#llama-b">Read instructions
        </button>
        <div id="llama-b" class="collapse">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            <img src="img/llamab.JPG" class="img-responsive" alt="Responsive image">
            This sub-test requires you to memorise names of different objects. There is first a 2 minute long
            <em>training phase</em>
            where you click on the objects to learn their names, followed by a
            <em>test phase</em>
            where you see a name and have to
            match it with the right object.
            <ol>
                <li>
                    It is not allowed to take notes on paper or to use help in other ways. Your responses will be
                    subjected
                    to statistical processing.
                    Note: Do not change anything in these boxes . They are default settings and should remain as they
                    are
                </li>
                <li>
                    <h4>TRAINING PHASE</h4>
                    When you click on an object, the object's name (a word) will be shown in the middle of the test
                    screen.
                    Try to memorise as many words as possible during this exercise period. The exercise time is 2
                    minutes.
                    Click the START button in the upper right corner to start the training phase. The limited exercise
                    time
                    starts counting down immediately upon clicking this button!

                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success"
                onclick="openPopUp('llamab.php', 575, 615, 'Llama B'); return false;"
                target="_blank">GOT IT! Take me to the first sub-test
        </button>
    </div>

    <div class="clearfix">
        <h4>Subtest 2 - LLAMA D (sound sequences)</h4>
        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#llama-d">Read instructions
        </button>
        <div id="llama-d" class="collapse">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            This sub-test requires you to memorise names of different objects. There is first a 2 minute long
            <em>training phase</em>
            where you click on the objects to learn their names, followed by a
            <em>test phase</em>
            where you see a name and have to
            match it with the right object.
            <ol>
                <li>
                    It is not allowed to take notes on paper or to use help in other ways. Your responses will be
                    subjected
                    to statistical processing.
                    Note: Do not change anything in these boxes . They are default settings and should remain as they
                    are
                </li>
                <li>
                    <h4>TRAINING PHASE</h4>
                    When you click on an object, the object's name (a word) will be shown in the middle of the test
                    screen.
                    Try to memorise as many words as possible during this exercise period. The exercise time is 2
                    minutes.
                    Click the START button in the upper right corner to start the training phase. The limited exercise
                    time
                    starts counting down immediately upon clicking this button!

                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success"
                onclick="openPopUp('llamad.php', 310, 400, 'Llama D'); return false;"
                target="_blank">GOT IT! Take me to the second sub-test
        </button>
    </div>

    <div class="clearfix">
        <h4>Subtest 3 - LLAMA E (sound–spelling)</h4>
        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#llama-e">Read instructions
        </button>
        <div id="llama-e" class="collapse">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            This sub-test requires you to memorise names of different objects. There is first a 2 minute long
            <em>training phase</em>
            where you click on the objects to learn their names, followed by a
            <em>test phase</em>
            where you see a name and have to
            match it with the right object.
            <ol>
                <li>
                    It is not allowed to take notes on paper or to use help in other ways. Your responses will be
                    subjected
                    to statistical processing.
                    Note: Do not change anything in these boxes . They are default settings and should remain as they
                    are
                </li>
                <li>
                    <h4>TRAINING PHASE</h4>
                    When you click on an object, the object's name (a word) will be shown in the middle of the test
                    screen.
                    Try to memorise as many words as possible during this exercise period. The exercise time is 2
                    minutes.
                    Click the START button in the upper right corner to start the training phase. The limited exercise
                    time
                    starts counting down immediately upon clicking this button!

                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success"
                onclick="openPopUp('llamae.php', 380, 500, 'Llama E'); return false;"
                target="_blank">GOT IT! Take me to the third sub-test
        </button>
    </div>

    <div class="clearfix">
        <h4>Subtest 4 -LLAMA F (grammar)</h4>
        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#llama-f">Read instructions
        </button>
        <div id="llama-f" class="collapse">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            This sub-test requires you to memorise names of different objects. There is first a 2 minute long
            <em>training phase</em>
            where you click on the objects to learn their names, followed by a
            <em>test phase</em>
            where you see a name and have to
            match it with the right object.
            <ol>
                <li>
                    It is not allowed to take notes on paper or to use help in other ways. Your responses will be
                    subjected
                    to statistical processing.
                    Note: Do not change anything in these boxes . They are default settings and should remain as they
                    are
                </li>
                <li>
                    <h4>TRAINING PHASE</h4>
                    When you click on an object, the object's name (a word) will be shown in the middle of the test
                    screen.
                    Try to memorise as many words as possible during this exercise period. The exercise time is 2
                    minutes.
                    Click the START button in the upper right corner to start the training phase. The limited exercise
                    time
                    starts counting down immediately upon clicking this button!

                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success"
                onclick="openPopUp('llamaf.php', 440, 610, 'Llama F'); return false;"
                target="_blank">GOT IT! Take me to the fourth sub-test
        </button>
    </div>


    <h4 style="float: right;"><a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-primary">Logout</a></h4>
</div>
</body>
</html>