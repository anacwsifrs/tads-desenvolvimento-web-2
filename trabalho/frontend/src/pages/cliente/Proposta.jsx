import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ArrowLeft, CheckCircle2, XCircle, Clock, Sparkles, Wallet, Banknote } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';

export default function Proposta() {
  const { id } = useParams();
  const [lote, setLote] = useState(null);
  const [carregando, setCarregando] = useState(true);
  const [enviando, setEnviando] = useState(false);

  const carregarProposta = async () => {
    try {
      const resposta = await fetch(`${API_URL}?acao=proposta&id=${id}`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.id) {
        setLote(dados);
      }
    } catch (error) {
      console.error('Erro ao carregar proposta:', error);
    } finally {
      setCarregando(false);
    }
  };

  useEffect(() => {
    if (id) carregarProposta();
  }, [id]);

  const handleResponder = async (decisao) => {
    if (decisao === 6 && !window.confirm('Tem certeza que deseja recusar a proposta?')) {
      return;
    }

    setEnviando(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=responder_proposta`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ lote_id: lote.id, decisao })
      });
      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        if (dados.novo_saldo !== null) {
          const userLocal = JSON.parse(localStorage.getItem('usuario') || '{}');
          localStorage.setItem(
            'usuario',
            JSON.stringify({ ...userLocal, saldo_credito: dados.novo_saldo })
          );
          window.dispatchEvent(new Event('usuarioAtualizado'));
        }
        alert(dados.mensagem || 'Sua resposta foi registrada com sucesso!');
        carregarProposta();
      } else {
        alert(dados.mensagem || 'Erro ao registrar resposta.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setEnviando(false);
    }
  };

  const formatarMoeda = (v) =>
    Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  if (carregando) {
    return (
      <div className="flex justify-center items-center py-20">
        <div className="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (!lote) {
    return (
      <div className="bg-white p-8 rounded-xl border border-stone-200 text-center my-8">
        <p className="font-bold text-stone-700">Lote não encontrado.</p>
        <Link to="/meus-lotes" className="text-emerald-600 font-semibold underline mt-2 inline-block">
          Voltar para Meus Lotes
        </Link>
      </div>
    );
  }

  const statusNum = Number(lote.status);
  const valorPix = Number(lote.valor_proposta || 0);
  const valorCredito = valorPix * 1.15;

  return (
    <div className="max-w-3xl mx-auto my-6 space-y-4">
      <Link
        to="/meus-lotes"
        className="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 hover:text-emerald-700 transition"
      >
        <ArrowLeft size={16} />
        <span>Voltar para Meus Lotes</span>
      </Link>

      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-8 space-y-6">
        <div className="text-center">
          <h2 className="text-2xl font-black text-stone-800">Olá, {lote.cliente_nome}!</h2>
          <p className="text-stone-500 text-sm mt-1">Lote #{lote.id} — {lote.descricao}</p>
        </div>

        {statusNum < 3 ? (
          <div className="bg-stone-100 border border-stone-200 rounded-xl p-6 text-center space-y-2 text-stone-600">
            <Clock className="mx-auto text-amber-500" size={32} />
            <p className="font-bold text-stone-800">
              Seu lote ainda está em processo de avaliação!
            </p>
            <p className="text-sm">
              Nossa equipe está analisando suas peças com carinho. Assim que a avaliação for
              concluída, os valores da proposta aparecerão nesta mesma página.
            </p>
          </div>
        ) : (
          <>
            <div className="bg-stone-50 border border-stone-200 rounded-xl p-5 space-y-3">
              <h3 className="font-bold text-stone-800">Peças Avaliadas:</h3>
              <ul className="divide-y divide-stone-200 text-sm">
                {(lote.pecas || []).map((peca) => (
                  <li key={peca.id} className="py-2 flex items-center justify-between gap-2">
                    <span className="text-stone-700">
                      <strong>{peca.categoria_nome}</strong>
                      {peca.descricao ? ` (${peca.descricao})` : ''}
                    </span>
                    {Number(peca.status_avaliacao) === 1 ? (
                      <span className="text-emerald-600 font-bold shrink-0">
                        Aprovada ({formatarMoeda(peca.valor_proposto)})
                      </span>
                    ) : (
                      <span className="text-red-500 font-bold line-through shrink-0">
                        Reprovada
                      </span>
                    )}
                  </li>
                ))}
              </ul>
            </div>

            {statusNum === 3 ? (
              <div className="space-y-4 pt-2">
                <h3 className="text-center text-lg font-black text-stone-800">
                  Escolha como deseja receber:
                </h3>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div className="bg-emerald-50 border-2 border-emerald-500 rounded-xl p-5 text-center flex flex-col justify-between space-y-3">
                    <div>
                      <div className="inline-flex items-center gap-1.5 text-emerald-800 font-bold text-sm">
                        <Banknote size={18} />
                        <span>Receber em Dinheiro (PIX)</span>
                      </div>
                      <div className="text-3xl font-black text-emerald-700 my-2">
                        {formatarMoeda(valorPix)}
                      </div>
                    </div>
                    <button
                      type="button"
                      disabled={enviando}
                      onClick={() => handleResponder(4)}
                      className="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-sm"
                    >
                      Aceitar via PIX
                    </button>
                  </div>

                  <div className="bg-amber-50 border-2 border-amber-500 rounded-xl p-5 text-center flex flex-col justify-between space-y-3">
                    <div>
                      <div className="inline-flex items-center gap-1.5 text-amber-800 font-bold text-sm">
                        <Wallet size={18} />
                        <span>Crédito na Loja (+15%)</span>
                      </div>
                      <div className="text-3xl font-black text-amber-600 my-2">
                        {formatarMoeda(valorCredito)}
                      </div>
                      <p className="text-xs text-amber-800">
                        Use este valor para comprar na loja!
                      </p>
                    </div>
                    <button
                      type="button"
                      disabled={enviando}
                      onClick={() => handleResponder(5)}
                      className="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg transition shadow-sm"
                    >
                      Aceitar Crédito na Loja
                    </button>
                  </div>
                </div>

                <button
                  type="button"
                  disabled={enviando}
                  onClick={() => handleResponder(6)}
                  className="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition text-sm"
                >
                  Não aceito, quero retirar as peças
                </button>
              </div>
            ) : (
              <div className="bg-stone-100 border border-stone-200 rounded-xl p-5 text-center font-bold">
                {statusNum === 4 && (
                  <span className="text-emerald-700 flex items-center justify-center gap-2">
                    <CheckCircle2 size={20} /> Você aceitou a proposta em PIX.
                  </span>
                )}
                {statusNum === 5 && (
                  <span className="text-amber-700 flex items-center justify-center gap-2">
                    <Sparkles size={20} /> Você aceitou os Créditos na Loja!
                  </span>
                )}
                {statusNum === 6 && (
                  <span className="text-red-600 flex items-center justify-center gap-2">
                    <XCircle size={20} /> Você recusou a proposta. Retire suas peças na loja.
                  </span>
                )}
                {statusNum === 7 && (
                  <span className="text-stone-700">
                    📦 Prazo expirado. O lote foi destinado para doação.
                  </span>
                )}
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}