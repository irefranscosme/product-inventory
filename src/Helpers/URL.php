<?php
namespace App\Helpers;

class URL {
    public function fullUrl() {
        // Get the protocol (http or https)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

        // Get the domain name
        $domain = $_SERVER['HTTP_HOST'];

        // Full URL
        $fullUrl = $protocol . '://' . $domain;

        return $fullUrl;
    }
}