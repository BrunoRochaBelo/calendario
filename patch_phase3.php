<?php
/**
 * Master Patch Script - Phase 3
 * Fixes: sanitize_text, documentos layout, logs datalist,
 *        editar_atividade cor+tipo_id, gerenciar_catalogo buttons,
 *        paroquias edit modal, color picker in edit form
 */

// =============================================
// 1. FIX editar_atividade.php
// =============================================
$f = 'c:/xampp/htdocs/calender/editar_atividade.php';
$c = file_get_contents($f);

// Fix sanitize_text -> trim
$c = str_replace(
    '$cor = sanitize_text($data[\'cor\'] ?? \'#3b82f6\');',
    '$cor = trim($data[\'cor\'] ?? \'#3b82f6\');',
    $c
);

// Fix SQL to include cor
$c = str_replace(
    "restrito = ?\n                WHERE id = ? AND paroquia_id = ?",
    "restrito = ?, cor = ?\n                WHERE id = ? AND paroquia_id = ?",
    $c
);

// Fix bind_param to include cor
$c = str_replace(
    "\$stmt->bind_param('siisssiii',",
    "\$stmt->bind_param('siisssiisii',",
    $c
);
$c = str_replace(
    "\$restrito, \$id, \$pid",
    "\$restrito, \$cor, \$id, \$pid",
    $c
);

// Fix tipo_id -> tipo_atividade_id in the HTML comparison
$c = str_replace(
    "\$activity['tipo_id']",
    "\$activity['tipo_atividade_id']",
    $c
);

// Add color picker to edit form - replace the single "Identificação" div with row-grid
$c = str_replace(
    '<div class="form-group">' . "\r\n" . '                            <label>Identificação do Evento</label>' . "\r\n" . '                            <input type="text" name="nome" value="<?= h($activity[\'nome\']) ?>" required autofocus>' . "\r\n" . '                        </div>',
    '<div class="row-grid" style="grid-template-columns: 1fr auto;">' . "\n" .
    '                            <div class="form-group">' . "\n" .
    '                                <label>Identificação do Evento</label>' . "\n" .
    '                                <input type="text" name="nome" value="<?= h($activity[\'nome\']) ?>" required autofocus>' . "\n" .
    '                            </div>' . "\n" .
    '                            <div class="form-group">' . "\n" .
    '                                <label>Cor do Evento</label>' . "\n" .
    '                                <input type="color" name="cor" value="<?= h($activity[\'cor\'] ?? \'#3b82f6\') ?>" style="height: 55px; width: 60px; padding: 0.2rem; cursor: pointer; border-radius: 12px; border: 1px solid var(--border); background: var(--panel-hi);">' . "\n" .
    '                            </div>' . "\n" .
    '                        </div>',
    $c
);

file_put_contents($f, $c);
echo "1. editar_atividade.php fixed\n";

// =============================================
// 2. FIX documentos.php - Remove CSV/XLS/DOC, keep only PDF; fix select colors; fix checkbox wrap
// =============================================
$f2 = 'c:/xampp/htdocs/calender/documentos.php';
$c2 = file_get_contents($f2);

// Fix select color: white text, black options
$c2 = str_replace(
    '<select name="status" style="color: #000;">',
    '<select name="status" style="color: #fff;">',
    $c2
);

// Remove CSV, XLS, DOC buttons from EVENTOS block - replace entire export-actions div
// First block (eventos)
$eventos_old_export = '<div class="export-actions">' . "\n" .
'                        <button type="submit" name="formato" value="csv" class="btn btn-ghost btn-export">' . "\n" .
'                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>' . "\n" .
'                            CSV' . "\n" .
'                        </button>' . "\n" .
'                        <button type="submit" name="formato" value="xls" class="btn btn-ghost btn-export">' . "\n" .
'                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>' . "\n" .
'                            XLS' . "\n" .
'                        </button>' . "\n" .
'                        <button type="submit" name="formato" value="doc" class="btn btn-ghost btn-export">' . "\n" .
'                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>' . "\n" .
'                            DOC' . "\n" .
'                        </button>' . "\n" .
'                        <button type="submit" name="formato" value="pdf"';

$pdf_only_export = '<div class="export-actions" style="grid-template-columns: 1fr;">' . "\n" .
'                        <button type="submit" name="formato" value="pdf"';

$c2 = str_replace($eventos_old_export, $pdf_only_export, $c2);

