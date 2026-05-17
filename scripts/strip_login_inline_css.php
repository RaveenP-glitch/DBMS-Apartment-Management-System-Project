<?php
$files = [
    __DIR__ . '/../Employee/employeelogin.php' => '../assets/css/login.css',
    __DIR__ . '/../Tenant/tenant_login.php' => '../assets/css/login.css',
];

$headLinks = <<<'HTML'
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="%s" />
HTML;

foreach ($files as $path => $cssHref) {
    $content = file_get_contents($path);
    $content = preg_replace('/\s*<style>.*?<\/style>/s', "\n" . sprintf($headLinks, $cssHref), $content, 1);
    file_put_contents($path, $content);
    echo "Updated: $path\n";
}

$loginHtml = __DIR__ . '/../login.html';
$html = file_get_contents($loginHtml);
$html = preg_replace(
    '/\s*<link rel="stylesheet" href="\.\/assets\/css\/styles\.css" \/>\s*<style>.*?<\/style>/s',
    "\n    <link href=\"https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css\" rel=\"stylesheet\" />\n    <link rel=\"stylesheet\" href=\"./assets/css/login.css\" />",
    $html,
    1
);
$html = str_replace('href="admin/admin_login.php"', 'href="Admin/admin_login.php"', $html);
$html = str_replace('href="employee/employeelogin.php"', 'href="Employee/employeelogin.php"', $html);
$html = str_replace('href="tenant/tenant_login.php"', 'href="Tenant/tenant_login.php"', $html);
file_put_contents($loginHtml, $html);
echo "Updated: login.html\n";
