<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Master Calendar (v2.0 Ultra Premium)
 * High-Performance Grid · Sunday Red Highlight · Responsive
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();

$pid = current_paroquia_id();

// 1. Date Navigation Logic
$month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$year  = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');

// Adjust for overflow/underflow
if ($month > 12) { $month = 1; $year++; }
if ($month < 1) { $month = 12; $year--; }

$dt = new DateTime("$year-$month-01");
$monthName = strftime('%B', $dt->getTimestamp()); // Will use setlocale or manual map
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
    SELECT a.*, t.cor, t.icone
    FROM atividades a
    LEFT JOIN tipos_atividade t ON a.tipo_atividade_id = t.id
    WHERE a.paroquia_id = ? AND a.data_inicio BETWEEN ? AND ?
    ORDER BY a.hora_inicio ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iss', $pid, $startMonth, $endMonth);
$stmt->execute();
$res = $stmt->get_result();

$activitiesByDay = [];
while ($row = $res->fetch_assoc()) {
    $day = (int)date('d', strtotime($row['data_inicio']));
    $activitiesByDay[$day][] = $row;
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
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 2rem 3rem; }
        
        /* Premium Calendar UI */
        .calendar-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 2.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);
        }
        .month-display { display: flex; align-items: baseline; gap: 1.5rem; }
        .month-display h1 { 
            font-size: 4.5rem; color: #ef4444; font-weight: 950; 
            letter-spacing: -0.05em; margin: 0; line-height: 0.8;
            text-transform: uppercase;
        }
        .year-label { font-size: 3rem; color: var(--text); font-weight: 900; opacity: 0.9; }

        .nav-controls { display: flex; gap: 0.8rem; }
        
        .calendar-container { 
            background: rgba(255, 255, 255, 0.01); border-radius: 24px; 
            border: 1px solid var(--border); overflow: hidden;
            box-shadow: var(--sh-lg);
        }

        /* Desktop Grid */
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); }
        
        .cal-weekday { 
            background: var(--bg-darker); padding: 1rem; text-align: center;
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.1em; color: var(--text-ghost);
            border-bottom: 1px solid var(--border);
        }
        .cal-weekday.sunday { color: #ef4444; background: rgba(239, 68, 68, 0.05); }

        .cal-day { 
            min-height: 140px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border);
            padding: 1rem; position: relative; transition: all 0.3s var(--anim);
            display: flex; flex-direction: column; gap: 0.5rem;
        }
        .cal-day:nth-child(7n) { border-right: none; }
        .cal-day:hover { background: rgba(255, 255, 255, 0.03); }
        .cal-day.empty { background: rgba(0, 0, 0, 0.1); }
        .cal-day.today { background: rgba(var(--primary-rgb), 0.05); }
        .cal-day.today::before { 
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; 
            background: var(--primary); box-shadow: 0 0 10px var(--primary);
        }

        .day-number { 
            font-size: 2.2rem; font-weight: 900; color: var(--text); 
            line-height: 1; opacity: 0.8; margin-bottom: 0.5rem;
        }
        .sunday .day-number { color: #ef4444; }

        .act-pill { 
            font-size: 0.65rem; padding: 0.4rem 0.6rem; border-radius: 6px;
            background: rgba(var(--primary-rgb), 0.1); border: 1px solid rgba(var(--primary-rgb), 0.2);
            color: var(--text); font-weight: 700; cursor: pointer;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: transform 0.2s;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .act-pill:hover { transform: scale(1.03); z-index: 2; border-color: var(--primary); }

        /* Mobile View (Rows) */
        @media (max-width: 900px) {
            .main-content { margin-left: 0; padding: 1.5rem; }
            .calendar-header { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
            .month-display h1 { font-size: 3rem; }
            .year-label { font-size: 2rem; }
            
            .cal-grid { grid-template-columns: 1fr; }
            .cal-weekday { display: none; }
            .cal-day { 
                min-height: auto; border-right: none; 
                flex-direction: row; align-items: center; padding: 1.2rem;
            }
            .cal-day.empty { display: none; }
            .day-number { min-width: 50px; font-size: 1.8rem; margin-bottom: 0; }
            .activities-list { flex: 1; display: flex; flex-wrap: wrap; gap: 0.5rem; }
        }

        /* FAB */
        .fab {
            position: fixed; bottom: 2.5rem; right: 2.5rem;
            width: 64px; height: 64px; border-radius: 20px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #fff; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 15px 30px rgba(var(--primary-rgb), 0.4);
            transition: all 0.4s var(--anim); z-index: 1001; text-decoration: none;
        }
        .fab:hover { transform: translateY(-8px) rotate(90deg); box-shadow: 0 20px 40px rgba(var(--primary-rgb), 0.6); }

        .moon-icon { position: absolute; top: 1rem; right: 1rem; opacity: 0.2; }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="calendar-header animate-in">
                <div class="month-display">
                    <h1 class="gradient-text" style="background: linear-gradient(to bottom, #ef4444, #991b1b); -webkit-background-clip: text;"><?= $displayMonth ?></h1>
                    <span class="year-label"><?= $year ?></span>
                </div>
                <div class="nav-controls">
                    <a href="?m=<?= $month-1 ?>&y=<?= $year ?>" class="btn btn-ghost" style="padding: 0.8rem;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="15 18 9 12 15 6"/></svg></a>
                    <a href="?m=<?= $month+1 ?>&y=<?= $year ?>" class="btn btn-ghost" style="padding: 0.8rem;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg></a>
                </div>
            </header>

            <div class="calendar-container animate-in" style="animation-delay: 0.1s;">
                <div class="cal-grid">
                    <?php 
                    $weekdays = ['DOMINGO', 'SEGUNDA', 'TERÇA', 'QUARTA', 'QUINTA', 'SEXTA', 'SÁBADO'];
                    foreach ($weekdays as $i => $day): ?>
                        <div class="cal-weekday <?= $i === 0 ? 'sunday' : '' ?>"><?= $day ?></div>
                    <?php endforeach; ?>

                    <?php 
                    // Empty cells before start
                    for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
                        <div class="cal-day empty"></div>
                    <?php endfor;

                    // Days of the month
                    for ($day = 1; $day <= $daysInMonth; $day++): 
                        $currentDate = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        $isSunday = (date('w', strtotime($currentDate)) == 0);
                        $isToday = ($currentDate === $today);
                        ?>
                        <div class="cal-day <?= $isSunday ? 'sunday' : '' ?> <?= $isToday ? 'today' : '' ?>">
                            <div class="day-number"><?= $day ?></div>
                            
                            <div class="activities-list">
                                <?php if (isset($activitiesByDay[$day])): ?>
                                    <?php foreach ($activitiesByDay[$day] as $act): ?>
                                        <a href="ver_atividade.php?id=<?= $act['id'] ?>" class="act-pill" style="border-left: 4px solid <?= $act['cor'] ?: 'var(--primary)' ?>;">
                                            <span><?= substr($act['hora_inicio'], 0, 5) ?></span>
                                            <strong><?= h($act['nome']) ?></strong>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </main>
    </div>

    <?php if (can('criar_eventos')): ?>
    <a href="novaatividade.php" class="fab shimmer" title="Adicionar Atividade">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    </a>
    <?php endif; ?>
</body>
</html>
