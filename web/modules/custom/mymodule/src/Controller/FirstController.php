<?php

/**
 * @file
 * Generate markup to be displayed. Functionality in this Controller is
 * wired to Drupal in mymodule.yml
 */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase {
    public function simpleContent() {

        return [
            '#type' => 'markup',
            '#markup' => t(
                "Hello Drupal world.Time flies like an arrow, fruit flies like a banana"
            ),
        ];

    }

    public function variableContent($name_1, $name_2) {

        return[
          '#type' => 'markup',
          '#markup' => t('@name1 and @name2 say helo to me',
            ['@name1' => $name_1, '@name2' => $name_2]),
        ];

    }

}
