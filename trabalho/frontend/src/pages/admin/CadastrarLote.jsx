import { useState, useEffect } from 'react';
import { ChevronDown, ChevronUp, Plus, Trash2, Image as ImageIcon, Search } from 'lucide-react';

const API_URL = 'http://localhost/brecho/index.php'; 

// Classes CSS padrão para reutilizar nos inputs e selects e remover o visual cinza nativo
const inputClass = "w-full p-2.5 bg-white text-stone-700 border border-stone-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm appearance-none";
const selectClass = "w-full p-2.5 bg-white text-stone-700 border border-stone-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm";

function SecaoColapsavel({ titulo, abertoPadrao = true, children, acaoCabecalho = null }) {
  const [aberto, setAberto] = useState(abertoPadrao);

  return (
    <div className="bg-white border border-stone-200 rounded-lg shadow-sm mb-4 overflow-hidden flex-shrink-0">
      <div 
        className="flex justify-between items-center p-4 bg-stone-50 cursor-pointer hover:bg-stone-100 transition-colors"
        onClick={() => setAberto(!aberto)}
      >
        <h3 className="text-lg font-bold text-stone-700 flex items-center gap-2">{titulo}</h3>
        <div className="flex items-center gap-4">
          {acaoCabecalho}
          {aberto ? <ChevronUp size={20} className="text-stone-500" /> : <ChevronDown size={20} className="text-stone-500" />}
        </div>
      </div>
      {aberto && <div className="p-5 bg-white">{children}</div>}
    </div>
  );
}

