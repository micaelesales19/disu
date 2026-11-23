<?= $this->extend('layouts/main') ?>

<?php
$edicao = $edicao ?? false;
$empresa = $empresa ?? [];

$valor = static function (string $campo, $padrao = '') use ($empresa) {
    return old($campo, $empresa[$campo] ?? $padrao);
};
?>

<?= $this->section('content') ?>

<div class="main-breadcrumb d-flex align-items-center justify-content-between my-3">
    <div>
        <h2 class="breadcrumb-title mb-1 fs-18">
            <?= $edicao ? 'Editar Empresa' : 'Nova Empresa' ?>
        </h2>
        <p class="text-muted mb-0 fs-12">
            <?= $edicao ? 'Edite' : 'Cadastre' ?> as informações da empresa
        </p>
    </div>

    <a href="<?= site_url('clientes/empresas') ?>"
        class="btn btn-light border">
        <i class="ri-arrow-left-line me-1"></i>
        Voltar
    </a>
</div>

<?php if (session('error')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="ri-error-warning-line me-2"></i>
        <?= esc(session('error')) ?>
    </div>
<?php endif; ?>

<?php if (session('errors')): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Verifique os dados informados:</strong>

        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $erro): ?>
                <li><?= esc($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>


<form id="formEmpresa" action="<?= $edicao
                                    ? site_url('clientes/empresas/atualizar/' . $empresa['id'])
                                    : site_url('clientes/empresas/salvar') ?>" method="POST">

    <?= csrf_field() ?>


    <!-- ORIENTAÇÕES PARA CADASTRO -->
    <div class="alert border rounded-3 mb-4"
        style="background-color: #eef4ff; border-color: #d8e4f5 !important; color: #183b70;"
        role="note">

        <?php
        if ($edicao) {
        } else {
        ?>

            <h6 class="fw-bold mb-2">
                <i class="ri-information-line me-1"></i>
                Como cadastrar com rapidez
            </h6>

            <ol class="mb-0 ps-3" style="line-height: 1.9;">
                <li>
                    Digite o <strong>CNPJ</strong> e use a lupa
                    para consultar automaticamente os dados cadastrais
                    (quando disponíveis).
                </li>

                <li>
                    Confira e ajuste <strong>Nome</strong>,
                    nome fantasia e razão social, caso necessário.
                </li>

                <li>
                    O campo <strong>Nome *</strong> é obrigatório
                    e será utilizado na lista de clientes.
                </li>
            </ol>

    </div>

<?php } ?>


<!-- ABAS -->
<ul class="nav nav-tabs mb-4" id="abasEmpresa" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link active"
            id="tab-basicos"
            data-bs-toggle="tab"
            data-bs-target="#dadosBasicos"
            type="button"
            role="tab"
            aria-controls="dadosBasicos"
            aria-selected="true">
            <i class="ri-building-line me-1"></i>
            Dados Básicos
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
            id="tab-fiscais"
            data-bs-toggle="tab"
            data-bs-target="#dadosFiscais"
            type="button"
            role="tab"
            aria-controls="dadosFiscais"
            aria-selected="false">
            <i class="ri-file-list-3-line me-1"></i>
            Dados Fiscais e Contábeis
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
            id="tab-documentos"
            data-bs-toggle="tab"
            data-bs-target="#dadosDocumentos"
            type="button"
            role="tab"
            aria-controls="dadosDocumentos"
            aria-selected="false">
            <i class="ri-folder-line me-1"></i>
            Documentos
        </button>
    </li>

</ul>

<div class="tab-content" id="conteudoEmpresa">

    <!-- ABA 1: DADOS BÁSICOS -->
    <div class="tab-pane fade show active"
        id="dadosBasicos"
        role="tabpanel"
        aria-labelledby="tab-basicos">

        <div class="card mb-4">
            <div class="card-body">

                <div class="col-12">
                    <h5 class="fw-semibold mb-2">
                        Identificação
                    </h5>
                    <p class="text-muted mb-3">
                        Dados cadastrais. A busca por CNPJ preenche vários campos; você pode editar antes de salvar.
                    </p>
                </div>

                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="cpf_cnpj" class="form-label fw-medium">
                            CPF/CNPJ
                        </label>

                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="cpf_cnpj"
                                name="cpf_cnpj"
                                value="<?= esc($valor('cpf_cnpj'), 'attr') ?>"
                                placeholder="00.000.000/0000-00"
                                aria-describedby="mensagemCnpj">

                            <button
                                type="button"
                                class="btn btn-primary px-3"
                                id="btnConsultarCnpj"
                                title="Consultar dados do CNPJ"
                                aria-label="Consultar dados do CNPJ">

                                <i class="ri-search-line fs-16"></i>
                            </button>
                        </div>

                        <div
                            id="mensagemCnpj"
                            class="form-text mt-1"
                            role="status"
                            aria-live="polite">
                        </div>
                    </div>


                    <div class="col-md-4">
                        <label class="form-label">Código interno</label>
                        <input type="text" name="codigo_interno" value="<?= esc($valor('codigo_interno'), 'attr') ?>"
                            class="form-control" placeholder="Ex.: 0042">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome"
                            class="form-control" value="<?= esc($valor('nome'), 'attr') ?>" required>

                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nome fantasia</label>
                        <input type="text" name="nome_fantasia" value="<?= esc($valor('nome_fantasia'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Razão social</label>
                        <input type="text" name="razao_social" value="<?= esc($valor('razao_social'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contador responsável</label>
                        <input type="text" name="contador_responsavel" value="<?= esc($valor('contador_responsavel'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data de entrada</label>
                        <input type="date" name="data_entrada" value="<?= esc($valor('data_entrada'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Data de saída</label>
                        <input type="date" name="data_saida" value="<?= esc($valor('data_saida'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="ativo"
                                value="1"
                                id="empresaAtiva"
                                <?= (int) $valor('ativo', 1) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                for="empresaAtiva">
                                Empresa ativa
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input"
                                type="checkbox"
                                name="vip" value="1"
                                id="empresaVip" <?= (int) $valor('vip', 0) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                for="empresaVip">
                                Cliente VIP
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- CONTATO -->
        <div class="card mb-4">

            <div class="card-body">
                <div class="col-12">
                    <h5 class="fw-semibold mb-2">
                        Contato
                    </h5>
                    <p class="text-muted mb-3">
                        E-mails e telefones. Ao escolher uma sugestão de e-mail do navegador, só estes campos devem ser afetados.
                    </p>
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">E-mail</label>
                        <input type="email" name="email" value="<?= esc($valor('email'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">E-mail de cobrança</label>
                        <input type="email" name="email_cobranca" value="<?= esc($valor('email_cobranca'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telefone</label>
                        <input type="tel"
                            name="telefone"
                            id="telefone"
                            class="form-control"
                            placeholder="(00) 00000-0000"
                            maxlength="15"
                            value="<?= esc($valor('telefone'), 'attr') ?>"
                            inputmode="numeric">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">WhatsApp</label>
                        <input type="tel"
                            name="whatsapp"
                            id="whatsapp"
                            class="form-control"
                            placeholder="(00) 00000-0000"
                            maxlength="15"
                            value="<?= esc($valor('whatsapp'), 'attr') ?>"
                            inputmode="numeric">
                    </div>

                </div>
            </div>
        </div>

        <!-- ENDEREÇO -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="col-12">
                    <h5 class="fw-semibold mb-2">
                        Endereço
                    </h5>
                    <p class="text-muted mb-3">
                        Ao sair do CEP com 8 dígitos, buscamos logradouro, bairro, cidade e UF automaticamente.
                    </p>
                </div>
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">CEP</label>
                        <input type="text"
                            name="cep"
                            id="cep"
                            class="form-control"
                            placeholder="00000-000"
                            maxlength="9"
                            value="<?= esc($valor('cep'), 'attr') ?>"
                            inputmode="numeric">

                        <div id="mensagemCep"
                            class="form-text"
                            role="status"
                            aria-live="polite"></div>
                    </div>

                    <div class="col-md-7">
                        <label class="form-label">Logradouro</label>
                        <input type="text" name="logradouro" value="<?= esc($valor('logradouro'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Número</label>
                        <input type="text" name="numero" value="<?= esc($valor('numero'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Complemento</label>
                        <input type="text" name="complemento"
                            class="form-control"
                            value="<?= esc($valor('complemento'), 'attr') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Bairro</label>
                        <input type="text" name="bairro" value="<?= esc($valor('bairro'), 'attr') ?>"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cidade</label>
                        <input type="text" name="cidade"
                            class="form-control"
                            value="<?= esc($valor('cidade'), 'attr') ?>">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">UF</label>
                        <input type="text" name="uf"
                            class="form-control" value="<?= esc($valor('uf'), 'attr') ?>"
                            maxlength="2">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observações internas</label>
                        <textarea name="observacoes_internas"
                            class="form-control"
                            rows="3"><?= esc($valor('observacoes_internas')) ?></textarea>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- ABA 2: DADOS FISCAIS E CONTÁBEIS -->
    <div class="tab-pane fade"
        id="dadosFiscais"
        role="tabpanel"
        aria-labelledby="tab-fiscais">

        <div class="card mb-4">
            <div class="card-body">
                <div class="col-12">
                    <h5 class="fw-semibold mb-2">
                        Dados Fiscais
                    </h5>
                    <p class="text-muted mb-3">
                        Regime tributário, inscrições e informações fiscais da empresa.
                    </p>
                </div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Regime tributário</label>
                        <select name="regime_tributario" class="form-select">
                            <option value="">Não informado</option>

                            <?php foreach (
                                [
                                    'simples_nacional' => 'Simples Nacional',
                                    'lucro_presumido'  => 'Lucro Presumido',
                                    'lucro_real'       => 'Lucro Real',
                                    'pessoa_fisica'    => 'Pessoa Física',
                                    'simei'            => 'Simei',
                                ] as $codigo => $descricao
                            ): ?>

                                <option value="<?= esc($codigo, 'attr') ?>"
                                    <?= $valor('regime_tributario') === $codigo ? 'selected' : '' ?>>
                                    <?= esc($descricao) ?>
                                </option>

                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plano da empresa</label>
                        <input type="text" name="plano_empresa"
                            class="form-control" placeholder="Ex.: carneleao, mei, start, pleno" value="<?= esc($valor('plano_empresa'), 'attr') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Inscrição municipal</label>
                        <input type="text" name="inscricao_municipal"
                            class="form-control" value="<?= esc($valor('inscricao_municipal'), 'attr') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Inscrição estadual</label>
                        <input type="text" name="inscricao_estadual"
                            class="form-control" value="<?= esc($valor('inscricao_estadual'), 'attr') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Capital social</label>
                        <input type="number" name="capital_social"
                            class="form-control"
                            step="0.01" min="0" placeholder='Ex.: "10000.00"' value="<?= esc($valor('capital_social'), 'attr') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Informações fiscais
                        </label>
                        <textarea name="informacoes_fiscais"
                            class="form-control"
                            rows="3" placeholder="Dados fiscais, tributação, anexos, etc."><?= esc($valor('informacoes_fiscais')) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                name="possui_funcionarios"
                                value="1"
                                class="form-check-input"
                                id="possuiFuncionarios"
                                <?= (int) $valor('possui_funcionarios', 0) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                for="possuiFuncionarios">
                                Possui funcionários
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                name="possui_extrato_bancario"
                                value="1"
                                class="form-check-input"
                                id="possuiExtrato"
                                <?= (int) $valor('possui_extrato_bancario', 0) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                for="possuiExtrato">
                                Possui extrato bancário
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                name="importar_notas_sieg"
                                value="1"
                                class="form-check-input"
                                id="importarSieg" <?= (int) $valor('importar_notas_sieg', 0) === 1 ? 'checked' : '' ?>>
                            <label class="form-check-label"
                                for="importarSieg">
                                Importar notas SIEG
                            </label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="col-12">

                    <h5 class="fw-semibold mb-2">
                        Dados contábeis e DP
                    </h5>

                    <p class="text-muted mb-3">
                        Observações contábeis e informações de departamento pessoal.
                    </p>

                </div>

                <!-- DADOS CONTÁBEIS -->
                <div class="col-12">
                    <label for="dados_contabeis" class="form-label">
                        Dados contábeis
                    </label>

                    <textarea
                        name="dados_contabeis"
                        id="dados_contabeis"
                        class="form-control"
                        rows="4"
                        placeholder="Observações e dados contábeis do cliente"><?= esc($valor('dados_contabeis')) ?></textarea>
                </div>

                <!-- DEPARTAMENTO PESSOAL -->
                <div class="col-12">
                    <label for="dados_dp" class="form-label">
                        Dados departamento pessoal (DP)
                    </label>

                    <textarea
                        name="dados_dp"
                        id="dados_dp"
                        class="form-control"
                        rows="4"
                        placeholder="Folha de pagamento, eSocial, admissões, etc."><?= esc($valor('dados_dp')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- ABA 3: DOCUMENTOS -->
    <div class="tab-pane fade"
        id="dadosDocumentos"
        role="tabpanel"
        aria-labelledby="tab-documentos">

        <div class="card mb-4">
            <div class="card-body">
                <div class="col-12">
                    <h5 class="fw-semibold mb-2">
                        Documentos
                    </h5>
                    <p class="text-muted mb-3">
                        URLs de contrato social e cartão CNPJ. Você pode anexar os arquivos após criar a empresa.
                    </p>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">
                            URL Contrato social
                        </label>
                        <input type="url"
                            name="url_contrato_social"
                            class="form-control"
                            placeholder="https://..." value="<?= esc($valor('url_contrato_social'), 'attr') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            URL Cartão CNPJ
                        </label>
                        <input type="url"
                            name="url_cartao_cnpj"
                            class="form-control"
                            placeholder="https://..." value="<?= esc($valor('url_cartao_cnpj'), 'attr') ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- AÇÕES -->
<div class="d-flex justify-content-end gap-2 mb-4">

    <a href="<?= site_url('clientes/empresas') ?>"
        class="btn btn-light border">
        Cancelar
    </a>

    <button type="submit"
        class="btn btn-primary"
        id="btnSalvarEmpresa">
        <i class="ri-save-line me-1"></i>
        <?= $edicao ? 'Salvar Alterações' : 'Salvar Empresa' ?>
    </button>
</div>

</form>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('formEmpresa');

        // ==========================================
        // MÁSCARAS DE TELEFONE E WHATSAPP
        // ==========================================

        function mascaraTelefone(valor) {
            const numeros = valor.replace(/\D/g, '').slice(0, 11);

            if (numeros.length <= 2) {
                return numeros.length === 2 ?
                    `(${numeros}) ` :
                    numeros;
            }

            const ddd = numeros.slice(0, 2);
            const restante = numeros.slice(2);

            if (numeros.length <= 10) {
                return `(${ddd}) ${restante.slice(0, 4)}` +
                    (restante.length > 4 ?
                        '-' + restante.slice(4, 8) :
                        '');
            }

            return `(${ddd}) ${restante.slice(0, 5)}` +
                (restante.length > 5 ?
                    '-' + restante.slice(5, 9) :
                    '');
        }

        ['telefone', 'whatsapp'].forEach(function(id) {
            const campo = document.getElementById(id);

            campo.addEventListener('input', function() {
                campo.value = mascaraTelefone(campo.value);
                campo.setCustomValidity('');
            });

            campo.addEventListener('blur', function() {
                const numeros = campo.value.replace(/\D/g, '');

                if (numeros.length > 0 &&
                    numeros.length !== 10 &&
                    numeros.length !== 11) {
                    campo.setCustomValidity(
                        'Informe um telefone com DDD e 10 ou 11 dígitos.'
                    );
                } else {
                    campo.setCustomValidity('');
                }
            });
        });

        // ==========================================
        // MÁSCARA E CONSULTA AUTOMÁTICA DO CEP
        // ==========================================

        const campoCep = document.getElementById('cep');
        const mensagemCep = document.getElementById('mensagemCep');

        const camposEndereco = {
            logradouro: form.elements.namedItem('logradouro'),
            bairro: form.elements.namedItem('bairro'),
            cidade: form.elements.namedItem('cidade'),
            uf: form.elements.namedItem('uf')
        };

        let ultimaConsulta = '';
        let consultaAtual = null;

        function mensagem(texto, erro = false) {
            mensagemCep.textContent = texto;
            mensagemCep.classList.toggle('text-danger', erro);
            mensagemCep.classList.toggle('text-success', !erro && texto !== '');
        }

        function mascaraCep(valor) {
            const numeros = valor.replace(/\D/g, '').slice(0, 8);

            return numeros.length > 5 ?
                numeros.slice(0, 5) + '-' + numeros.slice(5) :
                numeros;
        }

        async function consultarCep() {
            const cep = campoCep.value.replace(/\D/g, '');

            if (cep.length !== 8) {
                if (cep.length > 0) {
                    mensagem('Informe um CEP com 8 dígitos.', true);
                } else {
                    mensagem('');
                }
                return;
            }

            if (cep === ultimaConsulta) return;

            if (consultaAtual) consultaAtual.abort();

            const controller = new AbortController();
            consultaAtual = controller;

            mensagem('Consultando CEP...');

            try {
                const resposta = await fetch(
                    `https://viacep.com.br/ws/${cep}/json/`, {
                        signal: controller.signal
                    }
                );

                if (!resposta.ok) {
                    throw new Error('Falha na consulta.');
                }

                const dados = await resposta.json();

                // Evita preencher o endereço se o CEP mudou
                // enquanto a consulta estava em andamento.
                if (campoCep.value.replace(/\D/g, '') !== cep) return;

                if (dados.erro) {
                    mensagem('CEP não encontrado.', true);
                    return;
                }

                camposEndereco.logradouro.value = dados.logradouro || '';
                camposEndereco.bairro.value = dados.bairro || '';
                camposEndereco.cidade.value = dados.localidade || '';
                camposEndereco.uf.value = dados.uf || '';

                ultimaConsulta = cep;

                mensagem('Endereço encontrado!');

                const numero = form.elements.namedItem('numero');
                numero.focus();

            } catch (erro) {
                if (erro.name !== 'AbortError') {
                    mensagem(
                        'Não foi possível consultar o CEP. Preencha o endereço manualmente.',
                        true
                    );
                }
            } finally {
                if (consultaAtual === controller) {
                    consultaAtual = null;
                }
            }
        }

        campoCep.addEventListener('input', function() {
            campoCep.value = mascaraCep(campoCep.value);
            ultimaConsulta = '';

            if (consultaAtual) {
                consultaAtual.abort();
            }

            if (campoCep.value.replace(/\D/g, '').length === 8) {
                consultarCep();
            } else {
                mensagem('');
            }
        });

        campoCep.addEventListener('blur', consultarCep);

    });

    document.getElementById('btnConsultarCnpj')
        .addEventListener('click', async function() {

            const botao = this;
            const campo = document.getElementById('cpf_cnpj');
            const mensagem = document.getElementById('mensagemCnpj');

            const cnpj = campo.value.replace(/\D/g, '');

            if (cnpj.length !== 14) {
                mensagem.textContent = 'Informe um CNPJ com 14 dígitos.';
                mensagem.className = 'form-text text-danger';
                return;
            }

            botao.disabled = true;
            botao.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            mensagem.textContent = 'Consultando CNPJ...';
            mensagem.className = 'form-text text-muted';

            try {
                const url = <?= json_encode(site_url('clientes/empresas/consultar-cnpj')) ?>;

                const resposta = await fetch(`${url}/${cnpj}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const resultado = await resposta.json();

                if (!resposta.ok || !resultado.sucesso) {
                    throw new Error(resultado.mensagem || 'Erro na consulta.');
                }

                const dados = resultado.dados;

                Object.entries(dados).forEach(([nome, valor]) => {
                    if (nome === 'cpf_cnpj' || valor === null || valor === '') {
                        return;
                    }

                    const input = document.querySelector(
                        `[name="${nome}"]`
                    );

                    if (input) {
                        input.value = valor;
                        input.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                mensagem.textContent = 'Dados da empresa encontrados e preenchidos!';
                mensagem.className = 'form-text text-success';

            } catch (erro) {
                mensagem.textContent = erro.message;
                mensagem.className = 'form-text text-danger';

            } finally {
                botao.disabled = false;
                botao.innerHTML = '<i class="ri-search-line"></i>';
            }
        });
</script>

<?= $this->endSection() ?>
