<?php
$f = 'c:/xampp/htdocs/calender/novaatividade.php';
$c = file_get_contents($f);
$c = str_replace(
    '<div class="form-group">' . "\r\n" . '                            <label>Identificação do Evento</label>' . "\r\n" . '                            <input type="text" name="nome" placeholder="Nome da atividade ou celebração" required autofocus>' . "\r\n" . '                        </div>',
    '<div class="row-grid" style="grid-template-columns: 1fr auto;">' . "\n" . '                            <div class="form-group">' . "\n" . '                                <label>Identificação do Evento</label>' . "\n" . '                                <input type="text" name="nome" placeholder="Nome da atividade ou celebração" required autofocus>' . "\n" . '                            </div>' . "\n" . '                            <div class="form-group">' . "\n" . '                                <label>Cor do Evento</label>' . "\n" . '                                <input type="color" name="cor" value="#3b82f6" style="height: 55px; width: 60px; padding: 0.2rem; cursor: pointer; border-radius: 12px; border: 1px solid var(--border); background: var(--panel-hi);" title="Cor no Calendário">' . "\n" . '                            </div>' . "\n" . '                        </div>',
    $c
);
file_put_contents($f, $c);

// For editar_atividade.php
$f2 = 'c:/xampp/htdocs/calender/editar_atividade.php';
$c2 = file_get_contents($f2);
// First fix POST processing
$c2 = preg_replace('/(\$tipo = .+;)/', "$1\n        \$cor = sanitize_text(\$data['cor'] ?? '#3b82f6');", $c2);
$c2 = str_replace(
    'restrito = ? WHERE',
    'restrito = ?, cor = ? WHERE',
    $c2
);
$c2 = str_replace(
    "\$stmt->bind_param('siisssiiii',",
    "\$stmt->bind_param('siisssiiisi',",
    $c2
);
$c2 = str_replace(
    "\$data['hora_inicio'], \$uid, \$restrito, \$id, \$pid);",
    "\$data['hora_inicio'], \$uid, \$restrito, \$cor, \$id, \$pid);",
    $c2
);

// HTML form
$c2 = str_replace(
    '<div class="form-group">' . "\n" . '                            <label>Identificação do Evento</label>',
    '<div class="row-grid" style="grid-template-columns: 1fr auto;">' . "\n" . '                            <div class="form-group">' . "\n" . '                                <label>Identificação do Evento</label>',
    $c2
);
$c2 = str_replace(
    '<input type="text" name="nome" value="<?= h($ev[\'nome\']) ?>" required autofocus>' . "\n" . '                        </div>',
    '<input type="text" name="nome" value="<?= h($ev[\'nome\']) ?>" required autofocus>' . "\n" . '                            </div>' . "\n" . '                            <div class="form-group">' . "\n" . '                                <label>Cor do Evento</label>' . "\n" . '                                <input type="color" name="cor" value="<?= h($ev[\'cor\'] ?? \'#3b82f6\') ?>" style="height: 55px; width: 60px; padding: 0.2rem; cursor: pointer; border-radius: 12px; border: 1px solid var(--border); background: var(--panel-hi);" title="Cor no Calendário">' . "\n" . '                            </div>' . "\n" . '                        </div>',
    $c2
);

file_put_contents($f2, $c2);

// Check index.php for the dot color
$f3 = 'c:/xampp/htdocs/calender/index.php';
$c3 = file_get_contents($f3);
$c3 = str_replace(
    "a.id, a.nome, a.data_inicio, a.hora_inicio, a.restrito",
    "a.id, a.nome, a.data_inicio, a.hora_inicio, a.restrito, a.cor",
    $c3
);
$c3 = str_replace(
    "style=\"color: var(--primary); margin-right: 2px;\"",
    "style=\"color: <?= htmlspecialchars(\$act['cor'] ?? 'var(--primary)') ?>; margin-right: 2px;\"",
    $c3
);
file_put_contents($f3, $c3);
echo "Colors injected!";
