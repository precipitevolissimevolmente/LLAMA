<?php

class ResultE
{
    var $name;
    var $randomisationSequence;
    var $nrOfSeconds;
    var $testResults = [];
    var $finalResult;

    public static function fromJSON($json)
    {
        $json = json_decode($json, true);
//        logg($json);
        $result = new ResultE();
        $result->setName($json["name"]);
        $result->setRandomisationSequence($json["randomisationSequence"]);
        $result->setNrOfSeconds($json["nrOfSeconds"]);
        if(isset($json["finalResult"])) {
            $result->setFinalResult($json["finalResult"]);
        }
        if(isset($json["testResults"])) {
            foreach ($json["testResults"] as $value) {
//                logg($value["questionNumber"]);
                $result->addTestResult(TestResult::withAnswerEvaluation($value["questionNumber"], $value["question"], $value["answer"], $value["isCorrect"], $value["answerTimeSeconds"]));
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