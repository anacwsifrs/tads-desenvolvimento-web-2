import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import {
  ArrowLeft,
  Check,
  X,
  Sparkles,
  Shirt,
  AlertTriangle,
  Lock,
  Save,
  Package
} from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function AvaliarLote() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [lote, setLote] = useState(null);
  const [avaliacoes, setAvaliacoes] = useState({});
  const [carregando, setCarregando] = useState(true);
  const [salvando, setSalvando] = useState(false);
  const [memoriaIA, setMemoriaIA] = useState({});
  const [carregandoIA, setCarregandoIA] = useState({});
  const [popoverAberto, setPopoverAberto] = useState(null);

  useEffect(() => {
    const carregarLote = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=avaliar&id=${id}`, {
          credentials: 'include'
        });
        const dados = await resposta.json();

        if (resposta.ok && dados.id) {
          setLote(dados);
          const inicial = {};
          (dados.pecas || []).forEach((p) => {
            inicial[p.id] = {
              status: Number(p.status_avaliacao || 0),
              valor: p.valor_proposto ? String(p.valor_proposto) : ''
            };
          });
          setAvaliacoes(inicial);
        }
      } catch (error) {
        console.error('Erro ao carregar lote:', error);
      } finally {
        setCarregando(false);
      }
    };

    if (id) carregarLote();
  }, [id]);

  const definirDecisaoPeca = (pecaId, novoStatus) => {
    if (lote && Number(lote.status) >= 3) return;
    setAvaliacoes((prev) => ({
      ...prev,
      [pecaId]: {
        status: novoStatus,
        valor: novoStatus === 1 ? prev[pecaId]?.valor || '' : ''
      }
    }));
  };

  const alterarValorPeca = (pecaId, novoValor) => {
    if (lote && Number(lote.status) >= 3) return;
    setAvaliacoes((prev) => ({
      ...prev,
      [pecaId]: {
        ...prev[pecaId],
        valor: novoValor
      }
    }));
  };

  const calcularTotal = () => {
    return Object.values(avaliacoes).reduce((soma, item) => {
      if (item.status === 1 && item.valor !== '') {
        return soma + (parseFloat(item.valor) || 0);
      }
      return soma;
    }, 0);
  };

  const handleCliqueIA = async (peca) => {
    if (popoverAberto === peca.id) {
      setPopoverAberto(null);
      return;
    }

    if (memoriaIA[peca.id]) {
      setPopoverAberto(peca.id);
      return;
    }

    setCarregandoIA((prev) => ({ ...prev, [peca.id]: true }));

    try {
      const resposta = await fetch(`${API_URL}?acao=sugestao_ia`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          categoria: peca.categoria_nome || '',
          marca: peca.marca || '',
          tamanho: peca.tamanho || '',
          cor: peca.cor || '',
          estado: peca.estado || '',
          descricao: peca.descricao || '',
          observacoes: peca.observacoes || ''
        })
      });

      const data = await resposta.json();
      let textoResultado = '';

      if (data.error && (data.error.code === 503 || data.error.status === 'UNAVAILABLE')) {
        textoResultado =
          '⚠️ A IA está com alta demanda no momento (Erro 503). Aguarde alguns instantes e tente novamente.';
      } else if (data.erro || data.error) {
        textoResultado = `Erro: ${data.erro || data.error.message || 'Erro inesperado ao consultar a IA.'}`;
      } else if (data.candidates && data.candidates.length > 0) {
        textoResultado = data.candidates[0].content.parts[0].text;
        setMemoriaIA((prev) => ({ ...prev, [peca.id]: textoResultado }));
      } else {
        textoResultado = 'A IA não retornou sugestões para esta peça.';
      }

      setMemoriaIA((prev) => ({ ...prev, [peca.id]: textoResultado }));
      setPopoverAberto(peca.id);
    } catch (error) {
      setMemoriaIA((prev) => ({
        ...prev,
        [peca.id]: 'Erro de comunicação com o servidor. Tente novamente.'
      }));
      setPopoverAberto(peca.id);
    } finally {
      setCarregandoIA((prev) => ({ ...prev, [peca.id]: false }));
    }
  };

  const handleSalvarAvaliacao = async (e) => {
    e.preventDefault();

    const pendentes = Object.values(avaliacoes).some((item) => item.status === 0);
    if (pendentes) {
      alert('Você precisa aprovar (✔️) ou reprovar (❌) TODAS as peças antes de salvar.');
      return;
    }

    const semValor = Object.values(avaliacoes).some(
      (item) => item.status === 1 && (item.valor === '' || Number(item.valor) < 0)
    );
    if (semValor) {
      alert('Informe o valor ofertado para todas as peças aprovadas.');
      return;
    }

    setSalvando(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=salvar_proposta`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          lote_id: lote.id,
          valor_total: calcularTotal().toFixed(2),
          pecas: avaliacoes
        })
      });

      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        alert(dados.mensagem || 'Proposta salva com sucesso!');
        navigate('/admin/lotes');
      } else {
        alert(dados.mensagem || 'Erro ao salvar avaliação.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setSalvando(false);
    }
  };

  if (carregando) {
    return (
      <div className="flex justify-center items-center py-24">
        <div className="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (!lote) {
    return (
      <div className="bg-white border border-stone-200 rounded-xl p-8 text-center my-8">
        <p className="font-bold text-stone-700">Lote não encontrado.</p>
        <Link to="/admin/lotes" className="text-emerald-600 font-semibold underline mt-2 inline-block">
          Voltar para Painel de Lotes
        </Link>
      </div>
    );
  }

  const bloqueado = Number(lote.status) >= 3;
  const totalProposta = calcularTotal();

  return (
    <div className="space-y-6 pb-12">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <Link
            to="/admin/lotes"
            className="inline-flex items-center gap-1.5 text-sm font-semibold text-stone-500 hover:text-emerald-700 mb-2 transition"
          >
            <ArrowLeft size={16} />
            <span>Voltar para Painel de Lotes</span>
          </Link>
          <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
            <Package className="text-emerald-600" size={26} />
            <span>Avaliação de Lote #{lote.id}</span>
          </h2>
        </div>
      </div>

      <div className="bg-white border border-stone-200 rounded-xl p-5 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
          <span className="text-xs font-bold uppercase text-stone-400 block">Fornecedor</span>
          <p className="font-bold text-stone-800 text-base mt-0.5">
            {lote.cliente_nome}{' '}
            <span className="font-normal text-stone-500">({lote.telefone})</span>
          </p>
        </div>
        <div>
          <span className="text-xs font-bold uppercase text-stone-400 block">
            Descrição Geral do Lote
          </span>
          <p className="font-semibold text-stone-700 mt-0.5">{lote.descricao}</p>
        </div>
      </div>

      <form onSubmit={handleSalvarAvaliacao} className="space-y-6">
        <div className="bg-white border border-stone-200 rounded-xl shadow-sm overflow-visible">
          <div className="p-5 border-b border-stone-200 bg-stone-50 rounded-t-xl">
            <h3 className="font-bold text-stone-800 text-lg">Avaliar Peças Cadastradas</h3>
          </div>

          <div className="overflow-x-auto overflow-y-visible">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-stone-100 border-b border-stone-200 text-xs uppercase text-stone-600 font-bold">
                  <th className="p-4 w-20">Foto</th>
                  <th className="p-4">Detalhes da Peça</th>
                  <th className="p-4">Características</th>
                  <th className="p-4 w-56">Decisão</th>
                  <th className="p-4 w-60">Valor Ofertado (R$)</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {(lote.pecas || []).map((peca) => {
                  const av = avaliacoes[peca.id] || { status: 0, valor: '' };

                  return (
                    <tr key={peca.id} className="hover:bg-stone-50/70 transition-colors">
                      <td className="p-4">
                        {peca.foto_caminho ? (
                          <img
                            src={`${UPLOADS_URL}${peca.foto_caminho}`}
                            alt="Foto"
                            className="w-16 h-16 object-cover rounded-lg border border-stone-200"
                          />
                        ) : (
                          <div className="w-16 h-16 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400">
                            <Shirt size={24} />
                          </div>
                        )}
                      </td>

                      <td className="p-4">
                        <strong className="text-stone-800 block">
                          {peca.categoria_nome || 'Sem Categoria'}
                        </strong>
                        <p className="text-stone-600 text-xs mt-0.5">{peca.descricao}</p>
                        {peca.observacoes && (
                          <span className="inline-block mt-1.5 text-xs text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded">
                            Obs: {peca.observacoes}
                          </span>
                        )}
                      </td>

                      <td className="p-4 space-y-1">
                        <div className="flex flex-wrap gap-1">
                          <span className="bg-stone-100 text-stone-700 text-xs font-semibold px-2 py-0.5 rounded">
                            Marca: {peca.marca || 'N/A'}
                          </span>
                          <span className="bg-stone-100 text-stone-700 text-xs font-semibold px-2 py-0.5 rounded">
                            Tam: {peca.tamanho || 'N/A'}
                          </span>
                          <span className="bg-stone-100 text-stone-700 text-xs font-semibold px-2 py-0.5 rounded">
                            Cor: {peca.cor || 'N/A'}
                          </span>
                        </div>
                        <span className="inline-block bg-sky-50 text-sky-800 border border-sky-200 text-xs font-semibold px-2 py-0.5 rounded">
                          Estado: {peca.estado || 'N/A'}
                        </span>
                      </td>

                      <td className="p-4">
                        <div className="flex gap-2">
                          <button
                            type="button"
                            disabled={bloqueado}
                            onClick={() => definirDecisaoPeca(peca.id, 1)}
                            className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition flex items-center gap-1 ${
                              av.status === 1
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                : 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-600 hover:text-white'
                            } disabled:opacity-60 disabled:cursor-not-allowed`}
                          >
                            <Check size={14} />
                            <span>Aprovar</span>
                          </button>

                          <button
                            type="button"
                            disabled={bloqueado}
                            onClick={() => definirDecisaoPeca(peca.id, 2)}
                            className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition flex items-center gap-1 ${
                              av.status === 2
                                ? 'bg-red-600 text-white border-red-600 shadow-sm'
                                : 'bg-red-50 text-red-700 border-red-300 hover:bg-red-600 hover:text-white'
                            } disabled:opacity-60 disabled:cursor-not-allowed`}
                          >
                            <X size={14} />
                            <span>Reprovar</span>
                          </button>
                        </div>
                      </td>

                      <td className="p-4">
                        <div className="flex items-center gap-2 relative">
                          {av.status === 1 ? (
                            <input
                              type="number"
                              step="0.01"
                              min="0"
                              required
                              readOnly={bloqueado}
                              placeholder="0.00"
                              value={av.valor}
                              onChange={(e) => alterarValorPeca(peca.id, e.target.value)}
                              className="w-28 p-2 bg-white border border-stone-300 rounded-lg text-sm font-bold text-stone-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                          ) : (
                            <span className="w-28 text-xs font-semibold text-stone-400">
                              {av.status === 2 ? 'Reprovado' : 'Pendente'}
                            </span>
                          )}

                          <div className="relative">
                            <button
                              type="button"
                              onClick={() => handleCliqueIA(peca)}
                              disabled={carregandoIA[peca.id]}
                              title="Sugestão de Preço com IA (Gemini)"
                              className="w-9 h-9 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white flex items-center justify-center shadow-sm transition transform hover:scale-105 disabled:opacity-70"
                            >
                              {carregandoIA[peca.id] ? (
                                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                              ) : (
                                <Sparkles size={16} />
                              )}
                            </button>

                            {popoverAberto === peca.id && (
                              <div className="absolute right-0 top-11 w-72 bg-white border border-purple-300 rounded-xl shadow-xl p-3.5 z-50 text-left">
                                <div className="flex justify-between items-center border-b border-stone-100 pb-2 mb-2">
                                  <span className="text-xs font-bold text-purple-700 flex items-center gap-1">
                                    <Sparkles size={14} /> Sugestão da IA
                                  </span>
                                  <button
                                    type="button"
                                    onClick={() => setPopoverAberto(null)}
                                    className="text-stone-400 hover:text-red-500 font-bold text-sm"
                                  >
                                    ✕
                                  </button>
                                </div>
                                <div className="text-xs text-stone-700 leading-relaxed max-h-48 overflow-y-auto whitespace-pre-line">
                                  {memoriaIA[peca.id]}
                                </div>
                              </div>
                            )}
                          </div>
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>

          <div className="p-5 bg-stone-100 border-t border-stone-200 flex justify-end items-center gap-3 rounded-b-xl">
            <span className="text-base font-bold text-stone-700">Valor Total da Proposta:</span>
            <span className="text-2xl font-black text-emerald-600">
              {totalProposta.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}
            </span>
          </div>
        </div>

        {!bloqueado ? (
          <button
            type="submit"
            disabled={salvando}
            className="w-full py-4 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-xl transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60"
          >
            {salvando ? (
              <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            ) : (
              <>
                <Save size={20} />
                <span>Salvar Avaliação e Gerar Proposta</span>
              </>
            )}
          </button>
        ) : (
          <div className="space-y-3">
            <div className="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-xl text-center text-sm font-semibold flex items-center justify-center gap-2">
              <AlertTriangle size={18} className="text-amber-600" />
              <span>Esta avaliação já foi enviada e finalizada. Não é possível alterar a proposta.</span>
            </div>
            <button
              type="button"
              disabled
              className="w-full py-3.5 px-6 bg-stone-300 text-stone-500 font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-2"
            >
              <Lock size={18} />
              <span>Avaliação Bloqueada</span>
            </button>
          </div>
        )}
      </form>
    </div>
  );
}