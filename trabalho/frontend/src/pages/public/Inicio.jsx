import { Link } from 'react-router-dom';
import { Leaf, ArrowRight, Sparkles, CheckCircle2, HeartHandshake } from 'lucide-react';

export default function Inicio() {
  const passos = [
    {
      num: '1. Entrega do Lote',
      desc: 'Você traz suas peças de vestuário até o nosso brechó. Registramos o lote em seu nome no sistema.'
    },
    {
      num: '2. Avaliação Inteligente',
      desc: 'Nossa equipe realiza a triagem presencial e utiliza Inteligência Artificial para avaliar e precificar cada item com justiça.'
    },
    {
      num: '3. Proposta & Pagamento',
      desc: (
        <>
          Você recebe uma proposta no seu painel. Escolha entre receber em <strong>Dinheiro (PIX)</strong> ou em <strong>Crédito (+15% de bônus)</strong> para usar no brechó.
        </>
      )
    },
    {
      num: '4. Vitrine & Reserva',
      desc: 'As peças aprovadas vão para a Vitrine Pública. Clientes podem adicionar ao carrinho, pagar via PIX e retirar na loja física.'
    }
  ];

  return (
    <div className="space-y-10 pb-8">
      <section className="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-700 text-white rounded-2xl p-8 md:p-12 text-center shadow-lg relative overflow-hidden">
        <div className="max-w-3xl mx-auto relative z-10">
          <div className="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm px-4 py-1.5 rounded-full text-sm font-semibold mb-4">
            <Leaf size={16} className="text-emerald-200" />
            <span>Moda Circular & Sustentável</span>
          </div>
          
          <h1 className="text-3xl md:text-5xl font-black tracking-tight mb-4">
            Bem-vindo ao Brechó EcoModa 🌿
          </h1>
          
          <p className="text-emerald-50 text-base md:text-lg leading-relaxed mb-8">
            Sua plataforma de moda circular sustentável. Dê um novo destino às suas roupas e garanta peças incríveis com preços acessíveis.
          </p>

          <div className="flex flex-col sm:flex-row justify-center gap-4">
            <Link 
              to="/vitrine" 
              className="bg-amber-500 hover:bg-amber-400 text-stone-900 font-bold px-6 py-3.5 rounded-lg transition shadow-md flex items-center justify-center gap-2"
            >
              <span>Explorar Vitrine</span>
              <ArrowRight size={18} />
            </Link>
            <Link 
              to="/login" 
              className="bg-white/15 hover:bg-white/25 text-white font-semibold px-6 py-3.5 rounded-lg transition border border-white/30"
            >
              Acessar Minha Conta
            </Link>
          </div>
        </div>
      </section>

      <section>
        <div className="mb-6">
          <h2 className="text-2xl font-bold text-stone-800 flex items-center gap-2">
            <Sparkles className="text-emerald-600" size={24} />
            <span>Como Funciona o Nosso Sistema?</span>
          </h2>
          <p className="text-stone-500 mt-1">
            Entenda o processo simples e transparente para vender seus lotes e comprar no brechó:
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {passos.map((passo, index) => (
            <div 
              key={index} 
              className="bg-white p-6 rounded-xl border border-stone-200 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between"
            >
              <div>
                <div className="text-lg font-extrabold text-emerald-700 mb-3 pb-2 border-b border-stone-100">
                  {passo.num}
                </div>
                <p className="text-stone-600 text-sm leading-relaxed">
                  {passo.desc}
                </p>
              </div>
            </div>
          ))}
        </div>
      </section>

      <section className="bg-emerald-50/80 border-l-4 border-emerald-600 p-6 md:p-8 rounded-r-xl shadow-sm">
        <h3 className="text-xl font-bold text-emerald-900 flex items-center gap-2 mb-3">
          <HeartHandshake className="text-emerald-700" size={24} />
          <span>Nosso Compromisso com a Sustentabilidade e Doações</span>
        </h3>
        
        <p className="text-stone-700 mb-4">
          Prezamos pela transparência e pelo impacto social positivo. Confira nossas regras de avaliação e aceite:
        </p>

        <ul className="space-y-3 text-stone-700 text-sm md:text-base">
          <li className="flex items-start gap-2.5">
            <CheckCircle2 className="text-emerald-600 shrink-0 mt-0.5" size={18} />
            <span>
              <strong>Prazo de 3 Dias Úteis:</strong> Após a emissão da proposta, o fornecedor tem até 3 dias úteis para aceitar ou recusar os valores.
            </span>
          </li>
          <li className="flex items-start gap-2.5">
            <CheckCircle2 className="text-emerald-600 shrink-0 mt-0.5" size={18} />
            <span>
              <strong>Recusa de Lote:</strong> Em caso de recusa, as peças ficam disponíveis para retirada no brechó durante o prazo estipulado.
            </span>
          </li>
          <li className="flex items-start gap-2.5">
            <CheckCircle2 className="text-emerald-600 shrink-0 mt-0.5" size={18} />
            <span>
              <strong>Doação Consciente (Status 7):</strong> Peças não retiradas dentro do prazo de tolerância ou declaradas para doação são encaminhadas para instituições de caridade parceiras, promovendo a responsabilidade social.
            </span>
          </li>
        </ul>
      </section>
    </div>
  );
}