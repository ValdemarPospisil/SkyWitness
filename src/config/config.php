<?php
// Database configuration
const DB_HOST = 'db'; // nebo getenv('DB_HOST') ?: 'db';
const DB_NAME = 'skywitness';
const DB_USER = 'postgres';
const DB_PASSWORD = 'postgres';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');