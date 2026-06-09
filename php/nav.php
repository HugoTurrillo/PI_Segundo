<?php
/**
 * Enlaces de navegación unificados según sesión y rol.
 * $fromHtmlDir = true cuando la página que incluye el header está en HTML/
 */

function navUrls(bool $fromHtmlDir): array
{
    if ($fromHtmlDir) {
        return [
            "home" => "../php/home.php",
            "calendario" => "calendario.html",
            "panel_participante" => "participante.html",
            "panel_organizador" => "organizador.html",
            "login" => "login.html",
            "registro" => "registro.html",
            "logout" => "../php/logout.php",
            "logo" => "../IMG/LOGOENTERO.png",
        ];
    }

    return [
        "home" => "home.php",
        "calendario" => "../HTML/calendario.html",
        "panel_participante" => "../HTML/participante.html",
        "panel_organizador" => "../HTML/organizador.html",
        "login" => "../HTML/login.html",
        "registro" => "../HTML/registro.html",
        "logout" => "logout.php",
        "logo" => "../IMG/LOGOENTERO.png",
    ];
}

function navPaginaActual(?string $pagina): ?string
{
    if ($pagina === null || $pagina === "") {
        return null;
    }

    return strtolower(basename($pagina));
}

function navLink(string $href, string $label): string
{
    return '<a href="' . htmlspecialchars($href, ENT_QUOTES, "UTF-8") . '" class="nav-link">'
        . htmlspecialchars($label, ENT_QUOTES, "UTF-8") . "</a>";
}

function renderNavRight(bool $fromHtmlDir, ?string $paginaActual = null): void
{
    $u = navUrls($fromHtmlDir);
    $logueado = isset($_SESSION["id_usuario"]);
    $rol = $_SESSION["rol"] ?? null;
    $actual = navPaginaActual($paginaActual);

    $mostrarCalendario = $actual !== "calendario.html";
    $mostrarPanelParticipante = $actual !== "participante.html";
    $mostrarPanelOrganizador = $actual !== "organizador.html";

    if (!$logueado) {
        if ($mostrarCalendario) {
            echo navLink($u["calendario"], "Calendario");
        }
        return;
    }

    if ($rol === "participante") {
        if ($mostrarPanelParticipante) {
            echo navLink($u["panel_participante"], "Panel");
        }
        if ($mostrarCalendario) {
            echo navLink($u["calendario"], "Calendario");
        }
        return;
    }

    if ($rol === "organizador") {
        if ($mostrarPanelOrganizador) {
            echo navLink($u["panel_organizador"], "Panel");
        }
        if ($mostrarCalendario) {
            echo navLink($u["calendario"], "Calendario");
        }
    }
}

function renderNavButtons(bool $fromHtmlDir): void
{
    $u = navUrls($fromHtmlDir);

    if (!isset($_SESSION["id_usuario"])) {
        echo '<a href="' . htmlspecialchars($u["login"], ENT_QUOTES, "UTF-8") . '" class="btn nav-btn nav-btn-secondary">Acceso</a>';
        echo '<a href="' . htmlspecialchars($u["registro"], ENT_QUOTES, "UTF-8") . '" class="btn nav-btn">Unirse</a>';
        return;
    }

    echo '<a href="' . htmlspecialchars($u["logout"], ENT_QUOTES, "UTF-8") . '" class="btn nav-btn">Salir</a>';
}

function renderSiteHeader(bool $fromHtmlDir, ?string $paginaActual = null): void
{
    $u = navUrls($fromHtmlDir);
    ?>
<header class="navbar">
  <div class="navbar-inner">
    <div class="logo">
      <a href="<?= htmlspecialchars($u["home"], ENT_QUOTES, "UTF-8") ?>" aria-label="Ir al inicio">
        <img src="<?= htmlspecialchars($u["logo"], ENT_QUOTES, "UTF-8") ?>" alt="Logo Universidad Europea">
      </a>
    </div>

    <button type="button" class="nav-toggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="nav-menu">
      <span class="nav-toggle-bar"></span>
      <span class="nav-toggle-bar"></span>
      <span class="nav-toggle-bar"></span>
    </button>

    <div class="nav-backdrop" aria-hidden="true"></div>

    <div class="nav-menu" id="nav-menu">
      <nav class="nav-right" aria-label="Navegación principal">
        <?php renderNavRight($fromHtmlDir, $paginaActual); ?>
      </nav>
      <div class="nav-buttons">
        <?php renderNavButtons($fromHtmlDir); ?>
      </div>
    </div>
  </div>
</header>
    <?php
}
