import { applyTheme, themes } from './themes.js';

const SpeechRecognition = globalThis.SpeechRecognition || globalThis.webkitSpeechRecognition;

export class RQVoiceController extends EventTarget {
  constructor(options = {}) {
    super();
    this.locale = options.locale || 'pt-BR';
    this.commands = new Map();
    this.state = 'idle';
    this.transcript = '';
    this.recognition = SpeechRecognition ? new SpeechRecognition() : null;
    if (this.recognition) {
      this.recognition.lang = this.locale;
      this.recognition.continuous = false;
      this.recognition.interimResults = true;
      this.recognition.onstart = () => this.#setState('listening');
      this.recognition.onend = () => this.#setState('idle');
      this.recognition.onerror = (event) => this.#emit('error', { error: event.error });
      this.recognition.onresult = (event) => this.#receive(event);
    }
  }

  register(phrase, handler, options = {}) {
    this.commands.set(normalize(phrase), { handler, description: options.description || phrase });
    this.#emit('commandschange', { commands: this.listCommands() });
    return () => this.commands.delete(normalize(phrase));
  }

  listCommands() { return [...this.commands.values()].map(({ description }) => description); }
  supported() { return Boolean(this.recognition); }
  start() {
    if (!this.recognition) return this.#emit('unsupported', {});
    try { this.recognition.start(); } catch { /* already listening */ }
  }
  stop() { this.recognition?.stop(); }

  execute(rawText) {
    const text = normalize(rawText);
    this.transcript = rawText;
    const activeElement = globalThis.document?.activeElement;
    const editable = activeElement?.matches?.('input, textarea, [contenteditable="true"]')
      ? activeElement : null;
    if (editable && /^(digitar|escrever|editar)\s+/.test(text)) {
      const value = rawText.replace(/^(digitar|escrever|editar)\s+/i, '');
      if ('value' in editable) editable.value = value;
      else editable.textContent = value;
      editable.dispatchEvent(new Event('input', { bubbles: true }));
      return this.#emit('executed', { text: rawText, action: 'dictation' });
    }
    const entry = [...this.commands.entries()].find(([phrase]) => text.includes(phrase));
    if (entry) {
      entry[1].handler({ text: rawText, normalized: text });
      return this.#emit('executed', { text: rawText, action: entry[0] });
    }
    this.#emit('nomatch', { text: rawText });
  }

  speak(text) {
    if (!globalThis.speechSynthesis) return;
    speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = this.locale;
    speechSynthesis.speak(utterance);
  }

  #receive(event) {
    const result = event.results[event.results.length - 1];
    const text = result[0].transcript.trim();
    this.#emit('transcript', { text, final: result.isFinal });
    if (result.isFinal) this.execute(text);
  }
  #setState(state) { this.state = state; this.#emit('statechange', { state }); }
  #emit(type, detail) { this.dispatchEvent(new CustomEvent(type, { detail })); }
}

const ElementBase = globalThis.HTMLElement || class {};
export class RQVoiceOrb extends ElementBase {
  connectedCallback() {
    this.controller = this.controller || globalThis.rqVoice || new RQVoiceController();
    globalThis.rqVoice = this.controller;
    this.innerHTML = `<button class="rq-voice__trigger" aria-label="Abrir assistente de voz" aria-expanded="false"><span class="rq-voice__waves" aria-hidden="true">${'<i></i>'.repeat(9)}</span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Zm-7 9a1 1 0 0 1 2 0v1a5 5 0 0 0 10 0v-1a1 1 0 1 1 2 0v1a7 7 0 0 1-6 6.92V21h3a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2h3v-2.08A7 7 0 0 1 5 12v-1Z"/></svg></button><section class="rq-voice__panel" aria-live="polite"><div class="rq-voice__eyebrow"><span></span> Assistente RQ</div><strong>Como posso ajudar?</strong><p>Abra um módulo, execute uma ação ou edite o campo selecionado.</p><div class="rq-voice__transcript">Toque no microfone para falar</div><div class="rq-voice__suggestions"></div><button class="rq-button rq-button--primary rq-voice__listen">Começar a ouvir</button></section>`;
    this.querySelector('.rq-voice__trigger').onclick = () => this.toggle();
    this.querySelector('.rq-voice__listen').onclick = () => this.controller.state === 'listening' ? this.controller.stop() : this.controller.start();
    this.controller.addEventListener('statechange', e => this.#state(e.detail.state));
    this.controller.addEventListener('transcript', e => this.#text(e.detail.text));
    this.controller.addEventListener('executed', e => this.#text(`Feito: ${e.detail.text}`));
    this.controller.addEventListener('nomatch', e => this.#text(`Não reconheci uma ação para “${e.detail.text}”.`));
    this.#renderCommands();
  }
  toggle(force) {
    const open = force ?? !this.classList.contains('is-open');
    this.classList.toggle('is-open', open);
    this.querySelector('.rq-voice__trigger').setAttribute('aria-expanded', String(open));
  }
  #state(state) {
    this.dataset.state = state;
    this.querySelector('.rq-voice__listen').textContent = state === 'listening' ? 'Parar de ouvir' : 'Começar a ouvir';
    if (state === 'listening') { this.toggle(true); this.#text('● Escutando…'); }
  }
  #text(text) { this.querySelector('.rq-voice__transcript').textContent = text; }
  #renderCommands() {
    this.querySelector('.rq-voice__suggestions').innerHTML = this.controller.listCommands().slice(0, 3).map(x => `<button type="button">“${x}”</button>`).join('');
  }
}

function normalize(text) { return String(text).toLocaleLowerCase('pt-BR').normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim(); }

if (globalThis.customElements && !customElements.get('rq-voice-orb')) customElements.define('rq-voice-orb', RQVoiceOrb);
export { applyTheme, themes };
