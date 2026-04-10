<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Parish Calendar (v2.4.4 Ultra Premium)
 * High Performance · Glassmorphism · Dynamic Enrollment
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();
ensureInscricoesTable($conn);
ensureUserPhotoColumn($conn);
ensureEventActivitiesStructure($conn);

$pid = current_paroquia_id();
$canInteractActivities = canInteractWithActivity();
$msg = $_GET['msg'] ?? '';
$autoRefresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';

// 1. Date Navigation Logic
$month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$year  = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');

// Adjust for overflow/underflow
if ($month > 12) { $month = 1; $year++; }
if ($month < 1) { $month = 12; $year--; }

$dt = new DateTime("$year-$month-01");
$monthNames = [
    1 => 'JANEIRO', 2 => 'FEVEREIRO', 3 => 'MARÇO', 4 => 'ABRIL',
    5 => 'MAIO', 6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO',
    9 => 'SETEMBRO', 10 => 'OUTUBRO', 11 => 'NOVEMBRO', 12 => 'DEZEMBRO'
];
$displayMonth = $monthNames[$month];

// 2. Calendar Metadata
$daysInMonth = (int)$dt->format('t');
$firstDayOfWeek = (int)$dt->format('w'); // 0 (Sun) to 6 (Sat)
$today = date('Y-m-d');

// 3. Fetch Activities for the Month
$startMonth = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
$endMonth = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . $daysInMonth;

$sql = "
    SELECT
        a.*,
        t.cor,
        t.icone,
        (
            SELECT CONCAT(u.nome, '||', COALESCE(u.foto_perfil, ''))
            FROM inscricoes i_pre
            INNER JOIN usuarios u ON u.id = i_pre.usuario_id
            WHERE i_pre.atividade_id = a.id
            ORDER BY i_pre.data_inscricao ASC
            LIMIT 1
        ) AS primeiro_inscrito,
        (
            SELECT COUNT(*)
            FROM inscricoes i
            WHERE i.atividade_id = a.id
        ) + (
            SELECT COUNT(*)
            FROM atividade_evento_inscricoes aei
            INNER JOIN atividade_evento_itens ei ON ei.id = aei.evento_item_id
            WHERE ei.evento_id = a.id
        ) AS total_inscritos
    FROM atividades a
    LEFT JOIN tipos_atividade t ON a.tipo_atividade_id = t.id
    WHERE a.paroquia_id = ? 
      AND a.data_inicio BETWEEN ? AND ?
      AND (a.restrito = 0 OR ? = 1 OR a.criador_id = ?)
    ORDER BY a.hora_inicio ASC
";

$userId = (int)($_SESSION['usuario_id'] ?? 0);
$canVerRestritos = (int)can('ver_restritos');

$stmt = $conn->prepare($sql);
$stmt->bind_param('issii', $pid, $startMonth, $endMonth, $canVerRestritos, $userId);
$stmt->execute();
$res = $stmt->get_result();

$activitiesByDay = [];
while ($row = $res->fetch_assoc()) {
    $row['primeiro_inscrito_nome'] = '';
    $row['primeiro_inscrito_foto'] = '';
    $previewRaw = trim((string)($row['primeiro_inscrito'] ?? ''));
    if ($previewRaw !== '') {
        $parts = explode('||', $previewRaw, 2);
        $pname = trim((string)($parts[0] ?? ''));
        $pphoto = trim((string)($parts[1] ?? ''));
        if ($pphoto !== '' && !file_exists(__DIR__ . '/' . $pphoto)) {
            $pphoto = '';
        }
        $row['primeiro_inscrito_nome'] = $pname;
        $row['primeiro_inscrito_foto'] = $pphoto;
    }
    $day = (int)date('d', strtotime($row['data_inicio']));
    $activitiesByDay[$day][] = $row;
}

