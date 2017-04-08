<?php
include('php/config.php');
include('php/session.php');
include('php/util.php');
$userDetails = $userClass->userDetails($session_uid);
$result_b = getResultFromDb($userDetails->name, "b");
$result_d = getResultFromDb($userDetails->name, "d");
$result_e = getResultFromDb($userDetails->name, "e");
$result_f = getResultFromDb($userDetails->name, "f");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LLMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/util.css">
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
        <button type="button" class="btn btn-info left" data-toggle="collapse" data-target="#llama-b">Read instructions
        </button>
        <div id="llama-b" class="collapse left">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            <img src="img/llamab.JPG" class="img-responsive" alt="Responsive image">
            <p>This sub-test requires you to memorise names of different objects. There is first a 2 minute long
                <em>training phase</em>
                where you click on the objects to learn their names, followed by a
                <em>test phase</em>
                where you see a name and have to
                match it with the right object.</p>
            <p class="bg-warning">Do not take notes or use help in other ways.</p>
            <table>
                <tr>
                    <td>
                        Note: Do not change anything in these boxes
                    </td>
                    <td><img src="img/llamab-param.JPG" class="img-responsive img-mini" alt="Responsive image">
                    </td>
                    <td>. They are default settings and should remain as they are.
                    </td>
                </tr>
            </table>

            <ol>
                <li>
                    <p>TRAINING PHASE</p>
                    <span>
                    When you click on an object, the object's name (a word) will be shown in the middle of the test
                    screen.
                    Try to memorise as many words as possible during this exercise period. The exercise time is 2
                    minutes.
                        <table>
                        <tr>
                            <td>
                    Click the START button
                            </td>
                            <td>
                                <img src="img/start-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                in the upper right corner to start the training phase.
                            </td>
                        </tr>
                        </table>
                        The limited exercise
                    time
                    starts counting down immediately upon clicking this button!
                    </span>
                </li>
                <li>
                    <p>TEST PHASE</p>
                    <span>
                        <table>
                        <tr>
                            <td>
                    When you click on the centre arrow
                                 </td>
                            <td>
                                <img src="img/next-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                a name appears in the box above.
                            </td>
                        </tr>
                        </table>
                        You must then click on the
                    object associated with that name. Click the centre arrow again for the next word to be displayed. In
                    total, 20 words will be tested. The test phase has no time constraint.
                    <br/>
                        <table>
                        <tr>
                            <td>
                    After answering all the questions you may close the test window or click the EXIT button in the
                    lower right corner.
                              </td>
                            <td>
                                <img src="img/exit-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                        </tr>
                        </table>
                    </span>
                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success left"
                onclick="openPopUp('llamab.php', 575, 615, 'Llama B'); return false;"
                target="_blank">GOT IT! Take me to the first sub-test
        </button>
        <p class="bg-success left padding6">Result: <?php echo $result_b ?>%</p>
    </div>

    <div class="clearfix">
        <h4>Subtest 2 - LLAMA D (sound sequences)</h4>
        <button type="button" class="btn btn-info left" data-toggle="collapse" data-target="#llama-d">Read instructions
        </button>
        <div id="llama-d" class="collapse left">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            <img src="img/llamad.JPG" class="img-responsive" alt="Responsive image">
            This sub-test is a sound recognition task where you have to decide if words that you listen to are familiar
            (you have heard them before) or unfamiliar (you have never heard them before).
            <p class="bg-warning">Do not take notes or use help in other ways.</p>
            <ol>
                <li>
                    <p>TRAINING PHASE</p>
                    <span>
                    In the first part of the test, the computer plays a sequence of ten words. Listen carefully to these
                    words. Later, in the test phase, you will hear these words alongside other words that you have not
                    heard before.
                        <table>
                        <tr>
                            <td>
                            When you click the START button
                            </td>
                            <td>
                                <img src="img/start-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                in the upper right corner,
                            </td>
                        </tr>
                        </table> the ten words will be played immediately so make sure to be fully focused from the start.
                        You can not pause playback or repeat the words. The training phase ends with a beep.
                    </span>
                </li>
                <li>
                    <p>TEST PHASE</p>
                    <span>
                        <table>
                        <tr>
                            <td>
                                When you click on the centre arrow
                            <td>
                                <img src="img/next-mini.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                the computer plays a word.
                            </td>
                        </tr>
                        </table>
                        Click on the happy face if you think that you have heard the word before (during the training phase). Click on the sad face if you have not heard the word before. In total, 30 words will be tested. The test phase has no time constraint.
                        <br/>
                        <table>
                        <tr>
                            <td>
                    After answering all the questions you may close the test window or click the EXIT button in the
                    lower right corner.
                              </td>
                            <td>
                                <img src="img/exit-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                        </tr>
                        </table>
                    </span>
                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success left"
                onclick="openPopUp('llamad.php', 310, 400, 'Llama D'); return false;"
                target="_blank">GOT IT! Take me to the second sub-test
        </button>
        <p class="bg-success left padding6">Result: <?php echo $result_d ?>%</p>
    </div>

    <div class="clearfix">
        <h4>Subtest 3 - LLAMA E (sound–spelling)</h4>
        <button type="button" class="btn btn-info left" data-toggle="collapse" data-target="#llama-e">Read instructions
        </button>
        <div id="llama-e" class="collapse left">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            <img src="img/llamae.JPG" class="img-responsive" alt="Responsive image">
            This sub-test requires you to learn a spelling system, that is, to make connections between sounds and
            symbols. There is first a 2 minute long <em>training phase</em> where you click on the symbols to hear
            their sounds, followed by a <em>test phase</em> where you hear a spoken word and have to match it with the
            right spelling.
            <p class="bg-info">You can take any written notes that you need.</p>
            <table>
                <tr>
                    <td>
                        Note: Do not change the default exercise time setting
                    </td>
                    <td>
                        <img src="img/time-input.JPG" class="img-responsive img-mini" alt="Responsive image">
                    </td>
                    <td>
                        .
                    </td>
                </tr>
            </table>
            <ol>
                <li>
                    <p>TRAINING PHASE</p>
                    <span>
                        When you click on a symbol, the computer plays a sound. Your task is to learn the correspondences between sound and symbol, much like a spelling system (alphabet) in a real language.
                        Click on the symbols and try to learn as much as possible of this spelling system. Exercise time is 2 minutes.
                        <table>
                        <tr>
                            <td>
                                Click the START button
                            </td>
                            <td>
                                <img src="img/start-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                in the upper right corner to start the training phase.
                            </td>
                        </tr>
                        </table>
                        The limited exercise time starts counting down immediately upon clicking this button!
                    </span>
                </li>
                <li>
                    <p>TEST PHASE</p>
                    <span>
                        <table>
                        <tr>
                            <td>
                                When you click on the centre arrow
                            </td>
                            <td>
                                <img src="img/next-mini.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                the computer plays a short word.
                            </td>
                        </tr>
                        </table>
                        Your task is to choose between two alternative spellings of the word that you hear.
                        Click the spelling that you think corresponds to the spoken word. In total, 20 such words are tested. The test phase has no time constraint.
                        <br/>
                        <table>
                        <tr>
                            <td>
                                After answering all the questions you may close the test window or click the EXIT button in the
                                lower right corner.
                            </td>
                            <td>
                                <img src="img/exit-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                        </tr>
                        </table>
                    </span>
                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success left"
                onclick="openPopUp('llamae.php', 380, 500, 'Llama E'); return false;"
                target="_blank">GOT IT! Take me to the third sub-test
        </button>
        <p class="bg-success left padding6">Result: <?php echo $result_e ?>%</p>
    </div>

    <div class="clearfix">
        <h4>Subtest 4 -LLAMA F (grammar)</h4>
        <button type="button" class="btn btn-info left" data-toggle="collapse" data-target="#llama-f">Read instructions
        </button>
        <div id="llama-f" class="collapse left">
            <blockquote>READ ALL INSTRUCTIONS FIRST. TEST ONLY RUNS IN FIREFOX, CHROME AND EDGE.</blockquote>
            <img src="img/llamaf.JPG" class="img-responsive" alt="Responsive image">
            This sub-test requires you to learn grammar and vocabulary in an unfamiliar language. There is first a 5
            minute long <em>training phase</em> where you study correspondences between pictures and sentences that
            describe the pictures. This is followed by a <em>test phase</em> where you are shown pictures that have to
            be matched with their correct sentences.
            <p class="bg-info">You can take any written notes that you need.</p>
            <table>
                <tr>
                    <td>
                        Note: Do not change the default exercise time setting
                    </td>
                    <td>
                        <img src="img/time-input.JPG" class="img-responsive img-mini" alt="Responsive image">
                    </td>
                    <td>
                        .
                    </td>
                </tr>
            </table>
            <ol>
                <li>
                    <h4>TRAINING PHASE</h4>
                    <span>
                    When you click on any of the 20 small squares on the screen's left side, this activates an image in
                    the picture frame on the right, and a sentence that describes that particular picture. click on the
                    squares and study the images and their associated sentences. Try to learn as much as possible of
                    this unknown language. Practice time is 5 minutes.
                    <table>
                        <tr>
                            <td>
                                Click the START button
                            </td>
                            <td>
                                <img src="img/start-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                in the upper right corner to start the training phase.
                            </td>
                        </tr>
                    </table>
                    The limited exercise time starts counting down immediately upon clicking this button!
                    </span>
                </li>
                <li>
                    <p>TEST PHASE</p>
                    <span>
                    <table>
                        <tr>
                            <td>
                                When you click on the centre arrow
                            </td>
                            <td>
                                <img src="img/next-mini.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                            <td>
                                , an image appears on the screen and to the left of it are two
                                alternative sentences.
                            </td>
                        </tr>
                    </table>
                    Your task is to decide which of the two sentences accurately describes the
                    image. The test phase has no time constraint.
                    <br/>
                    <table>
                        <tr>
                            <td>
                                After answering all the questions you may close the test window or click the EXIT button in the
                                lower right corner.
                            </td>
                            <td>
                                <img src="img/exit-btn.JPG" class="img-responsive img-mini" alt="Responsive image">
                            </td>
                        </tr>
                        </table>
                    </span>
                </li>
            </ol>
        </div>

        <button type="button" class="btn btn-success left"
                onclick="openPopUp('llamaf.php', 440, 610, 'Llama F'); return false;"
                target="_blank">GOT IT! Take me to the fourth sub-test
        </button>
        <p class="bg-success left padding6">Result: <?php echo $result_f ?>%</p>
    </div>


    <h4 style="float: right;"><a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-primary">Logout</a></h4>
</div>
</body>
</html>