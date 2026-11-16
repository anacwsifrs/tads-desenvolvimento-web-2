import { ShieldCheck, Clock, Award, FileCheck2 } from 'lucide-react';

export default function Termos() {
  return (
    <div className="space-y-6 pb-8">
      <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-stone-200 pb-5">
        <div>
          <h2 className="text-2xl font-black text-stone-800 flex items-center gap-2">
            <FileCheck2 className="text-emerald-600" size={26} />
            <span>Termos de Serviço, Privacidade (LGPD) e Integridade</span>
          </h2>
          <p className="text-stone-500 text-sm mt-1">
            Diretrizes legais de proteção de dados, regras de triagem de lotes e declaração acadêmica.
          </p>
        </div>

        <span className="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold px-3 py-1.5 rounded-lg self-start sm:self-auto">
          <FileCheck2 size={14} className="text-emerald-600" />
          <span>Documentação Oficial</span>
        </span>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <section className="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-3 flex flex-col justify-between">
          <div className="space-y-3">
            <div className="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
              <ShieldCheck size={22} />
            </div>
            <h3 className="text-lg font-bold text-stone-800">
              1. Proteção de Dados Pessoais (LGPD)
            </h3>
            <p className="text-sm text-stone-600 leading-relaxed">
              Em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018), o{' '}
              <strong className="text-stone-800">Intent Brechó</strong> garante que os dados cadastrais
              (CPF, e-mail, telefone e chave PIX) são utilizados exclusivamente para o gerenciamento de
              lotes e pagamentos na plataforma, sem compartilhamento com terceiros.
            </p>
          </div>
          <span className="text-xs font-semibold text-emerald-700 pt-2 border-t border-stone-100">
            Lei nº 13.709/2018
          </span>
        </section>

        <section className="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-3 flex flex-col justify-between">
          <div className="space-y-3">
            <div className="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
              <Clock size={22} />
            </div>
            <h3 className="text-lg font-bold text-stone-800">
              2. Condições de Avaliação e Prazos
            </h3>
            <p className="text-sm text-stone-600 leading-relaxed">
              Os lotes cadastrados passam por triagem física. Caso a proposta apresentada pela loja não
              seja respondida em até <strong className="text-stone-800">3 dias úteis</strong>, ou caso a
              proposta seja recusada e as peças não sejam retiradas em até{' '}
              <strong className="text-stone-800">3 dias úteis</strong>, as roupas serão automaticamente
              destinadas para doação.
            </p>
          </div>
          <span className="text-xs font-semibold text-amber-700 pt-2 border-t border-stone-100">
            Prazo padrão: 3 dias úteis
          </span>
        </section>

        <section className="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-3 flex flex-col justify-between">
          <div className="space-y-3">
            <div className="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
              <Award size={22} />
            </div>
            <h3 className="text-lg font-bold text-stone-800">
              3. Integridade Acadêmica e Anti-Plágio
            </h3>
            <p className="text-sm text-stone-600 leading-relaxed">
              Declara-se para fins acadêmicos que este projeto de aplicação web é de autoria individual e
              desenvolvido estritamente para avaliação na disciplina de Desenvolvimento Web, respeitando os
              direitos autorais e as diretrizes estipuladas no edital.
            </p>
          </div>
          <span className="text-xs font-semibold text-sky-700 pt-2 border-t border-stone-100">
            Autoria Individual
          </span>
        </section>
      </div>
    </div>
  );
}