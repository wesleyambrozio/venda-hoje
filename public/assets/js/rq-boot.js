import { applyTheme } from '../rq/themes.js';
import { RQVoiceController, RQVoiceOrb } from '../rq/rq.js';
import { syncRqTheme, toggleModeFromUi, setPaletteFromUi } from './rq-theme.js';

applyTheme('venda_hoje');
syncRqTheme({
  mode: document.documentElement.dataset.mode || 'dark',
  palette: document.documentElement.dataset.palette || 'rose',
});

if (!customElements.get('rq-voice-orb')) {
  customElements.define('rq-voice-orb', RQVoiceOrb);
}

const voice = new RQVoiceController({ locale: 'pt-BR' });
globalThis.rqVoice = voice;
globalThis.toggleTheme = () => toggleModeFromUi();

const commandsEl = document.getElementById('rq-voice-commands');
if (commandsEl) {
  try {
    const commands = JSON.parse(commandsEl.textContent || '[]');
    commands.forEach((cmd) => {
      voice.register(cmd.phrase, () => {
        if (cmd.action && typeof globalThis[cmd.action] === 'function') {
          globalThis[cmd.action]();
          return;
        }
        if (cmd.url) {
          window.location.href = cmd.url;
        }
      }, { description: cmd.phrase });
    });
  } catch (e) {
    console.warn('Comandos de voz inválidos', e);
  }
}

document.getElementById('rq-theme-toggle')?.addEventListener('click', () => toggleModeFromUi());
document.getElementById('rq-palette-select')?.addEventListener('change', (e) => {
  setPaletteFromUi(e.target.value);
});
document.getElementById('rq-voice-header-toggle')?.addEventListener('click', () => {
  const orb = document.querySelector('rq-voice-orb');
  if (orb) orb.toggle(true);
  voice.start();
});