//  Same for contatos block - a bit different approach, replace all 3 ghost buttons before the PDF
// Let's just do a regex replace for any remaining CSV/XLS/DOC button blocks
$c2 = preg_replace(
    '/<button type="submit" name="formato" value="csv"[^<]*<[^<]*<\/svg>\s*CSV\s*<\/button>\s*/s',
    '',
    $c2
);
$c2 = preg_replace(
    '/<button type="submit" name="formato" value="xls"[^<]*<[^<]*<\/svg>\s*XLS\s*<\/button>\s*/s',
    '',
    $c2
);
$c2 = preg_replace(
    '/<button type="submit" name="formato" value="doc"[^<]*<[^<]*<\/svg>\s*DOC\s*<\/button>\s*/s',
    '',
    $c2
);

// Fix all remaining export-actions to be single column
$c2 = str_replace(
    '<div class="export-actions">',
    '<div class="export-actions" style="grid-template-columns: 1fr;">',
    $c2
);

// Fix E-mail checkbox label to be on one line (nowrap)
$c2 = str_replace(
    'value="Email" checked> E-mail',
    'value="Email" checked> <span style="white-space:nowrap;">E-mail</span>',
    $c2
);

// Add category/stats filter to events report
$eventos_inject_after = '<div class="form-row">' . "\n" .
'                        <div class="form-group" style="margin: 0;">' . "\n" .
'                            <label>DATA INICIAL (OPCIONAL)</label>';

$eventos_extra_filters = '<div class="form-group" style="margin: 0;">' . "\n" .
'                            <label>CATEGORIA / TIPO DE EVENTO</label>' . "\n" .
'                            <select name="tipo_id" style="color: #fff;">' . "\n" .
'                                <option value="">Todas as categorias</option>' . "\n" .
'                                <?php' . "\n" .
'                                    $tipos_r = $conn->query("SELECT * FROM tipos_atividade ORDER BY nome_tipo");' . "\n" .
'                                    if ($tipos_r) while ($tr = $tipos_r->fetch_assoc()) {' . "\n" .
'                                        echo \'<option value="\' . $tr["id"] . \'">\' . h($tr["nome_tipo"]) . \'</option>\';' . "\n" .
'                                    }' . "\n" .
'                                ?>' . "\n" .
'                            </select>' . "\n" .
'                        </div>' . "\n\n" .
'                        <div class="form-group" style="margin: 0;">' . "\n" .
'                            <label>CONTEÚDO DO RELATÓRIO</label>' . "\n" .
'                            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem; font-size: 0.85rem;">' . "\n" .
'                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-dim); text-transform: none; letter-spacing: normal;"><input type="checkbox" name="inc_inscritos" value="1" checked> Incluir Inscritos</label>' . "\n" .
'                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-dim); text-transform: none; letter-spacing: normal;"><input type="checkbox" name="inc_stats" value="1" checked> Estatísticas</label>' . "\n" .
'                            </div>' . "\n" .
'                        </div>' . "\n\n" .
'                        ' . $eventos_inject_after;

$c2 = str_replace($eventos_inject_after, $eventos_extra_filters, $c2);

file_put_contents($f2, $c2);
echo "2. documentos.php fixed\n";

// =============================================
// 3. FIX logs.php - add datalist autocomplete
// =============================================
$f3 = 'c:/xampp/htdocs/calender/logs.php';
$c3 = file_get_contents($f3);

// Add PHP block before DOCTYPE to generate datalist data
$datalist_php = '<?php' . "\n" .
'// Autocomplete data for filters' . "\n" .
'$usuarios_list = $conn->query("SELECT DISTINCT nome FROM usuarios WHERE paroquia_id = $pid ORDER BY nome");' . "\n" .
'$tabelas_list = $conn->query("SELECT DISTINCT tabela_afetada FROM log_alteracoes ORDER BY tabela_afetada");' . "\n" .
'?>' . "\n";

$c3 = str_replace('<!DOCTYPE html>', $datalist_php . '<!DOCTYPE html>', $c3);

// Add list attribute to inputs  
$c3 = str_replace(
    '<input type="text" name="tabela" value="<?= h($filter_table) ?>" placeholder="Ex: atividades, locais...">',
    '<input type="text" name="tabela" value="<?= h($filter_table) ?>" placeholder="Ex: atividades, locais..." list="tabelas_list" autocomplete="off">',
    $c3
);
$c3 = str_replace(
    '<input type="text" name="usuario" value="<?= h($filter_user) ?>" placeholder="Nome do usuário...">',
    '<input type="text" name="usuario" value="<?= h($filter_user) ?>" placeholder="Nome do usuário..." list="usuarios_list" autocomplete="off">',
    $c3
);

