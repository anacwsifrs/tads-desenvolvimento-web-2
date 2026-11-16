import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import {
  Leaf,
  ArrowRight,
  Sparkles,
  HeartHandshake,
  ShoppingBag,
  Package,
  Clock,
  RefreshCw,
  Shirt,
  Wallet,
  ShieldCheck,
  Store
} from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function Inicio() {
  const [abaFluxo, setAbaFluxo] = useState('vender');
  const [pecasDestaque, setPecasDestaque] = useState([]);
  const [carregandoPecas, setCarregandoPecas] = useState(true);

  const usuarioLogado = (() => {
    try {
      return JSON.parse(localStorage.getItem('brecho_usuario'));
    } catch {
      return null;
    }
  })();

  useEffect(() => {
    const carregarDestaques = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=vitrine`);
        const dados = await resposta.json();
        if (Array.isArray(dados)) {
          setPecasDestaque(dados.slice(0, 4));
        }
      } catch (error) {
        console.error('Erro ao carregar destaques da vitrine:', error);
      } finally {
        setCarregandoPecas(false);
      }
    };

    carregarDestaques();
  }, []);

  const formatarPreco = (valorProposto) => {
    const precoVenda = Number(valorProposto || 0) * 1.4;
    return precoVenda.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  };

  const fluxoVender = [
    {
      passo: '01',
      titulo: 'Entrega Presencial do Lote',
      desc: 'Traga suas roupas higienizadas até a loja física. Cadastramos o lote no sistema vinculado ao seu CPF.',
      icone: Package,
      cor: 'bg-emerald-100 text-emerald-700'
    },
    {
      passo: '02',
      titulo: 'Triagem & Avaliação com IA',
      desc: 'Nossa equipe analisa marca, tecido e conservação com apoio de Inteligência Artificial para sugerir valores justos.',
      icone: Sparkles,
      cor: 'bg-purple-100 text-purple-700'
    },
    {
      passo: '03',
      titulo: 'PIX ou +15% em Crédito',
      desc: 'Você recebe a proposta no painel e escolhe: dinheiro direto via PIX ou saldo em loja com 15% de bônus extra.',
      icone: Wallet,
      cor: 'bg-amber-100 text-amber-700'
    }
  ];

  const fluxoComprar = [
    {
      passo: '01',
      titulo: 'Explore a Vitrine Curada',
      desc: 'Filtre peças exclusivas por categoria, tamanho ou marca com fotos reais e estado de conservação detalhado.',
      icone: Store,
      cor: 'bg-emerald-100 text-emerald-700'
    },
    {
      passo: '02',
      titulo: 'Reserve com Saldo ou PIX',
      desc: 'Adicione itens ao carrinho, abata seu saldo de crédito acumulado ou finalize via PIX enviando o comprovante.',
      icone: ShoppingBag,
      cor: 'bg-sky-100 text-sky-700'
    },
    {
      passo: '03',
      titulo: 'Retirada no Balcão',
      desc: 'Assim que o pedido for aprovado pela loja, suas peças ficam separadas e prontas para retirada presencial.',
      icone: ShieldCheck,
      cor: 'bg-teal-100 text-teal-700'
    }
  ];

  const passosAtivos = abaFluxo === 'vender' ? fluxoVender : fluxoComprar;

  return (
    <div className="space-y-12 pb-10">
      {/* HERO ASSIMÉTRICO */}
      <section className="bg-white border border-stone-200 rounded-3xl p-6 sm:p-10 lg:p-12 shadow-sm overflow-hidden">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
          <div className="lg:col-span-7 space-y-6">
            <div className="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 px-3.5 py-1.5 rounded-full text-xs font-bold">
              <Leaf size={14} className="text-emerald-600" />
              <span>Moda Circular • Avaliação Inteligente • Economia Real</span>
            </div>

            <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black text-stone-800 tracking-tight leading-[1.12]">
              Renove seu guarda-roupa e transforme peças paradas em{' '}
              <span className="text-emerald-600">crédito ou PIX</span>.
            </h1>

            <p className="text-stone-600 text-base sm:text-lg leading-relaxed max-w-2xl">
              No <strong className="text-stone-800">Intent Brechó</strong>, cada peça ganha uma
              segunda história com triagem transparente, precificação assistida por IA e reserva
              online simplificada.
            </p>

            <div className="flex flex-col sm:flex-row gap-3 pt-1">
              <Link
                to="/vitrine"
                className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-2"
              >
                <ShoppingBag size={19} />
                <span>Explorar Vitrine</span>
                <ArrowRight size={18} />
              </Link>

              {usuarioLogado ? (
                <Link
                  to={usuarioLogado.tipo === 'admin' ? '/admin/lotes' : '/meus-lotes'}
                  className="bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold px-6 py-3.5 rounded-xl transition flex items-center justify-center gap-2"
                >
                  <Package size={19} className="text-emerald-600" />
                  <span>
                    {usuarioLogado.tipo === 'admin' ? 'Painel Administrativo' : 'Meus Lotes'}
                  </span>
                </Link>
              ) : (
                <Link
                  to="/cadastro"
                  className="bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold px-6 py-3.5 rounded-xl transition flex items-center justify-center gap-2"
                >
                  <span>Criar Conta Grátis</span>
                </Link>
              )}
            </div>

            <div className="grid grid-cols-3 gap-4 pt-4 border-t border-stone-100">
              <div>
                <span className="text-xl sm:text-2xl font-black text-emerald-600 block">+15%</span>
                <span className="text-xs text-stone-500 font-medium">
                  Bônus ao escolher crédito em loja
                </span>
              </div>
              <div>
                <span className="text-xl sm:text-2xl font-black text-stone-800 block">IA</span>
                <span className="text-xs text-stone-500 font-medium">
                  Precificação justa assistida
                </span>
              </div>
              <div>
                <span className="text-xl sm:text-2xl font-black text-stone-800 block">3 dias</span>
                <span className="text-xs text-stone-500 font-medium">
                  Prazo transparente de análise
                </span>
              </div>
            </div>
          </div>

          {/* CARD VISUAL LATERAL */}
          <div className="lg:col-span-5">
            <div className="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-700 rounded-2xl p-6 text-white shadow-lg space-y-5">
              <div className="flex items-center justify-between border-b border-white/15 pb-4">
                <div>
                  <span className="text-xs uppercase tracking-wider text-emerald-200 font-bold">
                    Ciclo Sustentável
                  </span>
                  <h2 className="text-lg font-black mt-0.5">Como seu lote gera valor</h2>
                </div>
                <div className="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                  <RefreshCw size={20} className="text-emerald-200" />
                </div>
              </div>

              <div className="space-y-3 text-sm">
                <div className="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 flex items-center justify-between">
                  <span className="text-emerald-50">Avaliação aprovada de R$ 100,00</span>
                  <span className="font-bold text-white bg-white/15 px-2.5 py-1 rounded-lg text-xs">
                    Proposta
                  </span>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div className="bg-white/10 rounded-xl p-3.5">
                    <span className="text-xs text-emerald-200 block">Opção 1 • PIX</span>
                    <strong className="text-lg font-black text-white">R$ 100,00</strong>
                    <span className="text-[11px] text-emerald-100 block mt-0.5">
                      Direto na sua chave
                    </span>
                  </div>

                  <div className="bg-amber-400 text-stone-900 rounded-xl p-3.5 shadow">
                    <span className="text-xs font-bold uppercase block text-amber-900">
                      Opção 2 • Crédito
                    </span>
                    <strong className="text-lg font-black">R$ 115,00</strong>
                    <span className="text-[11px] font-semibold block mt-0.5">
                      +15% bônus na vitrine
                    </span>
                  </div>
                </div>
              </div>

              <p className="text-xs text-emerald-100 leading-relaxed">
                O crédito fica disponível imediatamente no seu perfil para abater ou quitar
                reservas na vitrine.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* COMO FUNCIONA COM ABAS INTERATIVAS */}
      <section className="space-y-6">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-stone-200 pb-4">
          <div>
            <h2 className="text-2xl font-black text-stone-800">Como Funciona na Prática</h2>
            <p className="text-stone-500 text-sm mt-1">
              Escolha seu objetivo abaixo para ver o passo a passo completo.
            </p>
          </div>

          <div className="inline-flex bg-stone-100 p-1 rounded-xl border border-stone-200 self-start">
            <button
              type="button"
              onClick={() => setAbaFluxo('vender')}
              className={`px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition ${
                abaFluxo === 'vender'
                  ? 'bg-emerald-600 text-white shadow-xs'
                  : 'text-stone-600 hover:text-stone-900'
              }`}
            >
              Quero Desapegar (Lotes)
            </button>
            <button
              type="button"
              onClick={() => setAbaFluxo('comprar')}
              className={`px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition ${
                abaFluxo === 'comprar'
                  ? 'bg-emerald-600 text-white shadow-xs'
                  : 'text-stone-600 hover:text-stone-900'
              }`}
            >
              Quero Garimpar (Vitrine)
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {passosAtivos.map((item) => {
            const Icone = item.icone;
            return (
              <div
                key={item.passo}
                className="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow space-y-4"
              >
                <div className="flex items-center justify-between">
                  <div className={`w-11 h-11 rounded-xl flex items-center justify-center ${item.cor}`}>
                    <Icone size={22} />
                  </div>
                  <span className="text-2xl font-black text-stone-200">{item.passo}</span>
                </div>

                <div>
                  <h3 className="text-lg font-bold text-stone-800">{item.titulo}</h3>
                  <p className="text-sm text-stone-600 leading-relaxed mt-1.5">{item.desc}</p>
                </div>
              </div>
            );
          })}
        </div>
      </section>

      {/* NOVIDADES DA VITRINE */}
      {!carregandoPecas && pecasDestaque.length > 0 && (
        <section className="space-y-6">
          <div className="flex items-end justify-between border-b border-stone-200 pb-4">
            <div>
              <h2 className="text-2xl font-black text-stone-800">Recém-Chegados na Vitrine</h2>
              <p className="text-stone-500 text-sm mt-1">
                Peças únicas já avaliadas e prontas para reserva imediata.
              </p>
            </div>
            <Link
              to="/vitrine"
              className="text-sm font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1"
            >
              <span>Ver todas</span>
              <ArrowRight size={16} />
            </Link>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {pecasDestaque.map((peca) => (
              <Link
                key={peca.id}
                to={`/detalhar/${peca.id}`}
                className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition group"
              >
                <div className="h-44 bg-stone-100 flex items-center justify-center overflow-hidden">
                  {peca.foto_caminho ? (
                    <img
                      src={`${UPLOADS_URL}${peca.foto_caminho}`}
                      alt={peca.descricao || 'Peça'}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                  ) : (
                    <Shirt size={44} className="text-stone-300" />
                  )}
                </div>
                <div className="p-4">
                  <span className="text-[11px] font-bold uppercase text-emerald-700">
                    {peca.categoria_nome}
                  </span>
                  <h3 className="font-bold text-stone-800 text-sm line-clamp-1 mt-0.5">
                    {peca.descricao || 'Peça Exclusiva'}
                  </h3>
                  <p className="text-lg font-black text-emerald-600 mt-2">
                    {formatarPreco(peca.valor_proposto)}
                  </p>
                </div>
              </Link>
            ))}
          </div>
        </section>
      )}

      {/* REGRAS DE TRANSPARÊNCIA E IMPACTO SOCIAL */}
      <section className="bg-white border border-stone-200 rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-stone-100 pb-4">
          <h3 className="text-xl font-black text-stone-800 flex items-center gap-2">
            <HeartHandshake className="text-emerald-600" size={24} />
            <span>Transparência e Compromisso Social</span>
          </h3>
          <Link
            to="/termos"
            className="text-xs font-bold text-emerald-700 hover:underline"
          >
            Ler Termos Completos e LGPD →
          </Link>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm">
          <div className="p-4 rounded-xl bg-stone-50 border border-stone-200 space-y-1.5">
            <div className="flex items-center gap-2 font-bold text-stone-800">
              <Clock size={17} className="text-amber-600" />
              <span>Prazo de 3 Dias Úteis</span>
            </div>
            <p className="text-stone-600 leading-relaxed">
              Após a proposta ficar pronta, você tem até 3 dias úteis para aceitar ou recusar
              diretamente pelo sistema.
            </p>
          </div>

          <div className="p-4 rounded-xl bg-stone-50 border border-stone-200 space-y-1.5">
            <div className="flex items-center gap-2 font-bold text-stone-800">
              <Package size={17} className="text-sky-600" />
              <span>Retirada em Caso de Recusa</span>
            </div>
            <p className="text-stone-600 leading-relaxed">
              Se recusar a proposta ou tiver peças não aprovadas, elas ficam separadas para retirada
              na loja física dentro do prazo.
            </p>
          </div>

          <div className="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200 space-y-1.5">
            <div className="flex items-center gap-2 font-bold text-emerald-900">
              <HeartHandshake size={17} className="text-emerald-600" />
              <span>Doação Consciente</span>
            </div>
            <p className="text-emerald-800 leading-relaxed">
              Itens não retirados após o prazo ou marcados para doação são encaminhados a
              instituições parceiras da comunidade.
            </p>
          </div>
        </div>
      </section>
    </div>
  );
}