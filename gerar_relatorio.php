<?php
require_once 'functions.php';
requireLogin();

// Permissão para ver o relatório
if (!can('admin_sistema') && !can('ver_logs')) {
    die('Acesso negado.');
}

$tipo = $_GET['tipo'] ?? '';
$formato = $_GET['formato'] ?? 'pdf';
$pid = current_paroquia_id();

$data = [];
$columns = [];
$title = "";

if ($tipo === 'eventos') {
    $title = "Relatório de Eventos";
    $dtInic = $_GET['data_inicio'] ?? '';
    $dtFim = $_GET['data_fim'] ?? '';
    
    $where = ["a.paroquia_id = $pid"];
    if (!empty($dtInic)) $where[] = "a.data_inicio >= '" . $conn->real_escape_string($dtInic) . "'";
    if (!empty($dtFim)) $where[] = "a.data_inicio <= '" . $conn->real_escape_string($dtFim) . "'";
    
    $sql = "
        SELECT a.id, a.nome, a.data_inicio, a.hora_inicio, l.nome_local,
               (SELECT COUNT(*) FROM inscricoes i WHERE i.atividade_id = a.id) as inscritos
        FROM atividades a
        LEFT JOIN locais_paroquia l ON a.local_id = l.id
        WHERE " . implode(" AND ", $where) . "
        ORDER BY a.data_inicio ASC
    ";
    $res = $conn->query($sql);
    
    $columns = ['ID', 'Evento', 'Data', 'Hora', 'Local', 'Inscritos'];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = [
                $row['id'],
                $row['nome'],
                formatDate($row['data_inicio']),
                formatTime($row['hora_inicio']),
                $row['nome_local'] ?: 'Não definido',
                $row['inscritos']
            ];
        }
    }
} elseif ($tipo === 'contatos') {
    $title = "Relatório de Contatos";
    $status = $_GET['status'] ?? 'todos';
    
    $where = ["paroquia_id = $pid"];
    if ($status === '1') $where[] = "ativo = 1";
    if ($status === '0') $where[] = "ativo = 0";
    
    $sql = "
        SELECT id, nome, email, telefone, sexo, date_format(data_nascimento, '%d/%m/%Y') as dtnasc, ativo 
        FROM usuarios 
        WHERE " . implode(" AND ", $where) . "
        ORDER BY nome ASC
    ";
    $res = $conn->query($sql);
    
    $reqCols = $_GET['cols'] ?? [];
    $columns = ['ID'];
    if (in_array('Nome', $reqCols)) $columns[] = 'Nome';
    if (in_array('Email', $reqCols)) $columns[] = 'E-mail';
    if (in_array('Telefone', $reqCols)) $columns[] = 'Telefone';
    if (in_array('Sexo', $reqCols)) $columns[] = 'Sexo';
    if (in_array('Nascimento', $reqCols)) $columns[] = 'Nascimento';
    if (in_array('Status', $reqCols)) $columns[] = 'Status';

    if (empty($reqCols)) $columns = ['ID', 'Nome', 'E-mail', 'Telefone', 'Sexo', 'Nascimento', 'Status']; // fallback
    
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $rowData = [$row['id']];
            if (in_array('Nome', $reqCols) || empty($reqCols)) $rowData[] = $row['nome'];
            if (in_array('Email', $reqCols) || empty($reqCols)) $rowData[] = $row['email'];
            if (in_array('Telefone', $reqCols) || empty($reqCols)) $rowData[] = $row['telefone'];
            if (in_array('Sexo', $reqCols) || empty($reqCols)) $rowData[] = ($row['sexo'] === 'M' ? 'Masculino' : ($row['sexo'] === 'F' ? 'Feminino' : '-'));
            if (in_array('Nascimento', $reqCols) || empty($reqCols)) $rowData[] = $row['dtnasc'] ?: '-';
            if (in_array('Status', $reqCols) || empty($reqCols)) $rowData[] = (int)$row['ativo'] === 1 ? 'Ativo' : 'Inativo';
            
            $data[] = $rowData;
        }
    }
} else {
    die("Tipo de relatório inválido.");
}

$filename = "Relatorio_" . ucfirst($tipo) . "_" . date('Ymd_His');

if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    $output = fopen('php://output', 'w');
    // BOM for Excel
    fputs($output, "\xEF\xBB\xBF");
    fputcsv($output, $columns, ';');
    foreach ($data as $row) {
        fputcsv($output, $row, ';');
    }
    fclose($output);
    exit();
}

$html = '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">';
if ($formato === 'pdf') {
    $html .= '<title>' . $title . '</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 2rem; }
        h1 { font-size: 24px; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .meta { font-size: 12px; color: #666; margin-bottom: 20px; }
        @media print { body { margin: 0; } }
    </style>
    </head><body onload="window.print()">';
} else {
    // DOC or XLS layout mapping
    $html .= '<meta http-equiv="content-type" content="application/vnd.ms-' . ($formato === 'xls' ? 'excel' : 'word') . '; charset=UTF-8">';
    $html .= '</head><body>';
}

$html .= "<h1>{$title}</h1>";
$html .= "<div class='meta'>Gerado em: " . date('d/m/Y H:i') . "</div>";

$html .= "<table><thead><tr>";
foreach ($columns as $col) {
    if ($formato === 'pdf' || $formato === 'xls' || $formato === 'doc') {
        $html .= "<th>" . htmlspecialchars($col) . "</th>";
    }
}
$html .= "</tr></thead><tbody>";

foreach ($data as $row) {
    $html .= "<tr>";
    foreach ($row as $cell) {
        $html .= "<td>" . htmlspecialchars($cell ?? '') . "</td>";
    }
    $html .= "</tr>";
}
$html .= "</tbody></table></body></html>";

if ($formato === 'xls') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename={$filename}.xls");
    echo $html;
    exit();
} elseif ($formato === 'doc') {
    header("Content-Type: application/vnd.ms-word; charset=utf-8");
    header("Content-Disposition: attachment; filename={$filename}.doc");
    echo $html;
    exit();
} elseif ($formato === 'pdf') {
    echo $html;
    exit();
}