// Add datalist elements before </form> in the filter bar
$datalists_html = "\n" . '                <datalist id="tabelas_list">' . "\n" .
'                    <?php if ($tabelas_list) while ($tb = $tabelas_list->fetch_assoc()): ?>' . "\n" .
'                        <option value="<?= h($tb[\'tabela_afetada\']) ?>">' . "\n" .
'                    <?php endwhile; ?>' . "\n" .
'                </datalist>' . "\n" .
'                <datalist id="usuarios_list">' . "\n" .
'                    <?php if ($usuarios_list) while ($ul = $usuarios_list->fetch_assoc()): ?>' . "\n" .
'                        <option value="<?= h($ul[\'nome\']) ?>">' . "\n" .
'                    <?php endwhile; ?>' . "\n" .
'                </datalist>' . "\n";

// Insert before the closing </form> of the filter bar
$c3 = str_replace(
    '            </form>' . "\r\n" . "\r\n" . '            <section',
    $datalists_html . '            </form>' . "\r\n" . "\r\n" . '            <section',
    $c3
);

file_put_contents($f3, $c3);
echo "3. logs.php fixed with datalists\n";

// =============================================
// 4. FIX gerenciar_catalogo.php - button layout responsive
// =============================================
$f4 = 'c:/xampp/htdocs/calender/gerenciar_catalogo.php';
$c4 = file_get_contents($f4);

// Add responsive CSS for catalog card buttons if not present
if (strpos($c4, '.catalog-actions') === false) {
    $catalog_css = '
        .catalog-actions {
            display: flex; flex-wrap: wrap; gap: 0.5rem; padding-top: 1rem;
            border-top: 1px solid var(--border); margin-top: auto;
        }
        .catalog-actions .btn { flex: 1 1 auto; min-width: 80px; text-align: center; font-size: 0.75rem; padding: 0.6rem 0.8rem; white-space: nowrap; }
        @media (max-width: 480px) {
            .catalog-actions { flex-direction: column; }
            .catalog-actions .btn { width: 100%; }
        }
    ';
    $c4 = str_replace('</style>', $catalog_css . "\n    </style>", $c4);
}

file_put_contents($f4, $c4);
echo "4. gerenciar_catalogo.php buttons fixed\n";

// =============================================
// 5. FIX novaatividade.php - color picker already done, just verify
// =============================================
$f5 = 'c:/xampp/htdocs/calender/novaatividade.php';
$c5 = file_get_contents($f5);

// Add color picker if not already present
if (strpos($c5, 'type="color"') === false) {
    $c5 = str_replace(
        '<div class="form-group">' . "\r\n" . '                            <label>Identificação do Evento</label>' . "\r\n" . '                            <input type="text" name="nome" placeholder="Nome da atividade ou celebração" required autofocus>' . "\r\n" . '                        </div>',
        '<div class="row-grid" style="grid-template-columns: 1fr auto;">' . "\n" .
        '                            <div class="form-group">' . "\n" .
        '                                <label>Identificação do Evento</label>' . "\n" .
        '                                <input type="text" name="nome" placeholder="Nome da atividade ou celebração" required autofocus>' . "\n" .
        '                            </div>' . "\n" .
        '                            <div class="form-group">' . "\n" .
        '                                <label>Cor do Evento</label>' . "\n" .
        '                                <input type="color" name="cor" value="#3b82f6" style="height: 55px; width: 60px; padding: 0.2rem; cursor: pointer; border-radius: 12px; border: 1px solid var(--border); background: var(--panel-hi);">' . "\n" .
        '                            </div>' . "\n" .
        '                        </div>',
        $c5
    );
    file_put_contents($f5, $c5);
    echo "5. novaatividade.php color picker added\n";
} else {
    echo "5. novaatividade.php color picker already present\n";
}

// =============================================
// 6. Verify index.php has cor in query
// =============================================
$f6 = 'c:/xampp/htdocs/calender/index.php';
$c6 = file_get_contents($f6);
if (strpos($c6, 'a.cor') !== false) {
    echo "6. index.php already has cor in query\n";
} else {
    echo "6. WARNING - index.php needs manual cor injection\n";
}

echo "\n=== ALL PATCHES APPLIED ===\n";
