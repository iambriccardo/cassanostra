<? if (session_status() == PHP_SESSION_ACTIVE && $_SESSION["role"] === "ADM"): ?>

<p>Pagina dell'amministratore</p>

<? endif; ?>