import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Search, ShoppingCart, Eye, Shirt, Tag } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function Vitrine() {
  const navigate = useNavigate();
  const [pecas, setPecas] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [adicionandoId, setAdicionandoId] = useState(null);
  const [busca, setBusca] = useState('');
  const [categoriaFiltro, setCategoriaFiltro] = useState('');

  useEffect(() => {
    const carregarVitrine = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=vitrine`);
        const dados = await resposta.json();
        setPecas(Array.isArray(dados) ? dados : []);
      } catch (error) {
        console.error('Erro ao carregar vitrine:', error);
      } finally {
        setCarregando(false);
      }
    };

    carregarVitrine();
  }, []);

  const handleAdicionarCarrinho = async (pecaId) => {
    setAdicionandoId(pecaId);
    try {
      const resposta = await fetch(`${API_URL}?acao=adicionar_carrinho&id=${pecaId}`, {
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
      setAdicionandoId(null);
    }
  };

  const categoriasDisponiveis = [...new Set(pecas.map(p => p.categoria_nome))].sort();
  const pecasFiltradas = pecas.filter(p => {
    const termo = busca.toLowerCase();
    const bateTexto =
      (p.descricao || '').toLowerCase().includes(termo) ||
      (p.marca || '').toLowerCase().includes(termo) ||
      (p.categoria_nome || '').toLowerCase().includes(termo);
    const bateCategoria = categoriaFiltro === '' || p.categoria_nome === categoriaFiltro;
    return bateTexto && bateCategoria;
  });

  const formatarPreco = (valorProposto) => {
    const precoVenda = Number(valorProposto || 0) * 1.4;
    return precoVenda.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  };

  return (
    <div className="space-y-6 pb-8">
      <div className="bg-white p-6 rounded-xl border border-stone-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="text-2xl font-black text-stone-800">Vitrine de Peças Disponíveis</h2>
          <p className="text-stone-500 text-sm mt-1">
            Confira os itens selecionados e higienizados prontos para compra presencial ou reserva.
          </p>
        </div>

        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative">
            <Search className="absolute left-3 top-2.5 text-stone-400" size={18} />
            <input
              type="text"
              placeholder="Buscar peça, marca..."
              value={busca}
              onChange={(e) => setBusca(e.target.value)}
              className="pl-9 pr-4 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 w-full sm:w-56"
            />
          </div>

          <select
            value={categoriaFiltro}
            onChange={(e) => setCategoriaFiltro(e.target.value)}
            className="px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
          >
            <option value="">Todas as categorias</option>
            {categoriasDisponiveis.map((cat) => (
              <option key={cat} value={cat}>{cat}</option>
            ))}
          </select>
        </div>
      </div>

      {carregando ? (
        <div className="flex justify-center items-center py-20">
          <div className="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
      ) : pecasFiltradas.length === 0 ? (
        <div className="bg-white border border-stone-200 rounded-xl p-12 text-center text-stone-500">
          <Shirt size={48} className="mx-auto mb-3 text-stone-300" />
          <p className="font-semibold text-lg text-stone-700">Nenhuma peça encontrada na vitrine no momento.</p>
          <p className="text-sm mt-1">Tente limpar os filtros ou volte em breve para conferir novidades.</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {pecasFiltradas.map((peca) => (
            <div
              key={peca.id}
              className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between group"
            >
              <div>
                <div className="h-56 bg-stone-100 relative overflow-hidden flex items-center justify-center">
                  {peca.foto_caminho ? (
                    <img
                      src={`${UPLOADS_URL}${peca.foto_caminho}`}
                      alt={peca.descricao || 'Foto da peça'}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
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
                    <Shirt size={64} />
                  </div>

                  <span className="absolute top-3 left-3 bg-stone-800/85 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md flex items-center gap-1">
                    <Tag size={12} className="text-emerald-400" />
                    {peca.categoria_nome}
                  </span>
                </div>

                <div className="p-4">
                  <h3 className="font-bold text-stone-800 text-base line-clamp-1">
                    {peca.descricao || 'Peça de Vestuário'}
                  </h3>

                  <div className="flex items-center gap-3 mt-1.5 text-xs text-stone-500">
                    {peca.tamanho && (
                      <span>
                        Tamanho: <strong className="text-stone-700">{peca.tamanho}</strong>
                      </span>
                    )}
                    {peca.marca && (
                      <span>
                        • Marca: <strong className="text-stone-700">{peca.marca}</strong>
                      </span>
                    )}
                  </div>

                  <p className="text-2xl font-black text-emerald-600 mt-3">
                    {formatarPreco(peca.valor_proposto)}
                  </p>
                </div>
              </div>

              <div className="p-4 pt-0 flex gap-2">
                <Link
                  to={`/detalhar/${peca.id}`}
                  className="flex-1 py-2.5 px-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-sm font-bold transition flex items-center justify-center gap-1.5"
                >
                  <Eye size={16} />
                  <span>Detalhes</span>
                </Link>
                <button
                  type="button"
                  disabled={adicionandoId === peca.id}
                  onClick={() => handleAdicionarCarrinho(peca.id)}
                  className="flex-1 py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white rounded-lg text-sm font-bold transition flex items-center justify-center gap-1.5 shadow-sm"
                >
                  <ShoppingCart size={16} />
                  <span>{adicionandoId === peca.id ? '...' : 'Comprar'}</span>
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}