// 4. Fetch Birthdays
$sqlB = "SELECT nome, data_nascimento, foto_perfil FROM usuarios WHERE paroquia_id = ? AND MONTH(data_nascimento) = ? AND ativo = 1";
$stmtB = $conn->prepare($sqlB);
$stmtB->bind_param('ii', $pid, $month);
$stmtB->execute();
$resB = $stmtB->get_result();
while ($u = $resB->fetch_assoc()) {
    $birthYear = (int)date('Y', strtotime($u['data_nascimento']));
    if ($year < $birthYear) {
        continue; // só mostra a partir do ano de nascimento
    }
    $day = (int)date('d', strtotime($u['data_nascimento']));
    if ($day < 1 || $day > $daysInMonth) {
        continue; // ex: 29/02 em ano não-bissexto
    }
    $nameParts = preg_split('/\s+/', trim((string)$u['nome'])) ?: [];
    $firstName = $nameParts[0] ?? '';
    $lastName = count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '';
    $displayName = trim($firstName . ' ' . $lastName);
    if ($displayName === '') {
        $displayName = $firstName;
    }
    $shortName = mb_substr($displayName, 0, 12);
    $bdayAct = [
        'is_birthday' => true,
        'nome' => "Aniv. {$shortName}",
        'foto_perfil' => (string)($u['foto_perfil'] ?? '')
    ];
    if (!isset($activitiesByDay[$day])) $activitiesByDay[$day] = [];
    array_unshift($activitiesByDay[$day], $bdayAct);
}

// 5. Catholic Holidays
$holidays = [
    '01-01' => 'Santa Maria',
    '10-12' => 'Nossa Sra. Aparecida',
    '11-02' => 'Finados',
    '12-25' => 'Natal do Senhor',
];

if (function_exists('easter_days')) {
    $easterDays = easter_days($year);
    $easterTimestamp = strtotime("$year-03-21 +$easterDays days");
    $holidays[date('m-d', $easterTimestamp)] = 'Páscoa';
    $holidays[date('m-d', strtotime('-2 days', $easterTimestamp))] = 'Sexta-feira Santa';
    $holidays[date('m-d', strtotime('+60 days', $easterTimestamp))] = 'Corpus Christi';
    $holidays[date('m-d', strtotime('-7 days', $easterTimestamp))] = 'Domingo de Ramos';
}

