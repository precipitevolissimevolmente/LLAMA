<?php

class ResultD
{
    var $name;
    var $testResults = [];
    var $finalResult;

    public static function fromJSON($json)
    {
        $json = json_decode($json, true);
//        logg($json);
        $result = new ResultD();
        $result->setName($json["name"]);
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
     * @return array
     */
    public function getTestResults()
    {
        return $this->testResults;
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