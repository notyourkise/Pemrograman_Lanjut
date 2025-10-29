<?php

/**
 * Week 8: URL Helper
 * Helper functions for generating URLs
 */

/**
 * Get base URL from config
 * 
 * @return string
 */
function base_url()
{
    static $baseUrl = null;
    
    if ($baseUrl === null) {
        $config = require __DIR__ . '/../config.php';
        $baseUrl = $config['app']['base_url'];
    }
    
    return $baseUrl;
}

/**
 * Generate URL with base path
 * 
 * @param string $path
 * @return string
 */
function url($path = '')
{
    $path = ltrim($path, '/');
    return base_url() . '/' . $path;
}

/**
 * Generate asset URL
 * 
 * @param string $path
 * @return string
 */
function asset($path)
{
    $path = ltrim($path, '/');
    return base_url() . '/' . $path;
}

/**
 * Redirect to URL
 * 
 * @param string $path
 */
function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}
