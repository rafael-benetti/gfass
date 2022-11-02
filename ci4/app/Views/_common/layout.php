<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="assets/imagens/favicon.png"/>
    <title>GFASS - Sistema de Controle Financeiro Simples</title>
    <?php echo link_tag('assets/bootstrap/css/bootstrap.min.css') ?>
    <script src="<?php echo base_url('assets/jquery/jquery-3.5.1.min.js') ?>"></script>
    <style>
        body {
            padding-top: 80px;
        }

        .logo_site {
            height: 40px;
            margin: 0;
        }
    </style>
    <script>
        var base_url = "<?php echo base_url() ?>";
    </script>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a href="<?php echo base_url() ?>" class="navbar-brand"><img src="<?php echo base_url('assets/imagens/logo_php_exp_white.png') ?>" alt="Logo do GFASS" class="logo_site"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a href="<?php echo base_url() ?>" class="nav-link <?php echo checkLinkPermission('home', 'index') ?>">Home</a></li>
                <li class="nav-item"><a href="<?php echo base_url('lancamento') ?>" class="nav-link <?php echo checkLinkPermission('lancamento', 'index') ?>">Lançamentos</a></li>
                <li class="nav-item"><a href="<?php echo base_url('categoria') ?>" class="nav-link <?php echo checkLinkPermission('categoria', 'index') ?>">Categorias</a></li>
                <li class="nav-item"><a href="<?php echo base_url('orcamento') ?>" class="nav-link <?php echo checkLinkPermission('orcamento', 'index') ?>">Orçamentos</a></li>
                <li class="nav-item"><a href="<?php echo base_url('relatorio') ?>" class="nav-link <?php echo checkLinkPermission('relatorio', 'index') ?>">Relatório</a></li>
                <span class="navbar-text <?php echo checkLinkPermission() ?>">
                    <span class="text-white font-weight-bold">Bem-vindo <?php echo session()->nome_usuario ?></span>
                </span>
                <?php if (session()->isLoggedAdmin) : ?>
                    <li class="nav-item"><a href="<?php echo base_url('admin/home') ?>" class="nav-link">Área Administrativa</a></li>
                <?php endif; ?>

            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo checkLinkPermission('home', 'index') ?>" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Configurações
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item <?php echo checkLinkPermission('perfil', 'index') ?>" href="<?php echo base_url('perfil') ?>">Perfis</a>
                        <a class="dropdown-item <?php echo checkLinkPermission('usuario', 'index') ?>" href="<?php echo base_url('usuario') ?>">Usuários</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo checkLinkPermission('home', 'index') ?>" href="<?php echo base_url('login/signout') ?>">Sair</a>
                </li>

            </ul>
        </div>
    </nav>
    <div class="container" role="main">
        <?php echo $this->renderSection('content') ?>
    </div>

    <script src="<?php echo base_url('assets/jquery.mask/jquery.mask.js') ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
</body>

</html>