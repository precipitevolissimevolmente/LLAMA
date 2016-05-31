<?php
abstract class Action
{
    const START = "START";
    const TIMER = "TIMER";
    const START_TEST = "START_TEST";
    const NEXT = "NEXT";
    const CHOSE = "CHOSE";
    const CLOSE = "CLOSE";
}

abstract class ActionImage
{
    const START = "img/start.png";
    const TIMER = "img/hourglass.png";
    const NEXT = "img/next.png";
    const CHOSE = "img/chose.png";
    const CLOSE = "img/end.png";
}

abstract class ProgramPhase
{
    const NOT_STARTED = "NOT_STARTED";
    const LEARN_PHASE_STARTED = "LEARN_PHASE_STARTED";
    const TEST_PHASE = "TEST_PHASE";
    const TEST_PHASE_STARTED = "TEST_PHASE_STARTED";
}

abstract class Resources
{
    const RESULT = "RESULT";
    const PROGRAM_PHASE = "PROGRAM_PHASE";
    const VOCABULARY_KEY_VALUE = "VOCABULARY_KEY_VALUE";
    const VOCABULARY_VALUE_KEY = "VOCABULARY_VALUE_KEY";
    const VOCABULARY_RANDOMIZED_VALUES = "VOCABULARY_RANDOMIZED_VALUES";
    const TEST_CASE = "TEST_CASE";
    const START_TIME = "START_TIME";
}