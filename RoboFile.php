<?php

class RoboFile extends \Robo\Tasks {

  use NateAGeek\Robo\Task\PHPLint\Tasks;

  function testLinter() {
    $this->taskPHPLintTask(__DIR__ . "/tests")
      ->run();
  }

}