foreach ($holidays as $mmdd => $hName) {
    list($hM, $hD) = explode('-', $mmdd);
    if ((int)$hM === $month) {
        $day = (int)$hD;
        $hAct = [
            'is_holiday' => true,
            'nome' => $hName
        ];
        if (!isset($activitiesByDay[$day])) $activitiesByDay[$day] = [];
        array_unshift($activitiesByDay[$day], $hAct);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Calendário Paroquial – PASCOM</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .app-shell { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 1.5rem; display: flex; flex-direction: column; min-height: 100vh; transition: margin 0.3s; }
        
        /* Premium Scrollbar for Main Page */
        body::-webkit-scrollbar { width: 10px; }
        body::-webkit-scrollbar-track { background: var(--bg-darker); }
        body::-webkit-scrollbar-thumb { background: var(--panel-hi); border-radius: 10px; border: 2px solid var(--bg-darker); }
        body::-webkit-scrollbar-thumb:hover { background: var(--primary); }
        body { scrollbar-width: thin; scrollbar-color: var(--panel-hi) var(--bg-darker); }

        
        /* Premium Calendar UI */
        .calendar-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .month-display { display: flex; align-items: baseline; gap: 1rem; }
        .month-display h1 { 
            font-size: clamp(1.5rem, 4vw, 2.5rem); color: var(--primary); font-weight: 950; 
            letter-spacing: -0.05em; margin: 0; line-height: 0.8;
            text-transform: uppercase;
        }
        .year-label { font-size: clamp(1.2rem, 2.5vw, 2rem); color: var(--text); font-weight: 900; opacity: 0.9; }
        
        .context-msg {
            background: rgba(var(--primary-rgb), 0.1); color: var(--primary);
            font-size: 0.75rem; font-weight: 800; padding: 0.5rem 1rem;
            border-radius: 100px; text-transform: uppercase; letter-spacing: 0.05em;
            margin-right: 0.5rem; border: 1px solid rgba(var(--primary-rgb), 0.2);
            display: inline-flex; align-items: center; justify-content: center;
        }

        .nav-controls { display: flex; gap: 0.5rem; }
        
        .calendar-container { 
            background: rgba(255, 255, 255, 0.01); border-radius: 20px; 
            border: 1px solid var(--border); overflow: hidden;
            box-shadow: var(--sh-lg);
            display: flex; flex-direction: column;
            margin-bottom: 2rem;
        }


        /* Desktop Grid */
        .cal-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); grid-template-rows: auto; grid-auto-rows: minmax(160px, auto); }

        
        .cal-weekday { 
            background: var(--bg-darker); padding: 0.4rem 0.5rem; text-align: center;
            font-size: 0.8rem; font-weight: 800; text-transform: capitalize;
            letter-spacing: 0.05em; color: var(--text-ghost);
            border-bottom: 1px solid var(--border);
        }
        .cal-weekday.sunday { color: #ef4444; background: rgba(239, 68, 68, 0.05); }

        .cal-day { 
            border-right: 1px solid var(--border); border-bottom: 1px solid var(--border);
            padding: 0.8rem; position: relative; transition: all 0.3s var(--anim);
            display: flex; flex-direction: column; gap: 0.5rem; min-height: 100%;
        }

        .cal-day:nth-child(7n) { border-right: none; }
        .cal-day:hover { background: rgba(255, 255, 255, 0.03); }
        .cal-day.empty { background: rgba(0, 0, 0, 0.2); opacity: 0.3; }
        .cal-day.today { background: rgba(var(--primary-rgb), 0.05); }
        .cal-day.today::before { 
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; 
            background: var(--primary); box-shadow: 0 0 10px var(--primary);
        }

        .day-number { 
            font-size: 1.8rem; font-weight: 950; color: var(--text); 
            line-height: 1; opacity: 0.8; margin-bottom: 0.3rem;
            font-family: 'Outfit', sans-serif;
        }
        .sunday .day-number { color: #ef4444; }

        .activities-list { display: flex; flex-direction: column; gap: 0.3rem; }


        .act-pill { 
            font-size: 0.6rem; padding: 0.35rem 0.5rem; border-radius: 6px;
            background: rgba(var(--primary-rgb), 0.1); border: 1px solid rgba(var(--primary-rgb), 0.15);
            color: var(--text); font-weight: 700; cursor: pointer;
            transition: all 0.2s; text-decoration: none;
            display: flex; align-items: center; gap: 0.4rem;
            width: 100%; overflow: hidden;
        }
        .act-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            font-weight: 800;
        }

        .act-count {
            margin-left: auto;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: rgba(var(--accent-rgb), 0.15);
            color: var(--accent);
            font-size: 0.55rem;
            font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .act-pill:hover .act-count {
            background: var(--accent);
            color: #fff;
        }
        .act-pill:hover { 
            background: rgba(var(--primary-rgb), 0.2);
            transform: scale(1.02); border-color: var(--primary); 
        }
        .act-birthday { background: transparent; border: 1px dashed rgba(150, 150, 150, 0.3); opacity: 0.7; color: var(--text-dim); }
        .act-birthday:hover { opacity: 1; transform: none; border-color: rgba(150, 150, 150, 0.5); }
        .bday-photo {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid rgba(255,255,255,0.25);
            flex-shrink: 0;
        }
        .enroll-preview {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin: 0.25rem 0 0 0.5rem;
            color: var(--text-dim);
            font-size: 0.62rem;
            font-weight: 800;
            max-width: 100%;
        }
        .enroll-preview[hidden] { display: none; }
        .enroll-avatar,
        .enroll-avatar-img {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            flex-shrink: 0;
        }
        .enroll-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--panel-hi);
            border: 1px solid var(--border);
            color: var(--primary);
            font-size: 0.6rem;
            font-weight: 900;
        }
        .enroll-avatar-img { object-fit: cover; border: 1px solid rgba(255,255,255,0.25); }
        .enroll-name {
            color: var(--text);
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .enroll-more {
            margin-left: auto;
            padding: 0.12rem 0.4rem;
            border-radius: 999px;
            background: rgba(var(--accent-rgb), 0.12);
            border: 1px solid rgba(var(--accent-rgb), 0.2);
            color: var(--accent);
            font-size: 0.58rem;
            font-weight: 900;
            flex-shrink: 0;
        }
        .act-holiday { background: rgba(251, 191, 36, 0.1); border-left: 3px solid #fbbf24; color: #b45309; }

        /* Mobile View (Rows) - IMPROVED */
        @media (max-width: 1024px) {
            .main-content { margin-left: 0 !important; padding: 1rem; height: auto; overflow: visible; }
            .calendar-header { padding-left: 4.5rem; } /* Add room for menu toggle */
            .cal-grid { display: flex; flex-direction: column; gap: 0.5rem; height: auto; border: none; background: transparent; }
            .calendar-container { border: none; background: transparent; box-shadow: none; overflow: visible; }
            .cal-weekday { display: none; }
            .cal-day { 
                background: var(--panel); border: 1px solid var(--border); border-radius: 16px;
                min-height: auto; flex-direction: row; align-items: flex-start; gap: 1rem;
                padding: 1rem; margin-bottom: 0.5rem; width: 100%;
            }
            .cal-day.empty { display: none; }
            .day-number { min-width: 40px; font-size: 1.5rem; text-align: center; }
            .activities-list { flex: 1; overflow: visible; }
            .act-pill { white-space: normal; height: auto; padding: 0.6rem; }
            .act-name { white-space: normal; }
        }


        /* FAB */
        .fab {
            position: fixed; bottom: 2rem; right: 2rem;
            width: 60px; height: 60px; border-radius: 18px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 10px 25px rgba(var(--primary-rgb), 0.4);
            transition: all 0.3s var(--anim); z-index: 2000; text-decoration: none;
        }
        .fab:hover { transform: translateY(-5px) rotate(90deg); box-shadow: 0 15px 30px rgba(var(--primary-rgb), 0.5); }

        .event-modal-backdrop {
            position: fixed; inset: 0; background: rgba(0, 0, 0, 0.72);
            backdrop-filter: blur(10px); display: none; align-items: center; justify-content: center;
            z-index: 2600; padding: 1rem;
        }
        .event-modal-backdrop.open { display: flex; }
        .event-modal {
            width: min(680px, 100%); background: #11131f; border: 1px solid var(--border);
            border-radius: 24px; box-shadow: var(--sh-lg); padding: 1.5rem;
        }
        .event-modal-header { display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .event-modal-title { font-size: 1.5rem; font-weight: 900; margin: 0.35rem 0; }
        .event-meta { display: grid; gap: 0.4rem; color: var(--text-dim); font-size: 0.9rem; }
        .participant-chips { display: grid; gap: 0.5rem; margin-top: 1rem; }
        .participant-chip {
            padding: 0.55rem 0.75rem; border-radius: 12px; background: var(--panel-hi);
            border: 1px solid var(--border); font-size: 0.85rem; color: var(--text);
            width: 100%;
        }
        .event-items-wrap { margin-top: 1rem; display: none; }
        .event-items-wrap.show { display: block; }
        .event-items-list { display: grid; gap: 0.75rem; margin-top: 1rem; }
        .event-item-card {
            border: 1px solid var(--border); background: rgba(255,255,255,0.03);
            border-radius: 16px; padding: 0.9rem;
        }
        .event-item-head {
            display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;
            flex-wrap: wrap;
        }
        .event-item-title { font-size: 0.95rem; font-weight: 800; color: var(--text); }
        .event-item-count { font-size: 0.78rem; color: var(--text-dim); }
        .event-item-actions { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-top: 0.8rem; }
        .event-item-participants { display: grid; gap: 0.4rem; margin-top: 0.8rem; }
        .event-item-note { margin-top: 0.8rem; font-size: 0.78rem; color: #fbbf24; }
        .event-modal-actions { display: flex; gap: 0.75rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .modal-note {
            margin-top: 1rem; padding: 0.8rem 1rem; border-radius: 14px;
            background: rgba(245, 158, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25);
            font-size: 0.85rem; display: none;
        }
        .modal-feedback { margin-top: 1rem; display: none; }
        .modal-feedback.show { display: block; }
        .act-pill.button-reset {
            width: 100%; border-top: none; border-right: none; border-bottom: none;
            text-align: left; appearance: none;
        }

        /* Modal Scroll Style */
        .event-modal { max-height: 90vh; display: flex; flex-direction: column; }
        .event-modal-content-scroll {
            flex: 1;
            overflow-y: auto;
            margin: 1rem 0;
            padding-right: 0.5rem;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) transparent;
        }
        .event-modal-content-scroll::-webkit-scrollbar { width: 6px; }
        .event-modal-content-scroll::-webkit-scrollbar-track { background: transparent; }
        .event-modal-content-scroll::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
        .event-modal-content-scroll::-webkit-scrollbar-thumb:hover { background: var(--accent); }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <?php if ($msg && strpos(strtolower($msg), 'contexto') === false): ?>
                <div class="animate-in" style="margin-bottom: 1rem;"><?= alert('success', h($msg)) ?></div>
            <?php endif; ?>
            <header class="calendar-header animate-in">
                <div class="month-display">
                    <h1 class="gradient-text" style="background: linear-gradient(to bottom, var(--primary), var(--accent)); -webkit-background-clip: text;"><?= $displayMonth ?></h1>
                    <?php if ($msg && strpos(strtolower($msg), 'contexto') !== false): ?>
                        <span id="ctxMsg" class="context-msg animate-in"><?= h($msg) ?></span>
                    <?php endif; ?>
                    <span class="year-label"><?= $year ?></span>
                </div>
                <div class="nav-controls">
                    <a href="?m=<?= $month-1 ?>&y=<?= $year ?>" class="btn btn-ghost" style="padding: 0.7rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"/></svg></a>
                    <a href="?m=<?= date('n') ?>&y=<?= date('Y') ?>" class="btn btn-ghost" style="padding: 0.7rem 1.2rem; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.05em;">HOJE</a>
                    <a href="?m=<?= $month+1 ?>&y=<?= $year ?>" class="btn btn-ghost" style="padding: 0.7rem;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg></a>
                </div>
            </header>

            <div class="calendar-container animate-in" style="animation-delay: 0.1s;">
                <div class="cal-grid">
                    <?php 
                    $weekdays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                    foreach ($weekdays as $i => $day): ?>
                        <div class="cal-weekday <?= $i === 0 ? 'sunday' : '' ?>"><?= $day ?></div>
                    <?php endforeach; ?>

                    <?php 
                    // Empty cells before start
                    for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
                        <div class="cal-day empty"></div>
                    <?php endfor;

                    // Days of the month
                    for ($dayIdx = 1; $dayIdx <= $daysInMonth; $dayIdx++): 
                        $currentDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($dayIdx, 2, '0', STR_PAD_LEFT);
                        $isSunday = (date('w', strtotime($currentDate)) == 0);
                        $isToday = ($currentDate === $today);
                        ?>
                        <div class="cal-day <?= $isSunday ? 'sunday' : '' ?> <?= $isToday ? 'today' : '' ?>">
                            <div class="day-number"><?= $dayIdx ?></div>
                            
                            <div class="activities-list">
                                <?php if (isset($activitiesByDay[$dayIdx])): ?>
                                    <?php foreach ($activitiesByDay[$dayIdx] as $act): ?>
                                        <?php if (!empty($act['is_birthday'])): ?>
                                            <div class="act-pill act-birthday" style="pointer-events: none;">
                                                <?php if (!empty($act['foto_perfil']) && file_exists(__DIR__ . '/' . $act['foto_perfil'])): ?>
                                                    <img class="bday-photo" src="<?= h($act['foto_perfil']) ?>?v=<?= time() ?>" alt="Foto">
                                                <?php else: ?>
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                <?php endif; ?>
                                                <strong style="font-weight: 700; font-size: 0.55rem;"><?= h($act['nome']) ?></strong>
                                            </div>
                                        <?php elseif (!empty($act['is_holiday'])): ?>
                                            <div class="act-pill act-holiday" style="pointer-events: none;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                                                <strong style="font-weight: 800;"><?= h($act['nome']) ?></strong>
                                            </div>
                                        <?php else: ?>
                                            <?php if ($canInteractActivities): ?>
                                            <?php
                                                $pillClasses = "act-pill button-reset event-trigger";
                                                if (!empty($act['is_multi_color'])) $pillClasses .= " is-multi";
                                                if (!empty($act['is_flashing'])) $pillClasses .= " is-flashing";
                                                
                                                $pillStyle = "border-left: 3px solid " . ($act['cor'] ?: 'var(--primary)') . ";";
                                                if (!empty($act['is_multi_color'])) {
                                                    $pillStyle .= " background: linear-gradient(90deg, " . ($act['cor'] ?: '#8b5cf6') . "22, rgba(255,255,255,0.02)) !important;";
                                                }
                                            ?>
                                            <button
                                                    type="button"
                                                    class="<?= $pillClasses ?>"
                                                    data-activity-id="<?= (int)$act['id'] ?>"
                                                    style="<?= $pillStyle ?>"
                                                >
                                                    <span style="opacity: 0.6;"><?= substr($act['hora_inicio'] ?? '', 0, 5) ?></span>
                                                    <strong class="act-name"><?= h($act['nome']) ?></strong>
                                                    <span class="act-count"><?= (int)($act['total_inscritos'] ?? 0) ?></span>
                                                </button>
                                                <?php
                                                    $enName = trim((string)($act['primeiro_inscrito_nome'] ?? ''));
                                                    $enPhoto = trim((string)($act['primeiro_inscrito_foto'] ?? ''));
                                                    $enParts = preg_split('/\s+/', trim($enName)) ?: [];
                                                    $enFirst = $enParts[0] ?? $enName;
                                                    $enLast = count($enParts) > 1 ? $enParts[count($enParts) - 1] : '';
                                                    $enDisp = trim($enFirst . ' ' . $enLast);
                                                    $enDisp = mb_substr($enDisp ?: $enName, 0, 12);
                                                    $enTotal = (int)($act['total_inscritos'] ?? 0);
                                                    $enMore = max(0, $enTotal - 1);
                                                ?>
                                                <div class="enroll-preview" <?= ($enTotal > 0 && $enName !== '') ? '' : 'hidden' ?>>
                                                    <?php if ($enPhoto !== '' && file_exists(__DIR__ . '/' . $enPhoto)): ?>
                                                        <img class="enroll-avatar-img" src="<?= h($enPhoto) ?>?v=<?= time() ?>" alt="Foto">
                                                    <?php else: ?>
                                                        <div class="enroll-avatar"><?= mb_strtoupper(mb_substr($enDisp ?: $enName ?: '?', 0, 1)) ?></div>
                                                    <?php endif; ?>
                                                    <span class="enroll-name"><?= h($enDisp ?: $enName ?: '?') ?></span>
                                                    <?php if ($enMore > 0): ?><span class="enroll-more">+<?= $enMore ?></span><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $pillClasses = "act-pill";
                                                    if (!empty($act['is_multi_color'])) $pillClasses .= " is-multi";
                                                    if (!empty($act['is_flashing'])) $pillClasses .= " is-flashing";
                                                    
                                                    $pillStyle = "border-left: 3px solid " . ($act['cor'] ?: 'var(--primary)') . ";";
                                                    if (!empty($act['is_multi_color'])) {
                                                        $pillStyle .= " background: linear-gradient(90deg, " . ($act['cor'] ?: '#8b5cf6') . "22, rgba(255,255,255,0.02)) !important;";
                                                    }
                                                ?>
                                                <a href="ver_atividade.php?id=<?= $act['id'] ?>" class="<?= $pillClasses ?>" style="<?= $pillStyle ?>">
                                                    <span style="opacity: 0.6;"><?= substr($act['hora_inicio'] ?? '', 0, 5) ?></span>
                                                    <strong class="act-name"><?= h($act['nome']) ?></strong>
                                                    <span class="act-count"><?= (int)($act['total_inscritos'] ?? 0) ?></span>
                                                </a>
                                                <?php
                                                    $enName = trim((string)($act['primeiro_inscrito_nome'] ?? ''));
                                                    $enPhoto = trim((string)($act['primeiro_inscrito_foto'] ?? ''));
                                                    $enParts = preg_split('/\s+/', trim($enName)) ?: [];
                                                    $enFirst = $enParts[0] ?? $enName;
                                                    $enLast = count($enParts) > 1 ? $enParts[count($enParts) - 1] : '';
                                                    $enDisp = trim($enFirst . ' ' . $enLast);
                                                    $enDisp = mb_substr($enDisp ?: $enName, 0, 12);
                                                    $enTotal = (int)($act['total_inscritos'] ?? 0);
                                                    $enMore = max(0, $enTotal - 1);
                                                ?>
                                                <div class="enroll-preview" <?= ($enTotal > 0 && $enName !== '') ? '' : 'hidden' ?>>
                                                    <?php if ($enPhoto !== '' && file_exists(__DIR__ . '/' . $enPhoto)): ?>
                                                        <img class="enroll-avatar-img" src="<?= h($enPhoto) ?>?v=<?= time() ?>" alt="Foto">
                                                    <?php else: ?>
                                                        <div class="enroll-avatar"><?= mb_strtoupper(mb_substr($enDisp ?: $enName ?: '?', 0, 1)) ?></div>
                                                    <?php endif; ?>
                                                    <span class="enroll-name"><?= h($enDisp ?: $enName ?: '?') ?></span>
                                                    <?php if ($enMore > 0): ?><span class="enroll-more">+<?= $enMore ?></span><?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Move FAB inside a check that is less restrictive for Master users -->
    <?php if (can('criar_eventos') || $_SESSION['usuario_id'] == 1): ?>
    <a href="novaatividade.php" class="fab shimmer" title="Adicionar Atividade">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    </a>
    <?php endif; ?>

    <div class="event-modal-backdrop" id="eventModalBackdrop">
        <div class="event-modal" role="dialog" aria-modal="true" aria-labelledby="eventModalTitle">
            <div class="event-modal-header">
                <div>
                    <div id="eventModalType" class="type-badge" style="background: var(--panel-hi);">Evento</div>
                    <h2 id="eventModalTitle" class="event-modal-title">Carregando...</h2>
                    <div class="event-meta">
                        <span id="eventModalDate"></span>
                        <span id="eventModalLocation"></span>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost" id="closeEventModal">Fechar</button>
            </div>

            <div class="event-modal-content-scroll">
                <p id="eventModalDescription" style="color: var(--text); line-height: 1.6; margin: 0;"></p>

                <div id="eventModalItemsWrap" class="event-items-wrap">
                    <strong style="font-size: 0.85rem;">Atividades do evento</strong>
                    <div id="eventModalItems" class="event-items-list"></div>
                </div>

                <div id="eventModalParticipantsWrap" style="margin-top: 1rem;">
                    <strong style="font-size: 0.85rem;">Participantes</strong>
                    <div id="eventModalParticipants" class="participant-chips"></div>
                </div>

                <div id="eventModalNote" class="modal-note"></div>
                <div id="eventModalFeedback" class="modal-feedback"></div>
            </div>

            <div class="event-modal-actions">
                <button type="button" class="btn btn-primary shimmer" id="eventJoinButton" style="display:none;">Inscrever-me</button>
                <button type="button" class="btn btn-ghost" id="eventLeaveButton" style="display:none;">Desistir</button>
                <a href="#" class="btn btn-ghost" id="eventViewButton">Ver detalhes</a>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const backdrop = document.getElementById('eventModalBackdrop');
            const closeButton = document.getElementById('closeEventModal');
            const joinButton = document.getElementById('eventJoinButton');
            const leaveButton = document.getElementById('eventLeaveButton');
            const viewButton = document.getElementById('eventViewButton');
            const noteBox = document.getElementById('eventModalNote');
            const feedbackBox = document.getElementById('eventModalFeedback');
            const itemsWrap = document.getElementById('eventModalItemsWrap');
            const itemsContainer = document.getElementById('eventModalItems');
            const participantsWrap = document.getElementById('eventModalParticipantsWrap');
            let currentActivityId = null;

            function setFeedback(type, message) {
                if (!message) {
                    feedbackBox.className = 'modal-feedback';
                    feedbackBox.innerHTML = '';
                    return;
                }
                feedbackBox.className = 'modal-feedback show';
                feedbackBox.innerHTML = <?= json_encode(alert('info', '__MSG__')) ?>.replace('__MSG__', message);
                if (type === 'error') {
                    feedbackBox.innerHTML = <?= json_encode(alert('error', '__MSG__')) ?>.replace('__MSG__', message);
                }
                if (type === 'success') {
                    feedbackBox.innerHTML = <?= json_encode(alert('success', '__MSG__')) ?>.replace('__MSG__', message);
                }
            }

            function closeModal() {
                backdrop.classList.remove('open');
                currentActivityId = null;
                setFeedback('', '');
                noteBox.style.display = 'none';
                noteBox.textContent = '';
                itemsWrap.classList.remove('show');
                itemsContainer.innerHTML = '';
                participantsWrap.style.display = 'block';
            }

            async function loadActivity(id) {
                const response = await fetch(`atividade_json.php?id=${id}`, { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();
                if (!payload.success) {
                    throw new Error(payload.message || 'Falha ao carregar atividade.');
                }
                return payload.data.activity;
            }

            function renderParticipants(participants) {
                const container = document.getElementById('eventModalParticipants');
                if (!participants.length) {
                    container.innerHTML = '<div class="participant-chip">Nenhum inscrito ainda</div>';
                    return;
                }
                container.innerHTML = participants.map((participant) => (
                    `<div class="participant-chip">${participant.nome}</div>`
                )).join('');
            }

            function renderEventItems(activity) {
                const items = Array.isArray(activity.event_items) ? activity.event_items : [];
                if (!items.length) {
                    itemsWrap.classList.remove('show');
                    itemsContainer.innerHTML = '';
                    participantsWrap.style.display = 'block';
                    return false;
                }

                participantsWrap.style.display = 'none';
                itemsWrap.classList.add('show');
                itemsContainer.innerHTML = items.map((item) => {
                    const participants = Array.isArray(item.participants) && item.participants.length
                        ? item.participants.map((participant) => `<div class="participant-chip">${participant.nome}</div>`).join('')
                        : '<div class="participant-chip">Nenhum inscrito nesta atividade</div>';
                    const joinAction = activity.can_interact && !item.usuario_inscrito
                        ? `<button type="button" class="btn btn-primary shimmer event-item-action" data-action="join" data-item-id="${item.id}">Inscrever-me</button>`
                        : '';
                    const leaveAction = activity.can_interact && item.usuario_inscrito && activity.can_cancel_now
                        ? `<button type="button" class="btn btn-ghost event-item-action" data-action="leave" data-item-id="${item.id}">Desistir</button>`
                        : '';
                    const note = item.usuario_inscrito && !activity.can_cancel_now
                        ? `<div class="event-item-note">${activity.deadline_message}</div>`
                        : '';

                    return `
                        <div class="event-item-card">
                            <div class="event-item-head">
                                <div class="event-item-title">${item.nome}</div>
                                <div class="event-item-count">${item.total_inscritos} inscrito(s)</div>
                            </div>
                            <div class="event-item-actions">${joinAction}${leaveAction}</div>
                            <div class="event-item-participants">${participants}</div>
                            ${note}
                        </div>
                    `;
                }).join('');
                return true;
            }

            function fillModal(activity) {
                const hasEventItems = renderEventItems(activity);
                currentActivityId = activity.id;
                document.getElementById('eventModalType').textContent = activity.nome_tipo || 'Evento';
                document.getElementById('eventModalTitle').textContent = activity.nome;
                document.getElementById('eventModalDate').textContent = `${activity.data_inicio} às ${String(activity.hora_inicio || '00:00').slice(0, 5)}`;
                document.getElementById('eventModalLocation').textContent = activity.local_nome || 'Local não definido';
                document.getElementById('eventModalDescription').textContent = activity.descricao || 'Sem descrição.';
                viewButton.href = `ver_atividade.php?id=${activity.id}`;
                if (!hasEventItems) {
                    renderParticipants(activity.participants || []);
                }

                joinButton.style.display = !hasEventItems && activity.can_interact && !activity.usuario_inscrito ? 'inline-flex' : 'none';
                leaveButton.style.display = !hasEventItems && activity.can_interact && activity.usuario_inscrito ? 'inline-flex' : 'none';

                noteBox.style.display = !hasEventItems && !activity.can_cancel_now && activity.usuario_inscrito ? 'block' : 'none';
                noteBox.textContent = !hasEventItems && !activity.can_cancel_now && activity.usuario_inscrito ? activity.deadline_message : '';
                setFeedback('', '');
            }

            async function refreshModal() {
                if (!currentActivityId) return;
                const activity = await loadActivity(currentActivityId);
                fillModal(activity);
            }

            async function submitEnrollment(action, itemId = null) {
                if (!currentActivityId) return;
                const response = await fetch('inscrever.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        id: String(currentActivityId),
                        action,
                        item_id: itemId ? String(itemId) : ''
                    })
                });
                const payload = await response.json();
                setFeedback(payload.success ? 'success' : 'error', payload.message || '');
                if (payload.success) {
                    await refreshModal();
                    setTimeout(() => window.location.reload(), 1000);
                }
            }

            document.querySelectorAll('.event-trigger').forEach((button) => {
                button.addEventListener('click', async () => {
                    try {
                        const activity = await loadActivity(button.dataset.activityId);
                        fillModal(activity);
                        backdrop.classList.add('open');
                    } catch (error) {
                        alert(error.message);
                    }
                });
            });

            closeButton.addEventListener('click', closeModal);
            backdrop.addEventListener('click', (event) => {
                if (event.target === backdrop) {
                    closeModal();
                }
            });
            joinButton.addEventListener('click', () => submitEnrollment('join'));
            leaveButton.addEventListener('click', () => submitEnrollment('leave'));
            itemsContainer.addEventListener('click', (event) => {
                const button = event.target.closest('.event-item-action');
                if (!button) {
                    return;
                }
                submitEnrollment(button.dataset.action, button.dataset.itemId);
            });

            <?php if ($autoRefresh): ?>
            setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.delete('refresh');
                window.location.replace(url.toString());
            }, 1000);
            <?php endif; ?>
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctxMsg = document.getElementById('ctxMsg');
            if (ctxMsg) {
                setTimeout(() => {
                    ctxMsg.style.transition = 'all 0.5s ease';
                    ctxMsg.style.opacity = '0';
                    ctxMsg.style.transform = 'translateX(-10px)';
                    setTimeout(() => ctxMsg.remove(), 500);
                }, 4000);
            }
        });
    </script>
</body>
</html>
