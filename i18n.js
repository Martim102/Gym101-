// Sistema de idiomas do Gym101%.
// Guarda a escolha do utilizador no localStorage, para se manter entre páginas.

function idiomaAtual() {
    return localStorage.getItem("gym100_idioma") || "pt";
}

function t(chave) {
    const dicionario = TRADUCOES[idiomaAtual()] || TRADUCOES.pt;
    return dicionario[chave] || TRADUCOES.pt[chave] || chave;
}

function aplicarTraducoes() {
    document.querySelectorAll("[data-i18n]").forEach(el => {
        el.textContent = t(el.getAttribute("data-i18n"));
    });
    document.querySelectorAll("[data-i18n-placeholder]").forEach(el => {
        el.setAttribute("placeholder", t(el.getAttribute("data-i18n-placeholder")));
    });
    document.documentElement.lang = idiomaAtual() === "pt" ? "pt-PT" : idiomaAtual();

    const seletor = document.getElementById("seletorIdioma");
    if (seletor) seletor.value = idiomaAtual();
}

function mudarIdioma(novoIdioma) {
    localStorage.setItem("gym100_idioma", novoIdioma);
    aplicarTraducoes();
    // avisa a página para poder voltar a desenhar listas/tabelas já traduzidas
    document.dispatchEvent(new Event("idiomaMudou"));
}

function inserirSeletorIdioma(elementoDestino) {
    const seletor = document.createElement("select");
    seletor.id = "seletorIdioma";
    seletor.className = "seletor-idioma";
    seletor.innerHTML = `
        <option value="pt">PT</option>
        <option value="en">EN</option>
        <option value="es">ES</option>
    `;
    seletor.value = idiomaAtual();
    seletor.addEventListener("change", (e) => mudarIdioma(e.target.value));
    elementoDestino.appendChild(seletor);
}

document.addEventListener("DOMContentLoaded", aplicarTraducoes);
