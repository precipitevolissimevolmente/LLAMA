<?php

class Result
{
    var $name;
    var $surname;
    var $randomisationSequence;
    var $nrOfSeconds;
    var $testResults = [];
    var $finalResult;

    public static function fromJSON($json)
    {
        $json = json_decode($json, true);
//        logg($json);
        $result = new Result();
        $result->setName($json["name"]);
        $result->setSurname($json["surname"]);
        $result->setRandomisationSequence($json["randomisationSequence"]);
        $result->setNrOfSeconds($json["nrOfSeconds"]);
        if(isset($json["finalResult"])) {
            $result->setFinalResult($json["finalResult"]);
        }
        if(isset($json["testResults"])) {
            foreach ($json["testResults"] as $value) {
//                logg($value["questionNumber"]);
                $result->addTestResult(new TestResult($value["questionNumber"], $value["question"], $value["answer"], $value["answerTimeSeconds"]));
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getRandomisationSequence()
    {
        return $this->randomisationSequence;
    }

    /**
     * @param mixed $randomisationSequence
     */
    public function setRandomisationSequence($randomisationSequence)
    {
        $this->randomisationSequence = $randomisationSequence;
    }

    /**
     * @return mixed
     */
    public function getNrOfSeconds()
    {
        return $this->nrOfSeconds;
    }

    /**
     * @param mixed $nrOfSeconds
     */
    public function setNrOfSeconds($nrOfSeconds)
    {
        $this->nrOfSeconds = $nrOfSeconds;
    }

    /**
     * @return mixed
     */
    public function getFinalResult()
    {
        return $this->finalResult;
    }

    /**
     * @param mixed $finalResult
     */
    public function setFinalResult($finalResult)
    {
        $this->finalResult = $finalResult;
    }

    public function addTestResult($testResult)
    {
        array_push($this->testResults, $testResult);
    }

}

class TestResult
{
    var $questionNumber;
    var $question;
    var $answer;
    var $answerTimeSeconds;

    /**
     * TestResult constructor.
     * @param $questionNumber
     * @param $question
     * @param $answer
     * @param $answerTimeSeconds
     */
    public function __construct($questionNumber, $question, $answer, $answerTimeSeconds)
    {
        $this->questionNumber = $questionNumber;
        $this->question = $question;
        $this->answer = $answer;
        $this->answerTimeSeconds = $answerTimeSeconds;
    }

    /**
     * @return mixed
     */
    public function getQuestionNumber()
    {
        return $this->questionNumber;
    }

    /**
     * @param mixed $questionNumber
     */
    public function setQuestionNumber($questionNumber)
    {
        $this->questionNumber = $questionNumber;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getAnswerTimeSeconds()
    {
        return $this->answerTimeSeconds;
    }

    /**
     * @param mixed $answerTimeSeconds
     */
    public function setAnswerTimeSeconds($answerTimeSeconds)
    {
        $this->answerTimeSeconds = $answerTimeSeconds;
    }


}