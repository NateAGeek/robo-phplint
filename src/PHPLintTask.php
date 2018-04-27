<?php
namespace NateAGeek\Robo\Task\PHPLint;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use JakubOnderka\PhpConsoleHighlighter\Highlighter;
use Overtrue\PHPLint\Linter;
use Robo\Task\BaseTask;

class PHPLintTask extends BaseTask implements \Robo\Contract\TaskInterface {

  private $linter;
  private $files = [];
  private $use_cache = false;
  private $path;
  private $excludes = [];
  private $extensions = ['php'];
  private $verbose = true;

  public function __construct($path, array $excludes = [], array $extensions = ['php']) {
    $this->excludes = $excludes;
    $this->extensions = $extensions;
    $this->path = $path;

    $this->linter = new Linter($this->path, $this->excludes, $this->extensions);
  }

  public function setExcludes(array $excludes = []) {
    $this->excludes = $excludes;
    $this->linter = new Linter($this->path, $this->excludes, $this->extensions);

    return $this;
  }

  public function setExtensions(array $extensions = ['php']) {
    $this->extensions = $extensions;
    $this->linter = new Linter($this->path, $this->excludes, $this->extensions);

    return $this;
  }

  public function setFiles(array $files = []) {
    $this->files = $files;
    return $this;
  }

  public function setUseCache($use_cache) {
    $this->use_cache = $use_cache;
    return $this;
  }

  public function run() {
    $errors = $this->linter->lint($this->files, $this->use_cache);
    if($this->verbose) {
      $file_count = count($this->linter->getFiles());
      $output = $this->outputAdapter();
      if (!empty($errors)) {
        $error_count = count($errors);
        $output->writeMessage("<error>FAILURES!</error>\n");
        $output->writeMessage("<error>Files: {$file_count}, Failures: {$error_count}</error>\n");
        foreach ($errors as $file => $error_details) {
          $this->showError($error_details);
        }
      } else {
        $output->writeMessage("<info>OK! (Files: {$file_count}, Success: {$file_count})</info>\n");
      }
    }

    return $errors;
  }

  private function showError($error, $output = null) {
    if(!$output) {
      $output = $this->outputAdapter();
    }
    $output->writeMessage("<comment>{$error['file']}:{$error['line']}</comment>\n");
    $output->writeMessage($this->getHighlightedCodeSnippet($error['file'], $error['line']));
    $output->writeMessage("<error> {$error['error']}</error>\n");
  }

  private function getHighlightedCodeSnippet($filePath, $lineNumber, $linesBefore = 3, $linesAfter = 3) {
    if (
      !class_exists('\JakubOnderka\PhpConsoleHighlighter\Highlighter') ||
      !class_exists('\JakubOnderka\PhpConsoleColor\ConsoleColor')
    ) {
      return $this->getCodeSnippet($filePath, $lineNumber, $linesBefore, $linesAfter);
    }
    $colors = new ConsoleColor();
    $highlighter = new Highlighter($colors);
    $fileContent = file_get_contents($filePath);
    
    return $highlighter->getCodeSnippet($fileContent, $lineNumber, $linesBefore, $linesAfter);
  }

  private function getCodeSnippet($filePath, $lineNumber, $linesBefore, $linesAfter) {
    $lines = file($filePath);
    $offset = $lineNumber - $linesBefore - 1;
    $offset = max($offset, 0);
    $length = $linesAfter + $linesBefore + 1;
    $lines = array_slice($lines, $offset, $length, $preserveKeys = true);
    end($lines);
    $lineStrlen = strlen(key($lines) + 1);
    $snippet = '';
    foreach ($lines as $i => $line) {
      $snippet .= (abs($lineNumber) === $i + 1 ? '  > ' : '    ');
      $snippet .= str_pad($i + 1, $lineStrlen, ' ', STR_PAD_LEFT).'| '.rtrim($line).PHP_EOL;
    }
    return $snippet;
  }
}