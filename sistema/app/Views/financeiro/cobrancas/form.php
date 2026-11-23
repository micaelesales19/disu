<?php

/**
 * @var array $clientes
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<link
    href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css"
    rel="stylesheet">

<style>
    /* Campo principal */
    #formCobranca .ts-wrapper .ts-control {
        background-color: var(--bs-body-bg) !important;
        border: 1px solid var(--bs-border-color) !important;
        color: var(--bs-body-color) !important;
        min-height: 38px;
    }

    #formCobranca .ts-wrapper .ts-control .item,
    #formCobranca .ts-wrapper .ts-control input {
        color: var(--bs-body-color) !important;
        -webkit-text-fill-color: var(--bs-body-color) !important;
    }

    #formCobranca .ts-dropdown,
    #formCobranca .ts-dropdown .option {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
    }

    #formCobranca .ts-dropdown .option.active {
        background-color: var(--bs-tertiary-bg) !important;
    }

    /* Lista suspensa, inclusive quando anexada ao body */
    .ts-dropdown {
        background-color: var(--bs-body-bg, #fff) !important;
        color: var(--bs-body-color, #212529) !important;
        border: 1px solid var(--bs-border-color, #dee2e6) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .15) !important;
        border-radius: 6px !important;
        z-index: 2050 !important;
        overflow: hidden;
    }

    .ts-dropdown .option,
    .ts-dropdown .no-results {
        background-color: var(--bs-body-bg, #fff) !important;
        color: var(--bs-body-color, #212529) !important;
        padding: 10px 12px !important;
    }

    .ts-dropdown .option.active,
    .ts-dropdown .option:hover {
        background-color: var(--bs-tertiary-bg, #eef4ff) !important;
        color: var(--bs-body-color, #212529) !important;
    }

    /* Validação */
    #formCobranca .campo-invalido {
        border-color: var(--bs-danger, #dc3545) !important;
        box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .12);
    }

    #formCobranca .ts-wrapper.campo-invalido .ts-control {
        border-color: var(--bs-danger, #dc3545) !important;
    }
</style>


<div class="container-fluid">

    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                <?= esc($titulo ?? 'Nova Cobrança') ?>
            </h4>
            <p class="text-muted mb-0">
                Preencha os dados para cadastrar uma nova cobrança.
            </p>
        </div>

        <a href="<?= site_url('financeiro/cobrancas') ?>"
            class="btn btn-light border">
            <i class="ri-arrow-left-line me-1"></i>
            Voltar
        </a>

    </div>

    <div class="card">
        <div class="card-body">
            <form
                id="formCobranca"
                action="<?= site_url('financeiro/cobrancas/salvar') ?>"
                method="POST">

                <?= csrf_field() ?>

                <div class="row g-3">

                    <!-- DESCRIÇÃO -->
                    <div class="col-12">
                        <label class="form-label">
                            Descrição / Observações
                        </label>

                        <textarea
                            name="descricao"
                            class="form-control"
                            rows="3"
                            placeholder="Ex.: Honorários contábeis de novembro"><?= esc(old('descricao', '')) ?></textarea>
                    </div>

                    <!-- EMPRESA -->
                    <div class="col-md-6">
                        <label for="cliente_id" class="form-label fw-medium">
                            Empresa *
                        </label>

                        <select
                            name="cliente_id"
                            id="cliente_id"
                            class="form-select"
                            required>

                            <option value="">Selecione ou pesquise uma empresa...</option>

                            <?php foreach ($clientes as $cliente): ?>
                                <option
                                    value="<?= (int) $cliente['id'] ?>"
                                    <?= (string) old('cliente_id', '') === (string) $cliente['id']
                                        ? 'selected'
                                        : '' ?>>
                                    <?= esc($cliente['nome']) ?>
                                    <?= !empty($cliente['cpf_cnpj'])
                                        ? ' - ' . esc($cliente['cpf_cnpj'])
                                        : '' ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                        <small class="text-muted">
                            Digite o nome ou CNPJ para localizar a empresa.
                        </small>
                    </div>

                    <!-- CONTRATO -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Contrato
                        </label>

                        <select
                            name="contrato_id"
                            id="contrato_id"
                            class="form-select"
                            disabled>

                            <option value="">
                                Selecione primeiro uma empresa
                            </option>

                        </select>

                        <small class="text-muted">
                            Opcional para cobranças avulsas.
                        </small>
                    </div>

                    <!-- VALOR -->
                    <div class="col-md-6">
                        <label for="valor" class="form-label">
                            Valor (R$) *
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">R$</span>

                            <input
                                type="text"
                                name="valor"
                                id="valor"
                                class="form-control moeda-br"
                                placeholder="0,00"
                                inputmode="numeric"
                                autocomplete="off"
                                value="<?= esc(old('valor', ''), 'attr') ?>"
                                required>
                        </div>
                    </div>

                    <!-- VENCIMENTO -->
                    <div class="col-md-6">
                        <label class="form-label">
                            Vencimento *
                        </label>

                        <input
                            type="date"
                            name="vencimento"
                            id="vencimento"
                            class="form-control"
                            value="<?= esc(old('vencimento', ''), 'attr') ?>"
                            required>
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">

                    <a href="<?= site_url('financeiro/cobrancas') ?>"
                        class="btn btn-light border">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        Salvar Cobrança
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('formCobranca');

        const campoEmpresa = document.getElementById('cliente_id');
        const campoContrato = document.getElementById('contrato_id');
        const campoDescricao = document.querySelector(
            '#formCobranca [name="descricao"]'
        );
        const campoValor = document.getElementById('valor');

        const urlContratos = <?= json_encode(
                                    site_url('financeiro/cobrancas/contratos-empresa')
                                ) ?>;


        let contratoAnterior = <?= json_encode(old('contrato_id', '')) ?>;

        const recuperandoFormulario = <?= json_encode(
                                            session()->has('_ci_old_input')
                                        ) ?>;

        let contratosCarregados = [];
        let requisicaoAtual = 0;

        // Inicializa a busca de empresas, caso o disu.js
        // ainda não tenha inicializado o TomSelect.
        if (typeof TomSelect !== 'undefined' && !campoEmpresa.tomselect) {

            new TomSelect(campoEmpresa, {
                create: false,
                maxItems: 1,
                placeholder: 'Digite para pesquisar uma empresa...',
                searchField: ['text'],
                allowEmptyOption: false,
                dropdownParent: 'body'
            });
        }

        function limparContratos(mensagem) {
            campoContrato.innerHTML = '';

            campoContrato.add(
                new Option(mensagem, '')
            );

            campoContrato.value = '';
            campoContrato.disabled = true;

            contratosCarregados = [];
        }

        async function carregarContratos(clienteId) {

            const numeroRequisicao = ++requisicaoAtual;

            limparContratos('Carregando contratos...');

            if (!clienteId) {
                limparContratos('Selecione primeiro uma empresa');
                return;
            }

            try {
                const resposta = await fetch(
                    urlContratos + '/' + encodeURIComponent(clienteId), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!resposta.ok) {
                    throw new Error('Erro ao consultar contratos.');
                }

                const contratos = await resposta.json();

                // Evita mostrar contratos de uma empresa anterior
                // caso o usuário troque a seleção rapidamente.
                if (numeroRequisicao !== requisicaoAtual) {
                    return;
                }

                contratosCarregados = contratos;

                campoContrato.innerHTML = '';

                campoContrato.add(
                    new Option('Nenhum contrato (cobrança avulsa)', '')
                );

                contratos.forEach(function(contrato) {

                    const texto = contrato.titulo +
                        ' - ' +
                        disuFormatarMoeda(Number(contrato.valor));

                    campoContrato.add(
                        new Option(texto, contrato.id)
                    );
                });

                campoContrato.disabled = false;

                if (contratoAnterior) {
                    campoContrato.value = String(contratoAnterior);
                }

                if (contratos.length === 0) {
                    campoContrato.options[0].text =
                        'Nenhum contrato ativo encontrado';
                }

            } catch (erro) {

                if (numeroRequisicao !== requisicaoAtual) {
                    return;
                }

                console.error(erro);

                limparContratos('Erro ao carregar contratos');
            }
        }

        // Guarda a empresa inicial e os dados recuperados
        // após uma falha de validação.
        const empresaInicial = campoEmpresa.value;
        const descricaoInicial = campoDescricao.value;
        const valorInicial = campoValor.value;

        campoEmpresa.dataset.empresaAnterior = empresaInicial;

        // Ao selecionar uma empresa.
        campoEmpresa.addEventListener('change', function() {

            const empresaAnterior = this.dataset.empresaAnterior || '';
            const novaEmpresa = this.value;

            if (empresaAnterior === novaEmpresa) {
                return;
            }

            this.dataset.empresaAnterior = novaEmpresa;

            // Preserva os dados recuperados após erro de validação.
            if (!recuperandoFormulario) {
                campoDescricao.value = '';
                campoValor.value = '';
            }

            contratoAnterior = '';

            carregarContratos(novaEmpresa);
        });

        // Ao selecionar um contrato.
        campoContrato.addEventListener('change', function() {

            const contrato = contratosCarregados.find(
                item => String(item.id) === String(this.value)
            );

            if (!contrato) {
                return;
            }

            // Preserva os dados recuperados após erro de validação.
            if (!recuperandoFormulario) {
                campoDescricao.value = contrato.titulo;

                campoValor.value = disuFormatarValor(
                    Number(contrato.valor)
                );
            }
        });

        // Caso a empresa já esteja selecionada ao abrir a página.
        if (campoEmpresa.value) {
            carregarContratos(campoEmpresa.value);
        }
    });
</script>

<?= $this->endSection() ?>
