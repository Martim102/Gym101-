// Caminho base da API. Se mudares o nome da pasta do projeto no XAMPP, ajusta aqui.
const API = "http://localhost/gym100/backend";

async function chamarApi(caminho, opcoes = {}) {
    const resposta = await fetch(`${API}${caminho}`, {
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        ...opcoes
    });
    const dados = await resposta.json().catch(() => ({}));
    if (!resposta.ok) {
        throw new Error(dados.erro || "Ocorreu um erro inesperado.");
    }
    return dados;
}

// Garante que só quem tem sessão ativa vê as páginas internas.
async function protegerPagina() {
    try {
        const info = await chamarApi("/auth/sessao.php");
        if (!info.autenticado) {
            window.location.href = "index.html";
            return null;
        }
        const elNome = document.getElementById("nomeUtilizadorNav");
        if (elNome) elNome.textContent = info.nome;
        const elInicial = document.getElementById("inicialAvatar");
        if (elInicial) elInicial.textContent = info.nome.charAt(0).toUpperCase();

        const destinoIdioma = document.getElementById("seletorIdiomaDestino");
        if (destinoIdioma && typeof inserirSeletorIdioma === "function") {
            inserirSeletorIdioma(destinoIdioma);
        }

        return info;
    } catch {
        window.location.href = "index.html";
        return null;
    }
}

async function terminarSessao() {
    await chamarApi("/auth/logout.php", { method: "POST" });
    window.location.href = "index.html";
}

function formatarData(dataIso) {
    const [ano, mes, dia] = dataIso.split("-");
    return `${dia}/${mes}/${ano}`;
}

function mostrarErro(elemento, texto) {
    elemento.textContent = texto;
    elemento.style.display = "block";
    setTimeout(() => { elemento.style.display = "none"; }, 5000);
}
