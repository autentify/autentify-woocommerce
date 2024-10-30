=== Autentify anti fraud for WooCommerce ===
Description: Anti-fraude em tempo real para e-commerces, protegendo transações e auxiliando na tomada de decisões seguras.
Contributors: autentify
Tags: Fraud Prevention, E-commerce Security, Payment Protection, WooCommerce Anti-Fraud, Risk Management Plugin
Stable tag: 2.1.2
Requires at least: 4.7
Tested up to: 6.6.2
PHP requires at least: 5.6
PHP Tested up to: 8.3
WC requires at least: 3.3
WC tested up to: 8.2.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

== Description ==
AUTENTIFY é uma plataforma de prevenção a fraude em tempo real que ajuda comerciantes de todos os tamanhos na tomada de decisão. Este plugin se integra diretamente à plataforma AUTENTIFY e permite que os comerciantes comecem a combater a fraude imediatamente.

= Score de usuários em tempo real =

O AUTENTIFY analisa os usuários cadastrados, fornecendo uma pontuação única que avalia o risco de fraude apresentado por cada usuário. Se a pontuação de um usuário for de alto risco, você pode investigar o usuário antes mesmo que ocorra um pedido.

= Pontuação de risco =

Nosso algoritmo de pontuação exclusivo rastreia e analisa diversos atributos, usando apenas o email do usuário como chave primária, permitindo assim que você avalie facilmente o risco de fraude e automatize seu processo de revisão.

= Verificação de identidade =

Utilize nossas outras verificações como o AutentiD (checagem de dados cadastrais) ou AutentiFace (validação biométrica facial) como etapas extras de verificação. O nível de impacto no usuário pode ser personalizado e ajustável com base na pontuação que cada usuário recebe.

= Preços =

O AUTENTIFY é um serviço pré-pago com preços flexíveis com base no número de consultas recebidas por mês. Uma conta AUTENTIFY separada é necessária para liberação do token e utilização das consultas. Após o período de teste gratuito de 7 dias, o preço começa em R$ 199,90 por mês. Para obter mais informações, visite https://www.autentify.com.br.

== Installation ==

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.
* Configure o API Token. Exemplo disponível na screenshot 1.

= Requerimentos: =

É necessário possuir uma conta no [Autentify](https://www.autentify.com.br) e ter instalado o [WooCommerce](https://wordpress.org/plugins/woocommerce/).
Apenas com isso já é possível fazer consultas e receber o retorno automático de dados.

* PHP versão 5.6 ou mais recente
* MySQL versão 5.0 ou mais recente
* WordPress versão 4.7 ou mais recente
* WooCommerce versão 3.3 ou mais recente

== Screenshots ==

1. Configuração do API Token.
2. Pedidos com resultado de consulta e pedidos com opção para iniciar consulta.
3. Painel Autentify. Utilizando o nosso painel você consegue ter um controle maior das avaliações.
4. Demonstração de análise de consulta com opção de validação facial utilizando o nosso painel.

== FAQ ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* Ter instalado uma versão atual do plugin WooCommerce.
* Possuir uma conta no Autentify.
* Gerar um token de segurança no Autentify.
* Inserir o token de de segurança no plugin Autentify Woocommerce

= Mais dúvidas relacionadas ao funcionamento do plugin? =

Por favor, caso você tenha algum problema com o funcionamento do plugin, [abra um tópico no fórum do plugin](https://wordpress.org/support/plugin/autentify-anti-fraud-for-woocommerce/).

== Changelog ==

= 2.0.1 - 2022-04-18 =
* Fix to not use the AutentiMail check when the order already has an AutentiMail.

= 2.0.0 - 2022-04-16 =
* Remake the plugin using the Autentify API 2.0.

= 1.0.3 - 2021-01-07 =
* Change the point to call Autentify_Api and Autentify_Auth classes.

= 1.0.2 - 2020-10-03 =
* Update the way to send user checks by API.

= 1.0.1 - 2020-09-25 =
* Update language files

= 1.0.0 - 2020-09-25 =
* Initial Public Beta Release
