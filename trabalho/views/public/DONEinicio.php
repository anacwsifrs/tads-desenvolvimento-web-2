<?php require_once 'views/include/header.php'; ?>

<style>
    .hero-section { background: linear-gradient(135deg, #17a2b8, #007bff); color: white; padding: 40px 20px; border-radius: 10px; text-align: center; margin-bottom: 30px; }
    .hero-section h1 { font-size: 32px; margin-bottom: 10px; }
    .hero-section p { font-size: 18px; opacity: 0.95; max-width: 700px; margin: 0 auto 20px auto; }
    .btn-hero { padding: 12px 25px; background: #28a745; color: white; text-decoration: none; font-weight: bold; border-radius: 5px; font-size: 16px; display: inline-block; }
    .btn-hero:hover { background: #218838; }

    .grid-passos { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
    .card-passo { background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .card-passo .num { font-size: 28px; font-weight: bold; color: #17a2b8; margin-bottom: 10px; }

    .secao-doacao { background: #f8f9fa; border-left: 5px solid #28a745; padding: 20px; border-radius: 5px; margin-top: 30px; }
</style>

<div class="hero-section">
    <h1>Bem-vindo ao Brechó EcoModa 🌿</h1>
    <p>Sua plataforma de moda circular sustentável. Dê um novo destino às suas roupas e garanta peças incríveis com preços acessíveis.</p>
</div>

<h2>Como Funciona o Nosso Sistema?</h2>
<p style="color:#666;">Entenda o processo simples e transparente para vender seus lotes e comprar no brechó:</p>

<div class="grid-passos">
    <div class="card-passo">
        <div class="num">1. Entrega do Lote</div>
        <p>Você traz suas peças de vestuário até o nosso brechó. Registramos o lote em seu nome no sistema.</p>
    </div>
    <div class="card-passo">
        <div class="num">2. Avaliação Inteligente</div>
        <p>Nossa equipe realiza a triagem presencial e utiliza Inteligência Artificial para avaliar e precificar cada item com justiça.</p>
    </div>
    <div class="card-passo">
        <div class="num">3. Proposta & Pagamento</div>
        <p>Você recebe uma proposta no seu painel. Escolha entre receber em <strong>Dinheiro (PIX)</strong> ou em <strong>Crédito (+15% de bônus)</strong> para usar no brechó.</p>
    </div>
    <div class="card-passo">
        <div class="num">4. Vitrine & Reserva</div>
        <p>As peças aprovadas vão para a Vitrine Pública. Clientes podem adicionar ao carrinho, pagar via PIX e retirar na loja física.</p>
    </div>
</div>

<div class="secao-doacao">
    <h3 style="margin-top:0; color:#155724;">🌱 Nosso Compromisso com a Sustentabilidade e Doações</h3>
    <p style="margin-bottom:10px; color:#333;">
        Prezamos pela transparência e pelo impacto social positivo. Confira nossas regras de avaliação e aceite:
    </p>
    <ul>
        <li><strong>Prazo de 3 Dias Úteis:</strong> Após a emissão da proposta, o fornecedor tem até 3 dias úteis para aceitar ou recusar os valores.</li>
        <li><strong>Recusa de Lote:</strong> Em caso de recusa, as peças ficam disponíveis para retirada no brechó durante o prazo estipulado.</li>
        <li><strong>Doação Consciente (Status 7):</strong> Peças não retiradas dentro do prazo de tolerância ou declaradas para doação são encaminhadas para instituições de caridade parceiras, promovendo a responsabilidade social.</li>
    </ul>
</div>

<?php require_once 'views/include/footer.php'; ?>