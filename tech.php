<?php
declare(strict_types=1);

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>405 — Método Não Permitido</title>
      <link rel="stylesheet" href="style.css">
    </head>
    <body>
      <main class="container">
        <section class="card">
          <h1>405 — Method Not Allowed</h1>
          <p class="error-message">Acesso bloqueado: este endpoint aceita apenas requisições enviadas via método POST.</p>
          <p style="margin-top: 1rem;"><a href="index.html">Voltar ao formulário de inscrição</a></p>
        </section>
      </main>
    </body>
    </html>
    <?php
    exit;
}

$niveisPermitidos = [
    'iniciante' => 'Iniciante',
    'intermediario' => 'Intermediário',
    'avancado' => 'Avançado',
];

$turnosPermitidos = [
    'manha' => 'Manhã',
    'noite' => 'Noite',
];

$oficinasPermitidas = [
    'html_css' => 'HTML5 & CSS3',
    'js_dom' => 'JavaScript & DOM',
    'php_backend' => 'PHP Backend',
];

// 1. Normalização
$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$experiencia = trim((string) ($_POST['experiencia'] ?? ''));
$turno = trim((string) ($_POST['turno'] ?? ''));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));
$oficinasBrutas = $_POST['oficinas'] ?? [];

// 2. Validação no servidor
$erros = [];

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'O nome completo deve ter pelo menos 3 caracteres.';
}

if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $erros[] = 'Informe um endereço de e-mail válido.';
}

if ($experiencia === '' || !in_array($experiencia, array_keys($niveisPermitidos), true)) {
    $erros[] = 'Selecione um nível de experiência válido.';
}

if ($turno === '' || !in_array($turno, array_keys($turnosPermitidos), true)) {
    $erros[] = 'Selecione um turno de preferência válido.';
}

$oficinasValidas = [];
if (!is_array($oficinasBrutas) || count($oficinasBrutas) === 0) {
    $erros[] = 'Selecione ao menos uma oficina de interesse.';
} else {
    foreach ($oficinasBrutas as $item) {
        if (!is_string($item) || !in_array($item, array_keys($oficinasPermitidas), true)) {
            $erros[] = 'Foi enviada uma oficina inválida fora da lista permitida.';
            break;
        }
        if (!in_array($item, $oficinasValidas, true)) {
            $oficinasValidas[] = $item;
        }
    }
}

if (mb_strlen($mensagem, 'UTF-8') > 200) {
    $erros[] = 'A mensagem/motivação deve ter no máximo 200 caracteres.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado da Inscrição — Tech Experience 2026</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topo">
    <div class="container">
      <h1>Tech Experience 2026</h1>
      <p>Processamento da inscrição nas oficinas técnicas</p>
    </div>
  </header>

  <main class="container">
    <section class="card">
      <?php if (!empty($erros)): ?>
        <h2>Não foi possível concluir a inscrição</h2>
        <ul style="margin: 1rem 0 1.25rem 1.25rem;">
          <?php foreach ($erros as $erro): ?>
            <li class="error-message"><?= escapar($erro) ?></li>
          <?php endforeach; ?>
        </ul>
        <a href="index.html">← Voltar para o formulário</a>
      <?php else: ?>
        <h2>Inscrição confirmada com sucesso!</h2>
        <p style="margin-bottom: 1rem;">Confira abaixo os dados registrados:</p>

        <dl style="display: grid; gap: 0.65rem; margin-bottom: 1.5rem;">
          <div>
            <dt style="font-weight: 700;">Nome completo</dt>
            <dd><?= escapar($nome) ?></dd>
          </div>
          <div>
            <dt style="font-weight: 700;">E-mail</dt>
            <dd><?= escapar($email) ?></dd>
          </div>
          <div>
            <dt style="font-weight: 700;">Nível de experiência</dt>
            <dd><?= escapar($niveisPermitidos[$experiencia]) ?></dd>
          </div>
          <div>
            <dt style="font-weight: 700;">Turno de preferência</dt>
            <dd><?= escapar($turnosPermitidos[$turno]) ?></dd>
          </div>
          <div>
            <dt style="font-weight: 700;">Oficinas selecionadas</dt>
            <dd>
              <?php
              $nomesOficinas = array_map(
                  static fn(string $chave): string => escapar($oficinasPermitidas[$chave]),
                  $oficinasValidas
              );
              echo implode(', ', $nomesOficinas);
              ?>
            </dd>
          </div>
          <div>
            <dt style="font-weight: 700;">Mensagem / Motivação</dt>
            <dd><?= $mensagem !== '' ? nl2br(escapar($mensagem)) : 'Nenhuma mensagem informada.' ?></dd>
          </div>
        </dl>

        <a href="index.html">← Realizar nova inscrição</a>
      <?php endif; ?>
    </section>
  </main>

  <footer class="rodape">
    <div class="container">
      <p>Tech Experience 2026 — Portal Acadêmico de Oficinas</p>
    </div>
  </footer>
</body>
</html>