function ItemLoteColapsavel({ index, peca, atualizarPeca, removerPeca, categoriasBD }) {
  const [aberto, setAberto] = useState(true);
  const [preview, setPreview] = useState(null);

  const lidarComFoto = (e) => {
    const file = e.target.files[0];
    if (file) {
      setPreview(URL.createObjectURL(file));
      atualizarPeca(index, 'foto', file);
    }
  };

  return (
    <div className="bg-white border border-stone-200 rounded-lg mb-4 overflow-hidden shadow-sm">
      <div 
        className="flex justify-between items-center p-3 bg-stone-50 cursor-pointer hover:bg-stone-100 transition-colors border-b border-stone-100"
        onClick={() => setAberto(!aberto)}
      >
        <span className="font-bold text-stone-600">
          Peça {index + 1} {peca.categoria ? `- Categoria Selecionada` : ''}
        </span>
        <div className="flex gap-3 items-center">
          <button 
            type="button" 
            onClick={(e) => { e.stopPropagation(); removerPeca(index); }}
            className="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded transition-colors"
            title="Remover peça"
          >
            <Trash2 size={18} />
          </button>
          {aberto ? <ChevronUp size={18} className="text-stone-400" /> : <ChevronDown size={18} className="text-stone-400" />}
        </div>
      </div>

      {aberto && (
        <div className="p-4 grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">Categoria:</label>
            <select 
              className={selectClass}
              value={peca.categoria}
              onChange={(e) => atualizarPeca(index, 'categoria', e.target.value)}
            >
              <option value="">Selecione uma categoria...</option>
              {categoriasBD.map(cat => (
                <option key={cat.id} value={cat.id}>{cat.nome}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">Estado da Peça:</label>
            <select 
              className={selectClass}
              value={peca.estado}
              onChange={(e) => atualizarPeca(index, 'estado', e.target.value)}
            >
              <option value="">Selecione o estado...</option>
              <option value="Nova com etiqueta">Nova (Com etiqueta)</option>
              <option value="Seminova - Excelente">Seminova (Excelente estado)</option>
              <option value="Usada - Bom estado">Usada (Bom estado)</option>
              <option value="Usada - Com marcas de uso">Usada (Com marcas de uso)</option>
            </select>
          </div>

          <div className="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">Marca:</label>
              <input type="text" placeholder="Ex: Zara, Renner" className={inputClass} 
                     value={peca.marca} onChange={(e) => atualizarPeca(index, 'marca', e.target.value)} />
            </div>
            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">Tamanho:</label>
              <input type="text" placeholder="Ex: M, 40" className={inputClass} 
                     value={peca.tamanho} onChange={(e) => atualizarPeca(index, 'tamanho', e.target.value)} />
            </div>
            <div>
              <label className="block text-sm font-semibold text-stone-700 mb-1.5">Cor predominante:</label>
              <input type="text" placeholder="Ex: Preto" className={inputClass} 
                     value={peca.cor} onChange={(e) => atualizarPeca(index, 'cor', e.target.value)} />
            </div>
          </div>

          <div className="md:col-span-2">
            <label className="block text-sm font-semibold text-stone-700 mb-1.5">Descrição Breve:</label>
            <input type="text" placeholder="Ex: Jaqueta corta-vento com capuz" className={inputClass} 
                   value={peca.descricao} onChange={(e) => atualizarPeca(index, 'descricao', e.target.value)} />
          </div>

          <div className="md:col-span-2">
             <label className="block text-sm font-semibold text-stone-700 mb-1.5">Observações da Triagem (Opcional):</label>
            <input type="text" placeholder="Anotações internas sobre o estado real da peça" className={inputClass} 
                   value={peca.observacoes} onChange={(e) => atualizarPeca(index, 'observacoes', e.target.value)} />
          </div>

          <div className="md:col-span-2 border-2 border-dashed border-stone-300 bg-stone-50 hover:bg-stone-100 rounded-lg p-6 flex flex-col items-center justify-center relative transition-colors group cursor-pointer mt-2">
            {preview ? (
              <img src={preview} alt="Preview" className="h-40 object-contain rounded shadow-sm" />
            ) : (
              <div className="text-stone-500 flex flex-col items-center group-hover:text-emerald-600 transition-colors">
                <ImageIcon size={40} className="mb-3 opacity-70" />
                <span className="font-medium">Clique ou arraste para adicionar a foto da peça</span>
                <span className="text-xs text-stone-400 mt-1">Formatos aceitos: JPG, PNG, WEBP</span>
              </div>
            )}
            <input type="file" accept="image/*" onChange={lidarComFoto} className="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
          </div>
        </div>
      )}
    </div>
  );
}

export default function CadastrarLote() {
  const [fornecedor, setFornecedor] = useState({ cpf: '', nome: '', email: '', telefone: '', pix: '' });
  const [descricaoLote, setDescricaoLote] = useState('');
  const [buscandoCpf, setBuscandoCpf] = useState(false);
  const [salvando, setSalvando] = useState(false);
  const [categoriasBD, setCategoriasBD] = useState([]);
  
  const [pecas, setPecas] = useState([
    { categoria: '', estado: '', marca: '', tamanho: '', cor: '', descricao: '', observacoes: '', foto: null }
  ]);

  // Busca as categorias do banco de dados ao carregar a tela
  useEffect(() => {
    const buscarCategorias = async () => {
      try {
        const resposta = await fetch(`${API_URL}?acao=categorias`);
        const dados = await resposta.json();
        setCategoriasBD(dados);
      } catch (error) {
        console.error("Erro ao carregar categorias:", error);
      }
    };
    buscarCategorias();
  }, []);

  const buscarCliente = async () => {
    if (fornecedor.cpf.length < 11) return;
    setBuscandoCpf(true);
    try {
      const resposta = await fetch(`${API_URL}?acao=buscar_cliente&cpf=${fornecedor.cpf}`);
      const dados = await resposta.json();
      
      if (dados && dados.id) {
        setFornecedor({
          ...fornecedor,
          nome: dados.nome || '',
          email: dados.email || '',
          telefone: dados.telefone || '',
          pix: dados.chave_pix || ''
        });
      }
    } catch (error) {
      console.error("Erro ao buscar cliente", error);
    } finally {
      setBuscandoCpf(false);
    }
  };

  const adicionarPeca = (e) => {
    e.stopPropagation();
    setPecas([...pecas, { categoria: '', estado: '', marca: '', tamanho: '', cor: '', descricao: '', observacoes: '', foto: null }]);
  };

  const removerPeca = (indexParaRemover) => {
    if (pecas.length === 1) return alert("O lote precisa ter pelo menos uma peça.");
    setPecas(pecas.filter((_, index) => index !== indexParaRemover));
  };

  const atualizarPeca = (index, campo, valor) => {
    const novasPecas = [...pecas];
    novasPecas[index][campo] = valor;
    setPecas(novasPecas);
  };

  const salvarLote = async () => {
    setSalvando(true);
    const formData = new FormData();
    
    formData.append('cpf', fornecedor.cpf);
    formData.append('nome', fornecedor.nome);
    formData.append('email', fornecedor.email);
    formData.append('telefone', fornecedor.telefone);
    formData.append('pix', fornecedor.pix);
    formData.append('descricao_lote', descricaoLote);

    pecas.forEach((peca, index) => {
      formData.append(`peca_categoria[${index}]`, peca.categoria);
      formData.append(`peca_descricao[${index}]`, peca.descricao);
      formData.append(`peca_marca[${index}]`, peca.marca);
      formData.append(`peca_tamanho[${index}]`, peca.tamanho);
      formData.append(`peca_cor[${index}]`, peca.cor);
      formData.append(`peca_estado[${index}]`, peca.estado);
      formData.append(`peca_observacoes[${index}]`, peca.observacoes);
      
      if (peca.foto) {
        formData.append(`peca_foto[${index}]`, peca.foto);
      }
    });

    try {
      const resposta = await fetch(`${API_URL}?acao=salvar_lote`, {
        method: 'POST',
        body: formData
      });
      
      if(resposta.ok) {
        alert("Lote cadastrado com sucesso!");
      }
    } catch (error) {
      alert("Erro de conexão com o servidor.");
    } finally {
      setSalvando(false);
    }
  };

  return (
    <div className="max-w-4xl mx-auto bg-stone-100 h-[calc(100vh-140px)] flex flex-col relative overflow-hidden">
      <div className="pt-8 px-6 pb-4 shrink-0 bg-white border-b border-stone-200 shadow-sm z-20">
        <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
          📦 Entrada de Novo Lote
        </h2>
        <p className="text-stone-500 text-sm mt-1">Preencha os dados do fornecedor e adicione as peças do lote.</p>
      </div>
      
      <div className="flex-1 overflow-y-auto px-6 pt-6 pb-28 custom-scrollbar">
        <SecaoColapsavel titulo="1. Dados do Fornecedor" abertoPadrao={true}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 relative">
            <div className="relative">
              <input 
                type="text" 
                placeholder="CPF (Apenas números)" 
                className={`${inputClass} pr-10`} 
                value={fornecedor.cpf} 
                onChange={(e) => setFornecedor({...fornecedor, cpf: e.target.value})}
                onBlur={buscarCliente}
              />
              {buscandoCpf ? (
                <div className="absolute right-3 top-3 w-5 h-5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
              ) : (
                <Search className="absolute right-3 top-3 text-stone-400" size={20} />
              )}
            </div>
            <input type="text" placeholder="Nome Completo" className={inputClass} value={fornecedor.nome} onChange={(e) => setFornecedor({...fornecedor, nome: e.target.value})} />
            <input type="email" placeholder="E-mail" className={inputClass} value={fornecedor.email} onChange={(e) => setFornecedor({...fornecedor, email: e.target.value})} />
            <input type="text" placeholder="WhatsApp" className={inputClass} value={fornecedor.telefone} onChange={(e) => setFornecedor({...fornecedor, telefone: e.target.value})} />
            <input type="text" placeholder="Chave PIX" className={`${inputClass} md:col-span-2`} value={fornecedor.pix} onChange={(e) => setFornecedor({...fornecedor, pix: e.target.value})} />
          </div>
        </SecaoColapsavel>

        <SecaoColapsavel titulo="2. Identificação do Lote" abertoPadrao={false}>
          <input 
            type="text" 
            placeholder="Ex: Sacola preta de lixo, Mala azul pequena..." 
            className={inputClass}
            value={descricaoLote}
            onChange={(e) => setDescricaoLote(e.target.value)}
          />
        </SecaoColapsavel>

        <SecaoColapsavel 
          titulo="3. Peças do Lote" 
          abertoPadrao={true}
          acaoCabecalho={
            <button type="button" onClick={adicionarPeca} className="bg-amber-500 text-white px-4 py-2 text-sm rounded-md font-bold hover:bg-amber-600 transition-colors flex items-center gap-1.5 shadow-sm">
              <Plus size={18} /> Adicionar Peça
            </button>
          }
        >
          <div className="max-h-[55vh] overflow-y-auto pr-2 custom-scrollbar">
            {pecas.map((peca, index) => (
              <ItemLoteColapsavel key={index} index={index} peca={peca} atualizarPeca={atualizarPeca} removerPeca={removerPeca} categoriasBD={categoriasBD} />
            ))}
          </div>
        </SecaoColapsavel>
      </div>

      <div className="absolute bottom-0 left-0 right-0 p-5 bg-white border-t border-stone-200 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] z-30">
        <button 
          onClick={salvarLote}
          disabled={salvando}
          className="w-full bg-emerald-600 text-white py-4 px-6 rounded-lg font-bold text-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 transition-all shadow-md disabled:opacity-70 disabled:cursor-not-allowed flex justify-center items-center gap-2"
        >
          {salvando ? (
            <><div className="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Salvando Lote...</>
          ) : 'Finalizar Cadastro de Lote'}
        </button>
      </div>
    </div>
  );
}