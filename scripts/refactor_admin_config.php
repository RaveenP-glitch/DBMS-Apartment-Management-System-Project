<?php
/**
 * One-off: replace inline mysqli blocks in Admin/*.php with config.php
 */
$adminDir = dirname(__DIR__) . '/Admin';
$pattern = '/\<\?php\s*\n\$servername = "127\.0\.0\.1";\s*\n\$username = "root";\s*\n\$password = "";\s*\n\$database = "apartment_management";\s*\n\s*\n\$conn = new mysqli\(\$servername, \$username, \$password, \$database\);\s*\n\s*if \(\$conn->connect_error\) \{\s*\n\s*die\("Connection failed: " \. \$conn->connect_error\);\s*\n\}/s';

$replacement = "<?php\nrequire_once __DIR__ . '/../config.php';";

$skip = ['admin_login.php', 'admin_dashboard.php', 'deny.php'];

foreach (glob($adminDir . '/*.php') as $file) {
    $base = basename($file);
    if (in_array($base, $skip, true)) {
        continue;
    }
    $content = file_get_contents($file);
    $new = preg_replace($pattern, $replacement, $content, 1, $count);
    if ($count > 0) {
        file_put_contents($file, $new);
        echo "Updated: $base\n";
    }
}

// statistics.php uses $dbname variant
$stats = $adminDir . '/statistics.php';
if (is_file($stats)) {
    $c = file_get_contents($stats);
    $c = preg_replace(
        '/\<\?php\s*\/\/ Connect.*?\$conn = new mysqli\(\$servername, \$username, \$password, \$dbname\);\s*if \(\$conn->connect_error\) \{\s*die\("Connection failed: " \. \$conn->connect_error\);\s*\}/s',
        "<?php\nrequire_once __DIR__ . '/../config.php';",
        $c,
        1,
        $count
    );
    if ($count) {
        file_put_contents($stats, $c);
        echo "Updated: statistics.php\n";
    }
}

echo "Done.\n";
