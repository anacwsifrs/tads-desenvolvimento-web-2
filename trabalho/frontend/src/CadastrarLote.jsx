import { useState } from 'react';
import { ChevronDown, ChevronUp, Plus, Trash2, Image as ImageIcon } from 'lucide-react';

function SecaoColapsavel({ titulo, abertoPadrao = true, children, acaoCabecalho = null }) {
  const [aberto, setAberto] = useState(abertoPadrao);

  return (
    <div className="bg-white border border-stone-200 rounded-lg shadow-sm mb-4 overflow-hidden flex-shrink-0">
      <div 
        className="flex justify-between items-center p-4 bg-stone-100 cursor-pointer hover:bg-stone-200 transition-colors"
        onClick={() => setAberto(!aberto)}
      >
        <h3 className="text-lg font-bold text-stone-700 flex items-center gap-2">{titulo}</h3>
        <div className="flex items-center gap-4">
          {acaoCabecalho}
          {aberto ? <ChevronUp size={20} className="text-stone-500" /> : <ChevronDown size={20} className="text-stone-500" />}
        </div>
      </div>
      {aberto && (
        <div className="p-4 bg-white">
          {children}
        </div>
      )}
    </div>
  );
}

function ItemLoteColapsavel({ index, peca, atualizarPeca, removerPeca }) {
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
    <div className="bg-white border border-stone-200 rounded-lg mb-4 overflow-hidden">
      <div 
        className="flex justify-between items-center p-3 bg-stone-50 cursor-pointer hover:bg-stone-100 transition-colors border-b border-stone-100"
        onClick={() => setAberto(!aberto)}
      >
        <span className="font-bold text-stone-600">
          Peça {index + 1} {peca.categoria ? `- ${peca.categoria}` : ''}
        </span>
        <div className="flex gap-3 items-center">
          <button 
            type="button" 
            onClick={(e) => { e.stopPropagation(); removerPeca(index); }}
            className="text-red-400 hover:text-red-600 p-1"
            title="Remover peça"
          >
            <Trash2 size={18} />
          </button>
          {aberto ? <ChevronUp size={18} className="text-stone-400" /> : <ChevronDown size={18} className="text-stone-400" />}
        </div>
      </div>

      {aberto && (
        <div className="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-bold text-stone-600 mb-1">Tipo / Categoria:</label>
            <select 
              className="w-full p-2 border border-stone-300 rounded focus:border-stone-500 focus:ring-1 focus:ring-stone-500 outline-none"
              value={peca.categoria}
              onChange={(e) => atualizarPeca(index, 'categoria', e.target.value)}
            >
              <option value="">Selecione...</option>
              <option value="Camiseta">Camiseta</option>
              <option value="Calça">Calça</option>
              <option value="Vestido">Vestido</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-bold text-stone-600 mb-1">Estado da Peça:</label>
            <select 
              className="w-full p-2 border border-stone-300 rounded focus:border-stone-500 outline-none"
              value={peca.estado}
              onChange={(e) => atualizarPeca(index, 'estado', e.target.value)}
            >
              <option value="">Selecione...</option>
              <option value="Nova com etiqueta">Nova com etiqueta</option>
              <option value="Seminova (Perfeita)">Seminova (Perfeita)</option>
              <option value="Usada (Bom estado)">Usada (Bom estado)</option>
            </select>
          </div>

          <div className="md:col-span-2 flex gap-4">
            <input type="text" placeholder="Marca" className="w-full p-2 border border-stone-300 rounded" 
                   value={peca.marca} onChange={(e) => atualizarPeca(index, 'marca', e.target.value)} />
            <input type="text" placeholder="Tamanho (ex: M)" className="w-full p-2 border border-stone-300 rounded" 
                   value={peca.tamanho} onChange={(e) => atualizarPeca(index, 'tamanho', e.target.value)} />
            <input type="text" placeholder="Cor" className="w-full p-2 border border-stone-300 rounded" 
                   value={peca.cor} onChange={(e) => atualizarPeca(index, 'cor', e.target.value)} />
          </div>

          <div className="md:col-span-2">
            <input type="text" placeholder="Descrição breve (ex: Jaqueta corta-vento)" className="w-full p-2 border border-stone-300 rounded" 
                   value={peca.descricao} onChange={(e) => atualizarPeca(index, 'descricao', e.target.value)} />
          </div>

          <div className="md:col-span-2 border-2 border-dashed border-stone-300 rounded-lg p-4 flex flex-col items-center justify-center relative hover:bg-stone-50 transition-colors">
            {preview ? (
              <img src={preview} alt="Preview" className="h-32 object-contain rounded" />
            ) : (
              <div className="text-stone-400 flex flex-col items-center">
                <ImageIcon size={32} className="mb-2" />
                <span>Clique para adicionar a foto da peça</span>
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
  
  const [pecas, setPecas] = useState([
    { categoria: '', estado: '', marca: '', tamanho: '', cor: '', descricao: '', foto: null }
  ]);

  const adicionarPeca = (e) => {
    e.stopPropagation();
    setPecas([...pecas, { categoria: '', estado: '', marca: '', tamanho: '', cor: '', descricao: '', foto: null }]);
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

  return (
    <div className="max-w-4xl mx-auto bg-stone-50 h-screen flex flex-col relative overflow-hidden">
    
      <div className="pt-6 px-6 pb-2 shrink-0">
        <h2 className="text-2xl font-bold text-stone-800">📦 Entrada de Novo Lote</h2>
      </div>
      
      <div className="flex-1 overflow-y-auto px-6 pb-24">
        
        <SecaoColapsavel titulo="1. Dados do Fornecedor" abertoPadrao={false}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" placeholder="CPF" className="w-full p-2 border border-stone-300 rounded" value={fornecedor.cpf} onChange={(e) => setFornecedor({...fornecedor, cpf: e.target.value})} />
            <input type="text" placeholder="Nome Completo" className="w-full p-2 border border-stone-300 rounded" value={fornecedor.nome} onChange={(e) => setFornecedor({...fornecedor, nome: e.target.value})} />
            <input type="email" placeholder="E-mail" className="w-full p-2 border border-stone-300 rounded" value={fornecedor.email} onChange={(e) => setFornecedor({...fornecedor, email: e.target.value})} />
            <input type="text" placeholder="WhatsApp" className="w-full p-2 border border-stone-300 rounded" value={fornecedor.telefone} onChange={(e) => setFornecedor({...fornecedor, telefone: e.target.value})} />
          </div>
        </SecaoColapsavel>

        <SecaoColapsavel titulo="2. Identificação do Lote" abertoPadrao={false}>
          <input 
            type="text" 
            placeholder="Ex: Sacola preta, mala azul..." 
            className="w-full p-2 border border-stone-300 rounded"
            value={descricaoLote}
            onChange={(e) => setDescricaoLote(e.target.value)}
          />
        </SecaoColapsavel>

        <SecaoColapsavel 
          titulo="3. Peças do Lote" 
          abertoPadrao={true}
          acaoCabecalho={
            <button 
              type="button" 
              onClick={adicionarPeca}
              className="bg-amber-600 text-white px-3 py-1 text-sm rounded font-bold hover:bg-amber-700 transition flex items-center gap-1 shadow-sm"
            >
              <Plus size={16} /> Adicionar Peça
            </button>
          }
        >
          <div className="max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
            {pecas.map((peca, index) => (
              <ItemLoteColapsavel 
                key={index} 
                index={index} 
                peca={peca} 
                atualizarPeca={atualizarPeca}
                removerPeca={removerPeca}
              />
            ))}
          </div>
        </SecaoColapsavel>

      </div>

      <div className="absolute bottom-0 left-0 right-0 p-4 bg-stone-50 border-t border-stone-200 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)] z-10">
        <button className="w-full bg-emerald-600 text-white py-4 px-6 rounded-lg font-bold text-lg hover:bg-emerald-700 transition shadow-md">
          Finalizar Cadastro de Lote
        </button>
      </div>

    </div>
  );
}