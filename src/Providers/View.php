<?php

namespace App\Providers;

use Exception;

class View
{
    private $data = [];

    public function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function render($templateFile)
    {
        if (!file_exists(__DIR__ . "/../Views/$templateFile.php")) {
            throw new Exception("Template file not found: $templateFile");
        }


        ob_start(); // Start output buffering

        // Include the template file
        include_once __DIR__ . "/../Views/$templateFile.php";

        $content = ob_get_clean(); // Get the buffered output and clean the buffer

        // Replace placeholders with their values
        foreach ($this->data as $key => $value) {
            $placeholder = '{' . $key . '}';

            // Check if the placeholder exists in the content before replacing
            if (strpos($content, $placeholder) !== false) {
                $content = str_replace($placeholder, (string)$value, $content);
            }
        }

        // Echo the final HTML
        echo $content;
    }

    public function get($templateFile)
    {
        if (!file_exists(__DIR__ . "/../Views/$templateFile.php")) {
            throw new Exception("Template file not found: $templateFile");
        }


        ob_start(); // Start output buffering

        // Include the template file
        include_once __DIR__ . "/../Views/$templateFile.php";

        $content = ob_get_clean(); // Get the buffered output and clean the buffer

        // Replace placeholders with their values
        foreach ($this->data as $key => $value) {
            $placeholder = '{' . $key . '}';

            // Check if the placeholder exists in the content before replacing
            if (strpos($content, $placeholder) !== false) {
                $content = str_replace($placeholder, (string)$value, $content);
            }
        }

        // Echo the final HTML
        return $content;
    }
}
