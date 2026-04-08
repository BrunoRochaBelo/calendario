document.addEventListener('DOMContentLoaded', function () {
    const monthYear = document.getElementById('monthYear');
    const calendarContainer = document.getElementById('calendar');
    const prev = document.getElementById('prev');
    const next = document.getElementById('next');
    const exportPdfBtn = document.getElementById('exportPdf');

    if (!monthYear || !calendarContainer || !prev || !next) {
        // Se os elementos essenciais não estiverem na página, não faz nada.
        // Isso previne erros em páginas que não têm o calendário.
        return;
    }

    let currentDate = new Date();

    const weekDayInitials = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];

    function renderCalendar(date) {
        monthYear.textContent = date.toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
        const year = date.getFullYear();
        const month = date.getMonth();

        if (exportPdfBtn) {
            exportPdfBtn.href = `gerar_pdf.php?year=${year}&month=${month + 1}`;
        }

        fetch(`atividade_json.php?year=${year}&month=${month + 1}`)
            .then(response => response.json())
            .then(events => {
                const eventsByDay = {};
                events.forEach(event => {
                    const day = new Date(event.data_inicio + 'T00:00:00').getDate();
                    if (!eventsByDay[day]) {
                        eventsByDay[day] = [];
                    }
                    eventsByDay[day].push(event);
                });

                calendarContainer.innerHTML = buildDesktopCalendar(year, month, eventsByDay) + buildMobileCalendar(year, month, eventsByDay);
                bindInteractButtons();
            });
    }

    function bindInteractButtons() {
        document.querySelectorAll('.interact-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation(); // Evita redirecionar para ver_atividade.php
                const id = this.getAttribute('data-id');
                const action = this.getAttribute('data-action');
                let msg = action === 'join' ? "Deseja participar desta atividade?" : "Deseja cancelar sua participação?";

                if (confirm(msg)) {
                    window.location.href = `inscrever.php?id=${id}&action=${action}`;
                }
            });
        });
    }

    function buildDesktopCalendar(year, month, eventsByDay) {
        const firstDayOfMonth = new Date(year, month, 1);
        const startDayOfWeek = firstDayOfMonth.getDay(); // 0-6 (Sun-Sat)
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let html = '<div class="d-none d-lg-block"><table class="calendar-table"><thead><tr>';
        const weekDays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        weekDays.forEach((day, index) => {
            html += `<th class="${index === 0 ? 'sunday' : ''}">${day}</th>`;
        });
        html += '</tr></thead><tbody>';

        let day = 1;
        for (let i = 0; i < 6; i++) {
            html += '<tr>';
            for (let j = 0; j < 7; j++) {
                if ((i === 0 && j < startDayOfWeek) || day > daysInMonth) {
                    html += '<td class="bg-light"></td>';
                } else {
                    const isSunday = j === 0;
                    const today = new Date();
                    const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();

                    let cellClass = isSunday ? 'sunday' : '';
                    if (isToday) cellClass += ' today';

                    html += `<td class="${cellClass}"><div class="day-number"><span>${day}</span></div>`;
                    if (eventsByDay[day]) {
                        html += eventsByDay[day].map(e => {
                            let partHtml = '';
                            let hora = e.hora_inicio ? e.hora_inicio.substring(0, 5) : '';
                            if (e.total_inscritos == 0) {
                                partHtml = `<span class="badge bg-secondary ms-1 p-1 interact-btn" data-id="${e.id}" data-action="join" title="Participar">➕</span>`;
                            } else {
                                let action = e.usuario_inscrito > 0 ? "leave" : "join";
                                let color = e.usuario_inscrito > 0 ? "success" : "primary";
                                let icon = e.usuario_inscrito > 0 ? "✔" : "➕";
                                partHtml = `<span class="badge bg-${color} ms-1 p-1 interact-btn" data-id="${e.id}" data-action="${action}" title="Participar/Sair">👥 ${e.total_inscritos} ${icon}</span>`;
                            }
                            return `<div class="event" onclick="window.location.href='ver_atividade.php?id=${e.id}'">
                                <strong>${hora}</strong> ${e.nome} <br>
                                ${partHtml}
                            </div>`;
                        }).join('');
                    }
                    if (window.appData && window.appData.canCreateActivity) {
                        html += `<a class="add-event" href="novaatividade.php?data=${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}" title="Adicionar atividade neste dia">➕</a>`;
                    }
                    html += '</td>';
                    day++;
                }
            }
            html += '</tr>';
            if (day > daysInMonth) break;
        }

        html += '</tbody></table></div>';
        return html;
    }

    function buildMobileCalendar(year, month, eventsByDay) {
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        let html = '<div class="d-lg-none mobile-calendar-list">';

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dayOfWeek = date.getDay();
            const isSunday = dayOfWeek === 0;

            let addEventLink = '';
            if (window.appData && window.appData.canCreateActivity) {
                addEventLink = ` <a class="add-event-mobile" href="novaatividade.php?data=${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}" title="Adicionar atividade neste dia">➕</a>`;
            }

            html += `<div class="mobile-day-item ${isSunday ? 'sunday' : ''}">`;
            html += `<div class="mobile-day-header"><strong>${weekDayInitials[dayOfWeek]}</strong> ${day}${addEventLink}</div>`;
            html += '<div class="mobile-day-events">';
            if (eventsByDay[day]) {
                html += eventsByDay[day].map(e => {
                    let partHtml = '';
                    let hora = e.hora_inicio ? e.hora_inicio.substring(0, 5) : '';
                    if (e.total_inscritos == 0) {
                        partHtml = `<span class="badge bg-secondary ms-1 p-1 interact-btn" data-id="${e.id}" data-action="join" title="Participar">➕</span>`;
                    } else {
                        let action = e.usuario_inscrito > 0 ? "leave" : "join";
                        let color = e.usuario_inscrito > 0 ? "success" : "primary";
                        let icon = e.usuario_inscrito > 0 ? "✔" : "➕";
                        partHtml = `<span class="badge bg-${color} ms-1 p-1 interact-btn" data-id="${e.id}" data-action="${action}" title="Participar/Sair">👥 ${e.total_inscritos} ${icon}</span>`;
                    }
                    return `<div class="event" onclick="window.location.href='ver_atividade.php?id=${e.id}'">
                                <strong>${hora}</strong> ${e.nome} <br>
                                ${partHtml}
                            </div>`;
                }).join('');
            } else {
                html += '<span class="text-muted">Nenhuma atividade.</span>';
            }
            html += '</div>';
            html += '</div>';
        }

        html += '</div>';
        return html;
    }

    renderCalendar(currentDate);

    prev.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    next.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
});
