<?php

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