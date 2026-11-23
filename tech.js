document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-inscricao");
  const mensagem = document.getElementById("mensagem");
  const contador = document.getElementById("contador-mensagem");
  const alertaJs = document.getElementById("alerta-js");
  const erroOficinas = document.getElementById("erro-oficinas");
  const checkboxesOficinas = Array.from(document.querySelectorAll('input[name="oficinas[]"]'));

  const atualizarContador = () => {
    if (!mensagem || !contador) return;
    const limite = mensagem.maxLength || 200;
    const atual = mensagem.value.length;
    const restante = Math.max(0, limite - atual);
    contador.textContent = `Restam ${restante} caracteres`;
  };

  if (mensagem) {
    atualizarContador();
    mensagem.addEventListener("input", atualizarContador);
  }

  checkboxesOficinas.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const algumaMarcada = checkboxesOficinas.some((item) => item.checked);
      if (algumaMarcada && erroOficinas) {
        erroOficinas.textContent = "";
      }
    });
  });

  if (form) {
    form.addEventListener("submit", (event) => {
      const algumaMarcada = checkboxesOficinas.some((item) => item.checked);

      if (!algumaMarcada) {
        event.preventDefault();
        if (erroOficinas) {
          erroOficinas.textContent = "Selecione pelo menos uma oficina de interesse.";
        }
        if (alertaJs) {
          alertaJs.hidden = false;
          alertaJs.textContent = "Atenção: escolha ao menos uma oficina antes de enviar a inscrição.";
        }
        if (checkboxesOficinas[0]) {
          checkboxesOficinas[0].focus();
        }
      } else if (alertaJs) {
        alertaJs.hidden = true;
        alertaJs.textContent = "";
      }
    });
  }
});
