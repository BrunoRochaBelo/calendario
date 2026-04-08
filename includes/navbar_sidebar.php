<?php
// Este arquivo contém a navbar e a sidebar para serem incluídas nas páginas internas.
// Garante que a sessão já foi iniciada no arquivo principal.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// O functions.php também deve ser incluído na página principal antes deste arquivo.
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
            aria-controls="sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Lógica Nível 0 para Seleção de Paróquia (Dropdown) -->
        <?php if (has_level(0)):
            $stmtPar = $conn->query('SELECT id, nome FROM paroquias ORDER BY nome');
            $nome_par = 'Selecione a Paróquia';

            // Pega o nome da paróquia atual
            if (!empty($_SESSION['paroquia_id'])) {
                $stmtAtual = $conn->prepare('SELECT nome FROM paroquias WHERE id = ?');
                $stmtAtual->bind_param('i', $_SESSION['paroquia_id']);
                $stmtAtual->execute();
                $resAtual = $stmtAtual->get_result();
                if ($resAtual->num_rows > 0) {
                    $nome_par = $resAtual->fetch_assoc()['nome'];
                }
                $stmtAtual->close();
            }
            ?>
            <div class="dropdown me-3">
                <a class="navbar-brand dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <?php echo htmlspecialchars($nome_par); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php while ($p = $stmtPar->fetch_assoc()): ?>
                        <li><a class="dropdown-item"
                                href="select_paroquia.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nome']); ?></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php else:
            // Para os outros perfis, exibe apenas a paróquia se houver ou um fixo
            $nome_par = 'PasCom';
            if (!empty($_SESSION['paroquia_id'])) {
                $stmtAtual = $conn->prepare('SELECT nome FROM paroquias WHERE id = ?');
                $stmtAtual->bind_param('i', $_SESSION['paroquia_id']);
                $stmtAtual->execute();
                $resAtual = $stmtAtual->get_result();
                if ($resAtual->num_rows > 0) {
                    $nome_par = $resAtual->fetch_assoc()['nome'];
                }
                $stmtAtual->close();
            }
            ?>
            <span class="navbar-brand"><?php echo htmlspecialchars($nome_par); ?></span>
        <?php endif; ?>

        <div class="d-flex ms-auto align-items-center">
            <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
                <div class="month-nav text-white d-none d-md-flex">
                    <button id="prev" class="btn btn-outline-light">◀</button>
                    <span id="monthYear" class="mx-2 align-self-center"></span>
                    <button id="next" class="btn btn-outline-light">▶</button>
                </div>
            <?php endif; ?>
            <div class="dropdown">
                <a href="#" class="d-block link-light text-decoration-none dropdown-toggle ms-3"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> Olá,
                    <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? ''); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end text-small">
                    <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="index.php"
                    class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="bi bi-house-door me-2"></i>Home
                </a>
            </li>
            <?php if (has_level(1)): // Nível 1 (Supervisor) ou 0 (Admin) ?>
                <li>
                    <a href="cores.php"
                        class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'cores.php' ? 'active' : ''; ?>">
                        <i class="bi bi-palette me-2"></i>Cores
                    </a>
                </li>
                <li>
                    <a href="tipos_atividade.php"
                        class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'tipos_atividade.php' ? 'active' : ''; ?>">
                        <i class="bi bi-card-list me-2"></i>Tipos de atividade
                    </a>
                </li>
                <li>
                    <a href="locais_paroquia.php"
                        class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'locais_paroquia.php' ? 'active' : ''; ?>">
                        <i class="bi bi-geo-alt me-2"></i>Locais
                    </a>
                </li>
                <li>
                    <a href="logs.php"
                        class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'logs.php' ? 'active' : ''; ?>">
                        <i class="bi bi-file-text me-2"></i>Logs
                    </a>
                </li>
            <?php endif; ?>
            <?php if (has_level(0)): // Apenas Nível 0 (Admin) ?>
                <li>
                    <a href="register.php"
                        class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">
                        <i class="bi bi-person-plus me-2"></i>Criar usuário
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>