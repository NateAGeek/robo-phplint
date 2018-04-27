<?php

namespace NateAGeek\Robo\Task\PHPLint;

trait Tasks {

  /**
   * @param string $path
   * @param array $excludes
   * @param array $extensions
   * @return PHPLintTask
   */
  protected function taskPHPLintTask($path, array $excludes = [], array $extensions = ['php']) {
    return $this->task(PHPLintTask::class, $path, $excludes, $extensions);
  }

}