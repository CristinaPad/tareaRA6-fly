<?php
// Tu cadena de conexión (Lo ideal es que esto venga de getenv('DATABASE_URL'))
$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
  die("Falta DATABASE_URL en Fly (Secrets).");
}

$dbConfig = parse_url($databaseUrl);

$host = $dbConfig['host'] ?? '';
$user = $dbConfig['user'] ?? '';
$pass = $dbConfig['pass'] ?? '';
$port = $dbConfig['port'] ?? 5432;
$dbname = ltrim($dbConfig['path'] ?? '', '/');

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  print "<h1>Conexión exitosa a PostgreSQL</h1>";
  print "<h2>Cristina Paduraru</h2>";

  // ✅ 1) Crear tabla si no existe
  $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id SERIAL PRIMARY KEY,
      name VARCHAR(100)
    );
  ");

  // ✅ 2) Insertar datos si está vacía (para que salga la lista como al profe)
  $count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
  if ($count === 0) {
    $pdo->exec("
      INSERT INTO users (name) VALUES ('Admin'), ('User');
    ");
  }

  // ✅ 3) Ya puedes hacer el SELECT sin error
  $stmt = $pdo->query("SELECT id, name FROM users ORDER BY id");
  $users = $stmt->fetchAll();

  print "<h3>Lista de usuarios:</h3><ul>";
  foreach ($users as $u) {
    print "<li>ID: {$u['id']} - Nombre: " . htmlspecialchars($u['name']) . "</li>";
  }
  print "</ul>";

} catch (Exception $e) {
  http_response_code(500);
  print "<h1>Error de conexión</h1>";
  print "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}



// Parseamos la URL para extraer los componentes
$dbConfig = parse_url($databaseUrl);

// Extraemos los datos necesarios
$host   = $dbConfig['host'];
$user   = $dbConfig['user'];
$pass   = $dbConfig['pass'];
$port   = $dbConfig['port'] ?? 5432; // Puerto por defecto de Postgres
$dbname = ltrim($dbConfig['path'], '/');

// Construimos el DSN (Data Source Name)
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    // Creamos la conexión PDO
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<h1>Conexión exitosa a PostgreSQL</h1>";

    // Consultamos la tabla 'users' que crea tu script init.sql
    $stmt = $pdo->query("SELECT id, name FROM users");
    $users = $stmt->fetchAll();

    if ($users) {
        echo "<h3>Cuentas de usuarios:</h3><ul>";
        foreach ($users as $user) {
            echo "<li>ID: " . $user['id'] . " - Nombre: " . htmlspecialchars($user['name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay usuarios en la tabla.</p>";
    }

} catch (PDOException $e) {
    echo "<h1>Error de conexión</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
