
// dados pet shop
const petShops = [
    {
        //api whatzaap webs 
        id: 1,
        nome: "HAI Saúde Pet",
        distancia: 1.2,
        servicos: [
            { nome: "Banho", preco: 45 },
            { nome: "Tosa", preco: 60 },
            { nome: "Vacinação", preco: null },
            { nome: "Consulta", preco: 80 }
        ],
        site: "https://haisaudepet.com.br/",
        whatsapp: "5511910001149"
    },
    {
        id: 2,
        nome: "Planet Pet Center",
        distancia: 2.5,
        servicos: [
            { nome: "Banho", preco: 40 },
            { nome: "Tosa", preco: 55 },
            { nome: "Vacinação", preco: 70 },
            { nome: "Consulta", preco: 90 }
        ],
        site: "http://www.planetpetcenter.com.br/",
        whatsapp: "5511952007000"
    },
    {
        id: 3,
        nome: "NippoVet Hospital Veterinário",
        distancia: 0.8,
        servicos: [
            { nome: "Banho", preco: 50 },
            { nome: "Tosa", preco: 65 },
            { nome: "Vacinação", preco: 75 },
            { nome: "Consulta", preco: 85 }
        ],
        site: "https://nippovet.com.br/",
        whatsapp: "5511941177957"
    },
    {
        id: 4,
        nome: "New Dog Adestramento e hospedagem",
        distancia: 3.1,
        servicos: [
            { nome: "Banho", preco: 60 },
            { nome: "Tosa", preco: 80 },
            { nome: "Vacinação", preco: null },
            { nome: "Consulta", preco: 120 }
        ],
        site: "https://newdog-adestramento.blogspot.com/",
        whatsapp: "5501123121604"
    },
    {
        id: 5,
        nome: "Banho e Tosa - Bixos Estética Animal / Pet Shop - Mogi das Cruzes - Mogilar",
        distancia: 1.8,
        servicos: [
            { nome: "Banho", preco: null },
            { nome: "Tosa", preco: null },
            { nome: "Vacinação", preco: 65 },
            { nome: "Consulta", preco: 75 }
        ],
        site: "https://banhoetosabixos.com.br/",
        whatsapp: "5511953203632"
    },
    {
        id: 6,
        nome: "Clínica Veterinária Golden Dog Mogi",
        distancia: 2.9,
        servicos: [
            { nome: "Banho", preco: 35 },
            { nome: "Tosa", preco: 50 },
            { nome: "Vacinação", preco: 60 },
            { nome: "Consulta", preco: 70 }
        ],
        site: "https://goldendogmogi.com.br/",
        whatsapp: "551147273745"
    }
];

// ===================== ELEMENTOS DO DOM =====================
const containerResultados = document.getElementById('container-resultados');
const botoesOrdenacao = document.querySelectorAll('.botao-ordenacao');
const botaoBusca = document.getElementById('botao-busca');
const selectServico = document.getElementById('servico');
const contadorResultados = document.getElementById('contador-resultados');

//funcoes

// Renderiza os pet shops na tela
function renderizarPetShops(lista) {
    containerResultados.innerHTML = '';

    if (lista.length === 0) {
        containerResultados.innerHTML = '<p>Nenhum pet shop encontrado.</p>';
        contadorResultados.textContent = 0;
        return;
    }

    lista.forEach(pet => {
        let servicosHTML = '';
        pet.servicos.forEach(servico => {
            const precoDisplay = servico.preco ? `R$ ${servico.preco.toFixed(2)}` : '<span class="na">N/A</span>';
            servicosHTML += `<div class="servico"><span>${servico.nome}</span>: <span>${precoDisplay}</span></div>`;
        });

        const card = document.createElement('div');
        card.className = 'card-petshop';
        card.innerHTML = `
            <div class="cabecalho-card">${pet.nome}</div>
            <div class="corpo-card">
                <p>Distância: ${pet.distancia} km</p>
                ${servicosHTML}
            </div>
            <div class="rodape-card">
                <a href="${pet.site}" target="_blank" class="btn-visitar">Visitar site</a>
                <a href="https://api.whatsapp.com/send/?phone=${pet.whatsapp}" target="_blank" class="btn-ligar">WhatsApp</a>
            </div>
        `;

        containerResultados.appendChild(card);
    });

    contadorResultados.textContent = lista.length;
}

// Calcula preço medio de um pet shop (ignora serviços sem preço)
function calcularPrecoMedio(pet) {
    const precos = pet.servicos.map(s => s.preco).filter(p => p !== null);
    return precos.length ? precos.reduce((a, b) => a + b, 0) / precos.length : 0;
}

// Ordena pet shops por distância ou preço
function ordenarPetShops(tipo, lista) {
    const ordenado = [...lista];
    switch (tipo) {
        case 'preco-crescente':
            ordenado.sort((a, b) => calcularPrecoMedio(a) - calcularPrecoMedio(b));
            break;
        case 'preco-decrescente':
            ordenado.sort((a, b) => calcularPrecoMedio(b) - calcularPrecoMedio(a));
            break;
        default: // distancia
            ordenado.sort((a, b) => a.distancia - b.distancia);
            break;
    }
    return ordenado;
}

// Filtra pet shops pelo serviço selecionado
function filtrarPorServico(servico, lista) {
    if (servico === 'todos') return lista;

    const mapServico = {
        'banho': 'Banho',
        'tosa': 'Tosa',
        'vacina': 'Vacinação',
        'consulta': 'Consulta',
        'hospedagem': 'Hospedagem'
    };

    return lista.filter(pet =>
        pet.servicos.some(s => s.nome === mapServico[servico] && s.preco !== null)
    );
}

// tualiza exibição de resultados
function atualizarDisplay() {
    const filtrado = filtrarPorServico(selectServico.value, petShops);
    const botaoAtivo = document.querySelector('.botao-ordenacao.ativo');
    const tipoOrdenacao = botaoAtivo ? botaoAtivo.dataset.sort : 'distancia';
    const ordenado = ordenarPetShops(tipoOrdenacao, filtrado);
    renderizarPetShops(ordenado);
}

// eventosd ....

// botoes de ordenacao
botoesOrdenacao.forEach(btn => {
    btn.addEventListener('click', () => {
        botoesOrdenacao.forEach(b => b.classList.remove('ativo'));
        btn.classList.add('ativo');
        atualizarDisplay();
    });
});

// Bobusca
botaoBusca.addEventListener('click', atualizarDisplay);

// Renderização inicial
document.addEventListener('DOMContentLoaded', () => {
    const inicial = ordenarPetShops('distancia', petShops);
    renderizarPetShops(inicial);
});