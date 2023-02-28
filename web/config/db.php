<?php

// 1. conexion pdo - dsn

$conn = new PDO("pgsql:host=localhost;port=5432;dbname=postgres", "postgres", "curso");