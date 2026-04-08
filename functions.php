<?php
/**
 * ═══════════════════════════════════════════════════════
 * PASCOM — Global Utility Functions (v2.0)
 * Date Formatting · Sanity Checks · Parish Helpers
 * ═══════════════════════════════════════════════════════ */

require_once __DIR__ . '/config.php';

/**
 * Formata uma data para o padrão brasileiro (d/m/Y)
 */
function formatDate(string $date): string {
    if (empty($date)) return '—';
    try {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    } catch (Exception $e) {
        return h($date);
    }
}

/**
 * Formata um horário para o padrão (H:i)
 */
function formatTime(?string $time): string {
    if (empty($time)) return '00:00';
    return substr((string)$time, 0, 5);
}

/**
 * Retorna o nível de acesso do usuário atual de forma legível
 */
function getAccessLabel(int $level): string {
    $labels = [
        0 => 'Master',
        1 => 'Gerente',
        2 => 'Usuário',
        3 => 'Visitante'
    ];
    return $labels[$level] ?? 'Desconhecido';
}

/**
 * Retorna o ID da paróquia atual da sessão
 */
function current_paroquia_id(): int {
    return (int)($_SESSION['paroquia_id'] ?? 0);
}

/**
 * Sanitiza inputs de formulário em massa
 */
function sanitize_post(array $data): array {
    $sanitized = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = sanitize_post($value);
        } else {
            $sanitized[$key] = trim($value);
        }
    }
    return $sanitized;
}

/**
 * Gera um alerta HTML estilizado para o sistema
 */
function alert(string $type, string $message): string {
    $colors = [
        'success' => 'rgba(34, 197, 94, 0.1), #22c55e',
        'error'   => 'rgba(239, 68, 68, 0.1), #ef4444',
        'info'    => 'rgba(59, 130, 246, 0.1), #3b82f6'
    ];
    $style = $colors[$type] ?? $colors['info'];
    list($bg, $border) = explode(', ', $style);
    
    return "<div style='background:{$bg}; border:1px solid {$border}; color:{$border}; padding:1rem; border-radius:var(--r-md); margin-bottom:1.5rem; font-size:0.85rem; font-weight:700; text-align:center;'>{$message}</div>";
}
?>
