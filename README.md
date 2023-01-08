# :hamburger: FasTuga :hamburger: 

<p>Este projeto foi desenvolvido no âmbito da disciplina de <b>Desenvolvimento de Aplicações Distríbuidas</b> de Engenharia Informática no ano letivo 2022/2023.</p>

## :pencil: Introdução

O objetivo deste projeto é implementar uma aplicação Web com client-side e server-side, utilizando a
Framework Vue e Laravel respetivamente, para a cadeia de restaurantes FasTuga que funciona com base na entrega e recolha de produtos do menu disponibilizado no restaurante.

Clique [aqui](materiais/2021-22-EI-AI-enunciado.pdf) para ler o enunciado fornecido.
(Contém informações adicionais sobre o cenário e implementação)

## :computer: Linguagens e Tecnologias Utilizadas

<p><a href="https://laravel.com/" target="_blank" rel="noreferrer"> 
<img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/laravel/laravel-plain-wordmark.svg" alt="laravel" title="Laravel" width="40" height="40" /></a><a href="https://www.php.net" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" title="PHP" width="40" height="40" /></a><a href="https://www.mysql.com/" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/mysql/mysql-original-wordmark.svg" alt="mysql" title="MySQL" width="40" height="40" /></a></a><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/html5/html5-original-wordmark.svg" alt="html5" title="HTML5" width="40" height="40" /><img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/css3/css3-original-wordmark.svg" alt="css3" title="CSS3" width="40" height="40" /><a href="https://git-scm.com/" target="_blank" rel="noreferrer"> <img src="https://www.vectorlogo.zone/logos/git-scm/git-scm-icon.svg" alt="git" title="Git" width="40" height="40" /></a><a href="https://code.visualstudio.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/vscode/vscode-original.svg" alt="vscode" title="Visual Studio Code" width="40" height="40" /></a><a href="https://www.nginx.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/nginx/nginx-original.svg" alt="nginx" title="NGINX" width="40" height="40" /></a><a href="https://www.npmjs.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/npm/npm-original-wordmark.svg" alt="npm" title="NPM" width="40" height="40" /></a><a href="https://getcomposer.org" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/composer/composer-original.svg" alt="composer" title="Composer" width="40" height="40" /></a><a href="https://vuejs.org" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/vuejs/vuejs-original.svg" alt="vuejs" title="Vue.js" width="40" height="40" /></a></a><a href="https://www.javascript.com" target="_blank" rel="noreferrer"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/javascript/javascript-original.svg" alt="javascript" title="Javascript" width="40" height="40" /></a></p>

## :man_technologist: Configurar o Projeto

Para começar deve renomear o `.env.example` para `.env` e preenchê-lo com as informações corretas para a sua área de trabalho.

Após isso deve correr os seguintes comandos em um terminal na pasta fastuga:

```bash
composer update
php artisan migrate:fresh
php artisan db:seed
```

Deve correr também os seguintes comandos em um terminal na pasta app_client:

```bash
npm install
npm run dev
```

## :mortar_board: Outras Informações

-   Licenciatura em [Engenharia Informática](https://www.ipleiria.pt/curso/licenciatura-em-engenharia-informatica/)

<a href="https://www.ipleiria.pt/estg/"><img src="https://www.ipleiria.pt/normasgraficas/wp-content/uploads/sites/80/2017/09/estg_h-01.jpg" width="300" alt="Escola Superior de Tecnologia e Gestão" title="Escola Superior de Tecnologia e Gestão"></a>
