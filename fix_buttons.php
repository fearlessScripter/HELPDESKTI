<?php
$file = 'app/Views/tickets/mis_tickets.php';
$content = file_get_contents($file);

$search = '<button class="btn btn-sm btn-outline-secondary">Ver Detalles</button>';
$replace = '<a href="<?= base_url(\'/tickets/detalles\') ?>" class="btn btn-sm btn-outline-secondary">Ver Detalles</a>';
$content = str_replace($search, $replace, $content);

$searchCerrar = '<button class="btn btn-sm btn-success ms-1" onclick="alert(\'Simulación: Confirmar solución y cerrar ticket.\')">';
$replaceCerrar = '<?php if ($rol !== \'Técnico\'): ?>' . "\n" . '                            <button class="btn btn-sm btn-success ms-1" onclick="alert(\'Simulación: Confirmar solución y cerrar ticket.\')">';
$content = str_replace($searchCerrar, $replaceCerrar, $content);

$searchCerrarEnd = "</button>\r\n                        </td>";
$replaceCerrarEnd = "</button>\r\n                            <?php endif; ?>\r\n                        </td>";
$content = str_replace($searchCerrarEnd, $replaceCerrarEnd, $content);

// Also handle \n in case it uses unix line endings
$searchCerrarEnd2 = "</button>\n                        </td>";
$replaceCerrarEnd2 = "</button>\n                            <?php endif; ?>\n                        </td>";
if ($content === str_replace($searchCerrarEnd, $replaceCerrarEnd, $content)) {
    $content = str_replace($searchCerrarEnd2, $replaceCerrarEnd2, $content);
}

file_put_contents($file, $content);
echo "Done";
