
/**
 * DISU - Funções monetárias reutilizáveis
 */

(function () {
    'use strict';

    // Converte "1.250,75" para 1250.75
    window.disuValorNumerico = function (valor) {
        if (typeof valor === 'number') {
            return Number.isFinite(valor) ? valor : 0;
        }

        const texto = String(valor ?? '')
            .trim()
            .replace(/R\$\s*/g, '')
            .replace(/\./g, '')
            .replace(',', '.');

        const numero = Number(texto);

        return Number.isFinite(numero) ? numero : 0;
    };

    // Formata um número vindo do banco.
    // Ex.: 1250.75 -> "1.250,75"
    window.disuFormatarValor = function (valor) {
        const numero = typeof valor === 'number'
            ? valor
            : Number(valor);

        return (Number.isFinite(numero) ? numero : 0)
            .toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    };

    // Ex.: 1250.75 -> "R$ 1.250,75"
    window.disuFormatarMoeda = function (valor) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    };

    // Converte "1.250,75" para "1250.75"
    window.disuMoedaParaDecimal = function (valor) {
        const texto = String(valor ?? '').trim();

        if (!texto) return '';

        return texto.replace(/\./g, '').replace(',', '.');
    };

    // Máscara durante a digitação.
    function aplicarMascara(campo) {
        const digitos = String(campo.value).replace(/\D/g, '');

        if (!digitos) {
            campo.value = '';
            return;
        }

        campo.value = (Number(digitos) / 100)
            .toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    }

    function iniciarMascaras() {
        document.querySelectorAll('.moeda-br').forEach(campo => {

            if (campo.dataset.mascaraIniciada === '1') return;

            campo.dataset.mascaraIniciada = '1';

            // Valores iniciais devem estar em formato brasileiro.
            // Ex.: "1.250,75", nunca "1250.75".
            aplicarMascara(campo);

            campo.addEventListener('input', function () {
                aplicarMascara(this);
            });
        });
    }

    window.disuIniciarMascarasMoeda = iniciarMascaras;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarMascaras);
    } else {
        iniciarMascaras();
    }

})();

document.addEventListener('DOMContentLoaded', function () {
    const campoEmpresa = document.getElementById('cliente_id');

    if (campoEmpresa) {

        if (campoEmpresa && typeof TomSelect !== 'undefined' && !campoEmpresa.tomselect) {
            new TomSelect(campoEmpresa, {
                create: false,
                maxItems: 1,
                placeholder: 'Digite para pesquisar uma empresa...',
                searchField: ['text'],
                allowEmptyOption: false,
                dropdownParent: 'body',

                sortField: {
                    field: 'text',
                    direction: 'asc'
                },

                render: {
                    no_results: function () {
                        return '<div class="no-results p-2 text-muted">Nenhuma empresa encontrada</div>';
                    }
                }
            });
        }
    }
});
