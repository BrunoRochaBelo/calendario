<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Master Calendar (v2.0 Ultra Premium)
 * High-Performance Grid · Sunday Red Highlight · Responsive
 * ═══════════════════════════════════════════════════════ */

require_once 'functions.php';
requireLogin();
ensureInscricoesTable($conn);

$pid = current_paroquia_id();
$canInteractActivities = canInteractWithActivity();

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
            SELECT COUNT(*)
            FROM inscricoes i
            WHERE i.atividade_id = a.id
        ) AS total_inscritos
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

// 4. Fetch Birthdays
$sqlB = "SELECT nome, data_nascimento FROM usuarios WHERE paroquia_id = ? AND MONTH(data_nascimento) = ? AND ativo = 1";
$stmtB = $conn->prepare($sqlB);
$stmtB->bind_param('ii', $pid, $month);
$stmtB->execute();
$resB = $stmtB->get_result();
while ($u = $resB->fetch_assoc()) {
    $day = (int)date('d', strtotime($u['data_nascimento']));
    $bdayAct = [
        'is_birthday' => true,
        'nome' => "Aniver: " . explode(' ', trim($u['nome']))[0]
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
        .main-content { flex: 1; margin-left: var(--sidebar-w); padding: 1.5rem; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        
        /* Premium Calendar UI */
        .calendar-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .month-display { display: flex; align-items: baseline; gap: 1rem; }
        .month-display h1 { 
            font-size: clamp(2rem, 5vw, 4rem); color: var(--primary); font-weight: 950; 
            letter-spacing: -0.05em; margin: 0; line-height: 0.8;
            text-transform: uppercase;
        }
        .year-label { font-size: clamp(1.5rem, 3vw, 2.5rem); color: var(--text); font-weight: 900; opacity: 0.9; }

        .nav-controls { display: flex; gap: 0.5rem; }
        
        .calendar-container { 
            flex: 1;
            background: rgba(255, 255, 255, 0.01); border-radius: 20px; 
            border: 1px solid var(--border); overflow: hidden;
            box-shadow: var(--sh-lg);
            display: flex; flex-direction: column;
        }

        /* Desktop Grid */
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); flex: 1; min-height: 0; }
        
        .cal-weekday { 
            background: var(--bg-darker); padding: 0.75rem; text-align: center;
            font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.1em; color: var(--text-ghost);
            border-bottom: 1px solid var(--border);
        }
        .cal-weekday.sunday { color: #ef4444; background: rgba(239, 68, 68, 0.05); }

        .cal-day { 
            border-right: 1px solid var(--border); border-bottom: 1px solid var(--border);
            padding: 0.8rem; position: relative; transition: all 0.3s var(--anim);
            display: flex; flex-direction: column; gap: 0.4rem; overflow: hidden;
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

        .activities-list { overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 0.3rem; }
        .activities-list::-webkit-scrollbar { width: 3px; }

        .act-pill { 
            font-size: 0.6rem; padding: 0.35rem 0.5rem; border-radius: 6px;
            background: rgba(var(--primary-rgb), 0.1); border: 1px solid rgba(var(--primary-rgb), 0.15);
            color: var(--text); font-weight: 700; cursor: pointer;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: all 0.2s; text-decoration: none;
            display: flex; align-items: center; gap: 0.3rem;
        }
        .act-count {
            margin-left: auto;
            padding: 0.18rem 0.45rem;
            border-radius: 999px;
            background: rgba(var(--accent-rgb), 0.12);
            color: var(--accent);
            font-size: 0.52rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .act-pill:hover { 
            background: rgba(var(--primary-rgb), 0.2);
            transform: scale(1.02); border-color: var(--primary); 
        }
        .act-birthday { background: transparent; border: 1px dashed rgba(150, 150, 150, 0.3); opacity: 0.7; color: var(--text-dim); }
        .act-birthday:hover { opacity: 1; transform: none; border-color: rgba(150, 150, 150, 0.5); }
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
        .participant-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem; }
        .participant-chip {
            padding: 0.45rem 0.75rem; border-radius: 999px; background: var(--panel-hi);
            border: 1px solid var(--border); font-size: 0.8rem; color: var(--text);
        }
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
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <header class="calendar-header animate-in">
                <div class="month-display">
                    <h1 class="gradient-text" style="background: linear-gradient(to bottom, var(--primary), var(--accent)); -webkit-background-clip: text;"><?= $displayMonth ?></h1>
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
                    $weekdays = ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SAB'];
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
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                <strong style="font-weight: 700; font-size: 0.55rem;"><?= h($act['nome']) ?></strong>
                                            </div>
                                        <?php elseif (!empty($act['is_holiday'])): ?>
                                            <div class="act-pill act-holiday" style="pointer-events: none;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                                                <strong style="font-weight: 800;"><?= h($act['nome']) ?></strong>
                                            </div>
                                        <?php else: ?>
                                            <?php if ($canInteractActivities): ?>
                                            <button
                                                    type="button"
                                                    class="act-pill button-reset event-trigger"
                                                    data-activity-id="<?= (int)$act['id'] ?>"
                                                    style="border-left: 3px solid <?= h($act['cor'] ?: 'var(--primary)') ?>;"
                                                >
                                                    <span style="opacity: 0.6;"><?= substr($act['hora_inicio'], 0, 5) ?></span>
                                                    <strong style="font-weight: 800;"><?= h($act['nome']) ?></strong>
                                                    <span class="act-count"><?= (int)($act['total_inscritos'] ?? 0) ?> inscritos</span>
                                                </button>
                                            <?php else: ?>
                                                <a href="ver_atividade.php?id=<?= $act['id'] ?>" class="act-pill" style="border-left: 3px solid <?= h($act['cor'] ?: 'var(--primary)') ?>;">
                                                    <span style="opacity: 0.6;"><?= substr($act['hora_inicio'], 0, 5) ?></span>
                                                    <strong style="font-weight: 800;"><?= h($act['nome']) ?></strong>
                                                    <span class="act-count"><?= (int)($act['total_inscritos'] ?? 0) ?> inscritos</span>
                                                </a>
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

            <p id="eventModalDescription" style="color: var(--text); line-height: 1.6; margin: 0;"></p>

            <div style="margin-top: 1rem;">
                <strong style="font-size: 0.85rem;">Participantes</strong>
                <div id="eventModalParticipants" class="participant-chips"></div>
            </div>

            <div id="eventModalNote" class="modal-note"></div>
            <div id="eventModalFeedback" class="modal-feedback"></div>

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
                    container.innerHTML = '<span class="participant-chip">Nenhum inscrito ainda</span>';
                    return;
                }
                container.innerHTML = participants.map((participant) => (
                    `<span class="participant-chip">${participant.nome}</span>`
                )).join('');
            }

            function fillModal(activity) {
                currentActivityId = activity.id;
                document.getElementById('eventModalType').textContent = activity.nome_tipo || 'Evento';
                document.getElementById('eventModalTitle').textContent = activity.nome;
                document.getElementById('eventModalDate').textContent = `${activity.data_inicio} às ${String(activity.hora_inicio || '00:00').slice(0, 5)}`;
                document.getElementById('eventModalLocation').textContent = activity.local_nome || 'Local não definido';
                document.getElementById('eventModalDescription').textContent = activity.descricao || 'Sem descrição.';
                viewButton.href = `ver_atividade.php?id=${activity.id}`;
                renderParticipants(activity.participants || []);

                joinButton.style.display = activity.can_interact && !activity.usuario_inscrito ? 'inline-flex' : 'none';
                leaveButton.style.display = activity.can_interact && activity.usuario_inscrito ? 'inline-flex' : 'none';

                noteBox.style.display = (!activity.can_cancel_now && activity.usuario_inscrito) ? 'block' : 'none';
                noteBox.textContent = (!activity.can_cancel_now && activity.usuario_inscrito) ? activity.deadline_message : '';
                setFeedback('', '');
            }

            async function refreshModal() {
                if (!currentActivityId) return;
                const activity = await loadActivity(currentActivityId);
                fillModal(activity);
            }

            async function submitEnrollment(action) {
                if (!currentActivityId) return;
                const response = await fetch('inscrever.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ id: String(currentActivityId), action })
                });
                const payload = await response.json();
                setFeedback(payload.success ? 'success' : 'error', payload.message || '');
                if (payload.success) {
                    await refreshModal();
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
        })();
    </script>
</body>
</html>
