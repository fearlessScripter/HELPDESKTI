<?php
$files = [
    'app/Views/dashboard/admin.php',
    'app/Views/tickets/asignados.php',
    'app/Views/tickets/cola.php',
    'app/Views/tickets/mis_tickets.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Add endif if it's missing
    $search = "</a>\r\n            </div>";
    $replace = "</a>\r\n                <?php endif; ?>\r\n            </div>";
    
    $search2 = "</a>\n            </div>";
    $replace2 = "</a>\n                <?php endif; ?>\n            </div>";
    
    $newContent = str_replace($search, $replace, $content);
    if ($newContent === $content) {
        $newContent = str_replace($search2, $replace2, $content);
    }
    
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Fixed $file\n";
    }
}
