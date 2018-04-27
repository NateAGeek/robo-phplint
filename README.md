# Installation
Require the repo via `composer require nateageek/robo-phplint`
# Usage
```php
<?php

class RoboFile extends \Robo\Tasks {

  use NateAGeek\Robo\Task\PHPLint\Tasks;

  function testLint() {
    //This will run phplint on all *.php files in the directory /phplint-code 
   // Also will return any errors found as an array.
    $this->taskPHPLintTask("/phplint-code")
      ->run();
  }
}
```