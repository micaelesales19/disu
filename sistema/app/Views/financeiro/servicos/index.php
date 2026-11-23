<?php

/**
 * @var int $total
 * @var int $ativos
 * @var int $inativos
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- CABEÇALHO -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="mb-1">Serviços</h4>
            <p class="text-muted mb-0">
                Gerencie os serviços oferecidos pela contabilidade.
            </p>
        </div>

        <button type="button"
            class="btn btn-primary"
            id="btnNovoServico">
            <i class="ri-add-line me-1"></i>
            Novo Serviço
        </button>
    </div>

    <!-- MENSAGENS -->
    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('sucesso')) ?>
            <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('erro')) ?>
            <button type="button" class="btn-close"
                data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- INDICADORES -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-2">Total de serviços</p>
                        <h3 class="mb-0"><?= (int) $total ?></h3>
                    </div>
                    <i class="ri-file-list-3-line fs-2 text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-2">Serviços ativos</p>
                        <h3 class="mb-0"><?= (int) $ativos ?></h3>
                    </div>
                    <i class="ri-checkbox-circle-line fs-2 text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-2">Serviços inativos</p>
                        <h3 class="mb-0"><?= (int) $inativos ?></h3>
                    </div>
                    <i class="ri-forbid-line fs-2 text-secondary"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- TABELA -->
    <div class="card">

        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">Serviços cadastrados</h5>

            <div style="max-width:300px;width:100%;">
                <input type="search"
                    class="form-control"
                    id="buscarServico"
                    placeholder="Buscar serviço...">
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Serviço</th>
                            <th>Valor padrão</th>
                            <th>Unidade</th>
                            <th>NFS-e</th>
                            <th>Situação</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody id="tabelaServicos">

                        <?php if (empty($servicos)): ?>

                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                    Nenhum serviço cadastrado.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($servicos as $servico): ?>

                                <tr class="linha-servico">

                                    <td>
                                        <?= esc($servico['codigo_interno'] ?: '-') ?>
                                    </td>

                                    <td>
                                        <strong><?= esc($servico['nome']) ?></strong>

                                        <?php if (!empty($servico['descricao'])): ?>
                                            <div class="text-muted small">
                                                <?= esc($servico['descricao']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        R$ <?= number_format(
                                                (float) $servico['valor_padrao'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>
                                    </td>

                                    <td>
                                        <?= esc(ucfirst($servico['unidade'])) ?>
                                    </td>

                                    <td>
                                        <?php if ($servico['permite_nfse']): ?>
                                            <span class="badge bg-success-subtle text-success">
                                                Sim
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Não
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($servico['ativo']): ?>
                                            <span class="badge bg-success-subtle text-success">
                                                Ativo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Inativo
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">

                                        <button type="button"
                                            class="btn btn-sm btn-light btnEditarServico"
                                            title="Editar serviço"
                                            data-servico="<?= esc(
                                                                json_encode(
                                                                    $servico,
                                                                    JSON_HEX_TAG |
                                                                        JSON_HEX_APOS |
                                                                        JSON_HEX_AMP |
                                                                        JSON_HEX_QUOT
                                                                ),
                                                                'attr'
                                                            ) ?>">
                                            <i class="ri-edit-line"></i>
                                        </button>

                                        <?php if ($servico['ativo']): ?>

                                            <form action="<?= site_url(
                                                                'financeiro/servicos/desativar/' . $servico['id']
                                                            ) ?>"
                                                method="POST"
                                                class="d-inline formDesativar">

                                                <?= csrf_field() ?>

                                                <button type="submit"
                                                    class="btn btn-sm btn-light text-danger"
                                                    title="Desativar serviço">
                                                    <i class="ri-forbid-line"></i>
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

<!-- MODAL -->
<div class="modal fade"
    id="modalServico"
    tabindex="-1"
    aria-labelledby="modalServicoLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form id="formServico"
                action="<?= site_url('financeiro/servicos/salvar') ?>"
                method="POST">

                <?= csrf_field() ?>

                <input type="hidden" name="id" id="servicoId">

                <div class="modal-header">

                    <h5 class="modal-title" id="modalServicoLabel">
                        <i class="ri-file-add-line me-2"></i>
                        <span id="tituloModalServico">Novo Serviço</span>
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"></button>

                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label">
                                Nome do serviço <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control"
                                id="nomeServico"
                                name="nome"
                                maxlength="150"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Código interno</label>
                            <input type="text"
                                class="form-control"
                                id="codigoServico"
                                name="codigo_interno"
                                maxlength="30">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control"
                                id="descricaoServico"
                                name="descricao"
                                rows="3"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Valor padrão (R$)</label>
                            <input type="text"
                                class="form-control moeda-br"
                                id="valor"
                                name="valor_padrao"
                                inputmode="numeric"
                                min="0"
                                step="0.01">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Unidade de cobrança <span class="text-danger">*</span>
                            </label>

                            <select class="form-select"
                                id="unidadeServico"
                                name="unidade"
                                required>
                                <option value="servico">Por serviço</option>
                                <option value="mensalidade">Mensalidade</option>
                                <option value="hora">Por hora</option>
                                <option value="unidade">Por unidade</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h6>Configurações fiscais</h6>
                            <p class="text-muted small mb-0">
                                Informações para futura integração com NFS-e.
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Permite emissão de NFS-e?</label>
                            <select class="form-select"
                                id="emiteNfse"
                                name="permite_nfse">
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Código de serviço fiscal</label>
                            <input type="text"
                                class="form-control"
                                id="codigoFiscal"
                                name="codigo_fiscal"
                                maxlength="50">
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Situação</label>
                            <select class="form-select"
                                id="situacaoServico"
                                name="ativo">
                                <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        Salvar Serviço
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<?php
$reabrirModal = session()->getFlashdata('erro')
    && old('nome') !== null;
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modalElemento = document.getElementById('modalServico');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElemento);
        const form = document.getElementById('formServico');

        <?php if ($reabrirModal): ?>

            document.getElementById('servicoId').value =
                <?= json_encode(old('id') ?? '') ?>;

            document.getElementById('nomeServico').value =
                <?= json_encode(old('nome') ?? '') ?>;

            document.getElementById('codigoServico').value =
                <?= json_encode(old('codigo_interno') ?? '') ?>;

            document.getElementById('descricaoServico').value =
                <?= json_encode(old('descricao') ?? '') ?>;

            document.getElementById('valor').value =
                <?= json_encode(old('valor_padrao') ?? '') ?>;

            document.getElementById('unidadeServico').value =
                <?= json_encode(old('unidade') ?? 'servico') ?>;

            document.getElementById('emiteNfse').value =
                <?= json_encode(old('permite_nfse') ?? '0') ?>;

            document.getElementById('codigoFiscal').value =
                <?= json_encode(old('codigo_fiscal') ?? '') ?>;

            document.getElementById('situacaoServico').value =
                <?= json_encode(old('ativo') ?? '1') ?>;

            document.getElementById('tituloModalServico').textContent =
                document.getElementById('servicoId').value ?
                'Editar Serviço' :
                'Novo Serviço';

            modal.show();

        <?php endif; ?>

        // NOVO SERVIÇO
        document.getElementById('btnNovoServico').addEventListener('click', function() {

            form.reset();
            document.getElementById('servicoId').value = '';
            document.getElementById('tituloModalServico').textContent = 'Novo Serviço';

            modal.show();
        });

        // EDITAR SERVIÇO
        document.querySelectorAll('.btnEditarServico').forEach(function(botao) {

            botao.addEventListener('click', function() {

                const servico = JSON.parse(this.dataset.servico);

                form.reset();

                document.getElementById('servicoId').value = servico.id;
                document.getElementById('nomeServico').value = servico.nome ?? '';
                document.getElementById('codigoServico').value = servico.codigo_interno ?? '';
                document.getElementById('descricaoServico').value = servico.descricao ?? '';
                document.getElementById('valor').value = servico.valor_padrao ?? '';
                document.getElementById('unidadeServico').value = servico.unidade ?? 'servico';
                document.getElementById('emiteNfse').value = String(servico.permite_nfse);
                document.getElementById('codigoFiscal').value = servico.codigo_fiscal ?? '';
                document.getElementById('situacaoServico').value = String(servico.ativo);

                document.getElementById('tituloModalServico').textContent = 'Editar Serviço';

                modal.show();
            });
        });

        // PESQUISA
        document.getElementById('buscarServico').addEventListener('input', function() {

            const termo = this.value.toLocaleLowerCase('pt-BR');

            document.querySelectorAll('.linha-servico').forEach(function(linha) {
                const texto = linha.textContent.toLocaleLowerCase('pt-BR');
                linha.style.display = texto.includes(termo) ? '' : 'none';
            });
        });

        // CONFIRMAÇÃO DE DESATIVAÇÃO
        document.querySelectorAll('.formDesativar').forEach(function(formulario) {

            formulario.addEventListener('submit', function(event) {

                if (!confirm('Deseja realmente desativar este serviço?')) {
                    event.preventDefault();
                }
            });
        });

    });
</script>

<?= $this->endSection() ?>
