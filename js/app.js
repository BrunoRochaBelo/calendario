/**
 * app.js — Flexible Calendar Engine (RBAC aware)
 */

document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('calendar-grid');
    const monthDisplay = document.getElementById('monthDisplay');
    const modal = document.getElementById('event-modal');

    let currentMonth = new Date().getMonth() + 1;
    let currentYear = new Date().getFullYear();

    const months = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

    async function load() {
        monthDisplay.textContent = `${months[currentMonth - 1]} ${currentYear}`;
        grid.innerHTML = '';
        
        // Add Header
        ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"].forEach(d => {
            const h = document.createElement('div');
            h.className = 'calendar-day-head';
            h.textContent = d;
            grid.appendChild(h);
        });

        const first = new Date(currentYear, currentMonth - 1, 1);
        const last = new Date(currentYear, currentMonth, 0);
        const startOffset = first.getDay();
        const totalDays = last.getDate();

        // Fetch Events
        const resp = await fetch(`atividade_json.php?month=${currentMonth}&year=${currentYear}`);
        const events = await resp.json();

        // Empty cells for prev month
        for (let i = 0; i < startOffset; i++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-cell other-month';
            grid.appendChild(cell);
        }

        // Current Month Days
        for (let d = 1; d <= totalDays; d++) {
            const dateStr = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const isToday = new Date().toISOString().split('T')[0] === dateStr;

            const cell = document.createElement('div');
            cell.className = `calendar-cell ${isToday ? 'today' : ''}`;
            cell.innerHTML = `<div class="day-num">${d}</div>`;

            // Filter events for this day
            const dayEvents = events.filter(e => e.data_inicio.startsWith(dateStr));
            dayEvents.forEach(ev => {
                const el = document.createElement('div');
                el.className = 'event-item';
                el.style.borderLeftColor = ev.cor || '#8b5cf6';
                el.textContent = ev.titulo;
                el.onclick = () => showEvent(ev);
                cell.appendChild(el);
            });

            if (window.PASCOM.canCreate) {
                cell.title = "Clique duplo para criar";
                cell.ondblclick = () => window.location = `novaatividade.php?data=${dateStr}`;
            }

            grid.appendChild(cell);
        }
    }

    function showEvent(ev) {
        document.getElementById('modal-title').textContent = ev.titulo;
        document.getElementById('modal-category-dot').style.background = ev.cor || 'var(--primary)';
        document.getElementById('modal-datetime').textContent = new Date(ev.data_inicio).toLocaleString('pt-BR');
        document.getElementById('modal-location').textContent = ev.local_nome || 'Local não definido';
        document.getElementById('modal-description').textContent = ev.descricao || 'Sem descrição.';

        const actions = document.getElementById('modal-actions');
        actions.innerHTML = '';
        if (window.PASCOM.canEdit) {
            actions.innerHTML += `<a href="editar_atividade.php?id=${ev.id}" class="btn btn-ghost" style="color:var(--amber)">Editar</a>`;
        }
        if (window.PASCOM.canDelete) {
            actions.innerHTML += `<a href="excluir_atividade.php?id=${ev.id}" class="btn btn-ghost" style="color:var(--red)" onclick="return confirm('Excluir?')">Excluir</a>`;
        }
        actions.innerHTML += `<button class="btn btn-ghost" onclick="closeModal()">Fechar</button>`;

        modal.style.display = 'flex';
    }

    window.closeModal = () => { modal.style.display = 'none'; };

    document.getElementById('prevMonth').onclick = () => {
        currentMonth--;
        if (currentMonth < 1) { currentMonth = 12; currentYear--; }
        load();
    };
    document.getElementById('nextMonth').onclick = () => {
        currentMonth++;
        if (currentMonth > 12) { currentMonth = 1; currentYear++; }
        load();
    };

    load();
});
