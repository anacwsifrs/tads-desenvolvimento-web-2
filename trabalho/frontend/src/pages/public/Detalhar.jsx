import { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { ArrowLeft, ShoppingCart, Shirt, AlertCircle, CheckCircle2, Tag, Sparkles } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function Detalhar() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [peca, setPeca] = useState(null);
  const [carregando, setCarregando] = useState(true);
  const [adicionando, setAdicionando] = useState(false);
  const [erro, setErro] = useState('');

  useEffect(() => {
    const carregarPeca = async () => {
      setCarregando(true);
      setErro('');
      try {
        const resposta = await fetch(`${API_URL}?acao=detalhar&id=${id}`);
        const dados = await resposta.json();

        if (!resposta.ok || dados.erro) {
          setErro(dados.erro || 'Peça não encontrada.');
          setPeca(null);
        } else {
          setPeca(dados);
        }
      } catch (error) {
        console.error('Erro ao buscar detalhes da peça:', error);
        setErro('Erro de conexão com o servidor.');
      } finally {
        setCarregando(false);
      }
    };

    if (id) carregarPeca();
  }, [id]);

  const handleAdicionarCarrinho = async () => {
    if (!peca) return;
    setAdicionando(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=adicionar_carrinho&id=${peca.id}`, {
        method: 'GET',
        credentials: 'include'
      });

      const dados = await resposta.json();

      if (resposta.status === 401) {
        alert('Faça login para adicionar peças ao carrinho.');
        navigate('/login');
        return;
      }

      if (resposta.ok && dados.sucesso) {
        navigate('/carrinho');
      } else {
        alert(dados.mensagem || 'Não foi possível adicionar a peça ao carrinho.');
      }
    } catch (error) {
      console.error('Erro ao adicionar ao carrinho:', error);
      alert('Erro de conexão com o servidor.');
    } finally {
      setAdicionando(false);
    }
  };

  const formatarPreco = (valorProposto) => {
    const precoVenda = Number(valorProposto || 0) * 1.4;
    return precoVenda.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  };

  if (carregando) {
    return (
      <div className="flex justify-center items-center py-24">
        <div className="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
      </div>
    );
  }

  if (erro || !peca) {
    return (
      <div className="max-w-xl mx-auto bg-white border border-stone-200 rounded-xl p-8 text-center shadow-sm my-8">
        <AlertCircle size={48} className="mx-auto text-red-500 mb-3" />
        <h2 className="text-xl font-bold text-stone-800">{erro || 'Peça não encontrada'}</h2>
        <p className="text-stone-500 text-sm mt-1 mb-6">
          O item solicitado pode ter sido vendido ou removido da vitrine.
        </p>
        <Link
          to="/vitrine"
          className="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-lg transition"
        >
          <ArrowLeft size={18} />
          <span>Voltar para a Vitrine</span>
        </Link>
      </div>
    );
  }

  const indisponivel = Number(peca.disponivel) === 0;

  return (
    <div className="space-y-6 pb-10">
      <div>
        <Link
          to="/vitrine"
          className="inline-flex items-center gap-2 text-stone-600 hover:text-emerald-700 font-semibold text-sm transition"
        >
          <ArrowLeft size={18} />
          <span>Voltar para a Vitrine</span>
        </Link>
      </div>

      <div className="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <div className="bg-stone-100 flex items-center justify-center min-h-[360px] md:min-h-[480px] relative border-b md:border-b-0 md:border-r border-stone-200">
          {peca.foto_caminho ? (
            <img
              src={`${UPLOADS_URL}${peca.foto_caminho}`}
              alt={peca.descricao || 'Foto da peça'}
              className="w-full h-full max-h-[540px] object-cover"
              onError={(e) => {
                e.target.style.display = 'none';
                e.target.nextSibling.style.display = 'flex';
              }}
            />
          ) : null}

          <div
            className={`w-full h-full items-center justify-center text-stone-300 ${
              peca.foto_caminho ? 'hidden' : 'flex'
            }`}
          >
            <Shirt size={96} />
          </div>
        </div>

        <div className="p-6 md:p-8 flex flex-col justify-between space-y-6">
          <div className="space-y-4">
            <div className="flex flex-wrap items-center justify-between gap-2">
              {indisponivel ? (
                <span className="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">
                  <AlertCircle size={14} />
                  <span>Peça Reservada / Vendida</span>
                </span>
              ) : (
                <span className="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full">
                  <CheckCircle2 size={14} />
                  <span>Disponível na Loja Física</span>
                </span>
              )}

              <span className="text-xs font-mono font-semibold text-stone-400">
                Código: #PECA-{peca.id}
              </span>
            </div>

            <h1 className="text-2xl md:text-3xl font-black text-stone-800 leading-tight">
              {peca.descricao || 'Peça de Vestuário Exclusiva'}
            </h1>

            <div className="bg-stone-50 border border-stone-200 rounded-xl p-4 grid grid-cols-2 gap-y-3 gap-x-4 text-sm">
              <div>
                <span className="text-stone-400 block text-xs uppercase font-bold">Categoria</span>
                <span className="font-semibold text-stone-800 flex items-center gap-1 mt-0.5">
                  <Tag size={14} className="text-emerald-600" />
                  {peca.categoria_nome}
                </span>
              </div>

              <div>
                <span className="text-stone-400 block text-xs uppercase font-bold">Marca</span>
                <span className="font-semibold text-stone-800 mt-0.5 block">
                  {peca.marca || 'Não informada'}
                </span>
              </div>

              <div>
                <span className="text-stone-400 block text-xs uppercase font-bold">Tamanho</span>
                <span className="font-semibold text-stone-800 mt-0.5 block">
                  {peca.tamanho || 'Único'}
                </span>
              </div>

              <div>
                <span className="text-stone-400 block text-xs uppercase font-bold">Cor</span>
                <span className="font-semibold text-stone-800 mt-0.5 block">
                  {peca.cor || 'Não informada'}
                </span>
              </div>

              <div className="col-span-2 pt-2 border-t border-stone-200">
                <span className="text-stone-400 block text-xs uppercase font-bold">Estado de Conservação</span>
                <span className="font-semibold text-stone-800 flex items-center gap-1.5 mt-0.5">
                  <Sparkles size={14} className="text-amber-500" />
                  {peca.estado || 'Bom'}
                </span>
              </div>
            </div>

            {peca.observacoes && (
              <div className="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <h4 className="text-xs font-bold uppercase text-amber-800 mb-1">
                  Observações da Triagem
                </h4>
                <p className="text-sm text-amber-900 whitespace-pre-line">
                  {peca.observacoes}
                </p>
              </div>
            )}
          </div>

          <div className="pt-4 border-t border-stone-100 space-y-4">
            <div>
              <span className="text-xs text-stone-400 font-semibold uppercase block">Valor da Peça</span>
              <span className="text-4xl font-black text-emerald-600">
                {formatarPreco(peca.valor_proposto)}
              </span>
            </div>

            <button
              type="button"
              disabled={indisponivel || adicionando}
              onClick={handleAdicionarCarrinho}
              className="w-full py-4 px-6 bg-emerald-600 hover:bg-emerald-700 disabled:bg-stone-300 disabled:cursor-not-allowed text-white rounded-xl font-bold text-base transition shadow-md flex items-center justify-center gap-2"
            >
              <ShoppingCart size={20} />
              <span>
                {indisponivel
                  ? 'Item Indisponível'
                  : adicionando
                  ? 'Adicionando...'
                  : 'Adicionar ao Carrinho'}
              </span>
            </button>

            <p className="text-xs text-stone-500 text-center">
              * Esta peça deve ser retirada presencialmente no balcão da loja após a reserva.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}