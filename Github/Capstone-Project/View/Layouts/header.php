<?php
require_once __DIR__ . '/../../Config/constants.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '' ?></title>
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>Public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>console.log('Tailwind Script Tag reached');</script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="https://www.gstatic.com/firebasejs/ui/6.0.1/firebase-ui-auth.css" />
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://www.gstatic.com/firebasejs/ui/6.0.1/firebase-ui-auth.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Leaflet Maps -->
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>Libraries/leaflet/dist/leaflet.css">
    <script src="<?= defined('BASE_URL') ? BASE_URL : '/' ?>Libraries/leaflet/dist/leaflet.js"></script>
    <script>
        const BASE_URL = '<?= defined('BASE_URL') ? BASE_URL : '/' ?>';

        // Global SweetAlert2 defaults — FixMart Green buttons on every alert
        document.addEventListener('DOMContentLoaded', function() {
            const _origFire = Swal.fire.bind(Swal);
            Swal.fire = function(...args) {
                if (typeof args[0] === 'object' && args[0] !== null) {
                    if (!args[0].confirmButtonColor) args[0].confirmButtonColor = '#10b981';
                    if (!args[0].cancelButtonColor)  args[0].cancelButtonColor  = '#6b7280';
                } else if (args.length >= 1) {
                    // Called as Swal.fire(title, text, icon) shorthand
                    const obj = { title: args[0], text: args[1], icon: args[2], confirmButtonColor: '#10b981' };
                    return _origFire(obj);
                }
                return _origFire(...args);
            };
        });
    </script>
</head>