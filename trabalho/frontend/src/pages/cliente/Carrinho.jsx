import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { ShoppingCart, Trash2, CheckCircle2, Wallet, Store, Shirt } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php';
const UPLOADS_URL = 'http://localhost/brecho/uploads/';

export default function Carrinho() {
  const navigate = useNavigate();
  const [itens, setItens] = useState([]);
  const [saldo, setSaldo] = useState(0);
  const [usarSaldo, setUsarSaldo] = useState(false);
  const [carregando, setCarregando] = useState(true);
  const [finalizando, setFinalizando] = useState(false);

  const carregarCarrinho = async () => {
    try {
      const resposta = await fetch(`${API_URL}?acao=carrinho`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok) {
        const lista = dados.itens ? Object.values(dados.itens) : [];
        setItens(lista);
        setSaldo(Number(dados.saldo_credito || 0));
      }
    } catch (error) {
      console.error('Erro ao carregar carrinho:', error);
    } finally {
      setCarregando(false);
    }
  };

  useEffect(() => {
    carregarCarrinho();
  }, []);

  const handleRemover = async (id) => {
    try {
      const resposta = await fetch(`${API_URL}?acao=remover_carrinho&id=${id}`, {
        credentials: 'include'
      });
      const dados = await resposta.json();
      if (resposta.ok && dados.sucesso) {
        setItens(dados.itens ? Object.values(dados.itens) : []);
      }
    } catch (error) {
      alert('Erro ao remover item.');
    }
  };

  const subtotal = itens.reduce((soma, item) => soma + Number(item.preco_venda || 0), 0);
  const desconto = usarSaldo ? Math.min(subtotal, saldo) : 0;
  const totalFinal = Math.max(0, subtotal - desconto);

  const handleFinalizar = async (e) => {
    e.preventDefault();
    setFinalizando(true);

    try {
      const resposta = await fetch(`${API_URL}?acao=finalizar_compra`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ usar_saldo: usarSaldo })
      });
      const dados = await resposta.json();

      if (resposta.ok && dados.sucesso) {
        if (dados.novo_saldo !== null) {
          const userLocal = JSON.parse(localStorage.getItem('brecho_usuario') || '{}');
          localStorage.setItem(
            'brecho_usuario',
            JSON.stringify({ ...userLocal, saldo_credito: dados.novo_saldo })
          );
          window.dispatchEvent(new Event('usuarioAtualizado'));
        }
        alert(dados.mensagem);
        navigate('/meus-pedidos');
      } else {
        alert(dados.mensagem || 'Erro ao processar compra.');
      }
    } catch (error) {
      alert('Erro de conexão com o servidor.');
    } finally {
      setFinalizando(false);
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

  return (
    <div className="space-y-6 pb-8">
      <div className="border-b border-stone-200 pb-5">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          <ShoppingCart className="text-emerald-600" size={26} />
          <span>Meu Carrinho de Compras</span>
        </h2>
        <p className="text-stone-500 text-sm mt-1">
          Revise as peças selecionadas, aplique seu saldo de crédito e conclua a reserva.
        </p>
      </div>

      {itens.length === 0 ? (
        <div className="bg-white p-12 text-center rounded-2xl border border-stone-200 shadow-sm space-y-4">
          <div className="w-16 h-16 bg-stone-100 rounded-full flex items-center justify-center mx-auto text-stone-400">
            <ShoppingCart size={32} />
          </div>
          <h3 className="text-xl font-bold text-stone-700">Seu carrinho está vazio!</h3>
          <p className="text-stone-500 text-sm">
            Explore peças exclusivas selecionadas na nossa vitrine.
          </p>
          <Link
            to="/vitrine"
            className="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm"
          >
            <Store size={18} />
            <span>Voltar para a Vitrine</span>
          </Link>
        </div>
      ) : (
        <form onSubmit={handleFinalizar} className="space-y-6">
          <div className="bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-stone-100 border-b border-stone-200 text-xs uppercase text-stone-600 font-bold">
                  <th className="p-4">Peça</th>
                  <th className="p-4">Valor Venda</th>
                  <th className="p-4 w-28 text-center">Ação</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200 text-sm">
                {itens.map((item) => (
                  <tr key={item.id} className="hover:bg-stone-50 transition-colors">
                    <td className="p-4">
                      <div className="flex items-center gap-3">
                        {item.foto_caminho ? (
                          <img
                            src={`${UPLOADS_URL}${item.foto_caminho}`}
                            alt={item.descricao}
                            className="w-14 h-14 object-cover rounded-lg border border-stone-200"
                          />
                        ) : (
                          <div className="w-14 h-14 rounded-lg bg-stone-100 flex items-center justify-center text-stone-400">
                            <Shirt size={22} />
                          </div>
                        )}
                        <strong className="text-stone-800">{item.descricao}</strong>
                      </div>
                    </td>
                    <td className="p-4 font-bold text-stone-800">
                      {formatarMoeda(item.preco_venda)}
                    </td>
                    <td className="p-4 text-center">
                      <button
                        type="button"
                        onClick={() => handleRemover(item.id)}
                        className="inline-flex items-center gap-1 text-red-600 hover:text-red-700 font-bold text-xs bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition"
                      >
                        <Trash2 size={14} />
                        <span>Remover</span>
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {saldo > 0 && (
            <label className="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl cursor-pointer select-none">
              <input
                type="checkbox"
                checked={usarSaldo}
                onChange={(e) => setUsarSaldo(e.target.checked)}
                className="w-5 h-5 accent-emerald-600 rounded"
              />
              <Wallet className="text-amber-600 shrink-0" size={20} />
              <span className="font-bold text-amber-900 text-sm md:text-base">
                Usar meu saldo disponível de {formatarMoeda(saldo)}
              </span>
            </label>
          )}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            <div className="bg-emerald-50 border border-emerald-200 rounded-xl p-6 flex flex-col justify-between space-y-4">
              <div>
                <h3 className="text-lg font-black text-emerald-900">Confirmar Pedido</h3>
                <p className="text-sm text-emerald-800 mt-1">
                  {totalFinal === 0
                    ? 'Seu saldo cobre o valor total do pedido. Após confirmar, o administrador validará a baixa para sua retirada!'
                    : 'Ao finalizar, você será redirecionado para a tela de compras onde poderá anexar o comprovante do PIX.'}
                </p>
              </div>
              <button
                type="submit"
                disabled={finalizando}
                className="w-full py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-xl transition shadow-md flex items-center justify-center gap-2 disabled:opacity-60"
              >
                <CheckCircle2 size={20} />
                <span>
                  {finalizando
                    ? 'Processando...'
                    : totalFinal === 0
                    ? 'Finalizar Compra com Saldo'
                    : 'Gerar Pedido & Reservar'}
                </span>
              </button>
            </div>

            <div className="bg-white border border-stone-200 rounded-xl p-6 flex flex-col justify-center text-right space-y-2 shadow-sm">
              <div className="text-stone-600 text-sm">
                Subtotal: <strong className="text-stone-800">{formatarMoeda(subtotal)}</strong>
              </div>
              {usarSaldo && desconto > 0 && (
                <div className="text-red-600 text-sm font-semibold">
                  Desconto do Saldo: <strong>- {formatarMoeda(desconto)}</strong>
                </div>
              )}
              <div className="pt-3 border-t border-stone-100 text-lg font-bold text-stone-800">
                Total a Pagar:{' '}
                <span className="text-3xl font-black text-emerald-600 block sm:inline">
                  {formatarMoeda(totalFinal)}
                </span>
              </div>
            </div>
          </div>
        </form>
      )}
    </div>
  );
}