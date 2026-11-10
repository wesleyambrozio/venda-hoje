/**
 * Tema claro/escuro + paletas RQCODE para Venda Hoje.
 */

export const VH_PALETTES = {
  indigo: { accent: '#6366f1', accent2: '#13c999', glow: '99 102 241' },
  emerald: { accent: '#10b981', accent2: '#34d399', glow: '16 185 129' },
  rose: { accent: '#f43f5e', accent2: '#fbbf24', glow: '244 63 94' },
  amber: { accent: '#f59e0b', accent2: '#fbbf24', glow: '245 158 11' },
  cyan: { accent: '#06b6d4', accent2: '#22d3ee', glow: '6 182 212' },
  violet: { accent: '#8b5cf6', accent2: '#a78bfa', glow: '139 92 246' },
};

const LIGHT_TOKENS = {
  '--rq-bg': '#f1f4fa',
  '--rq-surface': 'rgba(255,255,255,.9)',
  '--rq-surface-strong': 'rgba(255,255,255,.96)',
  '--rq-border': 'rgba(15,23,42,.1)',
  '--rq-text': '#0f172a',
  '--rq-muted': '#64748b',
  '--rq-shadow': '0 18px 50px rgba(15,23,42,.1)',
};

const DARK_TOKENS = {
  '--rq-bg': '#070b16',
  '--rq-surface': 'rgba(17,24,45,.72)',
  '--rq-surface-strong': 'rgba(24,33,60,.92)',
  '--rq-border': 'rgba(255,255,255,.1)',
  '--rq-text': '#f7f8ff',
  '--rq-muted': '#96a1bd',
  '--rq-shadow': '0 24px 80px rgba(0,0,0,.34)',
};

export function syncRqTheme(opts = {}) {
  const root = opts.root || document.documentElement;
  const mode = opts.mode || root.dataset.mode || 'dark';
  const palette = opts.palette || root.dataset.palette || 'rose';
  const tokens = mode === 'light' ? LIGHT_TOKENS : DARK_TOKENS;
  const colors = VH_PALETTES[palette] || VH_PALETTES.rose;

  root.dataset.mode = mode;
  root.dataset.palette = palette;
  root.dataset.rqTheme = 'venda_hoje';
  root.style.colorScheme = mode === 'light' ? 'light' : 'dark';

  Object.entries(tokens).forEach(([key, value]) => {
    root.style.setProperty(key, value);
  });
  root.style.setProperty('--rq-accent', colors.accent);
  root.style.setProperty('--rq-accent-2', colors.accent2);
  root.style.setProperty('--rq-glow-rgb', colors.glow);

  // Bridge para classes legadas do app
  root.style.setProperty('--primary', colors.accent);
  root.style.setProperty('--primary-light', colors.accent2);
  root.style.setProperty('--primary-dark', colors.accent);
  root.style.setProperty('--secondary', colors.accent2);
  root.style.setProperty('--bg', tokens['--rq-bg']);
  root.style.setProperty('--bg-dark', mode === 'light' ? '#e8edf5' : '#0c1222');
  root.style.setProperty('--surface', mode === 'light' ? '#ffffff' : 'rgba(24,33,60,.92)');
  root.style.setProperty('--text', tokens['--rq-text']);
  root.style.setProperty('--text-muted', tokens['--rq-muted']);
  root.style.setProperty('--border', tokens['--rq-border']);
  root.style.setProperty('--shadow', tokens['--rq-shadow']);

  document.body?.classList.toggle('rq-mode-light', mode === 'light');
  document.body?.classList.toggle('rq-mode-dark', mode === 'dark');

  return { mode, palette, colors };
}

export function persistRqTheme({ mode, palette, themeSaveUrl, csrf }) {
  if (!themeSaveUrl || !csrf) return Promise.resolve();
  const body = new URLSearchParams({ _csrf: csrf });
  if (mode) body.set('theme_mode', mode);
  if (palette) body.set('color_palette', palette);
  return fetch(themeSaveUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-CSRF-Token': csrf,
      'X-Requested-With': 'XMLHttpRequest',
      Accept: 'application/json',
    },
    body,
    credentials: 'same-origin',
  }).catch(() => {});
}

export function applyAndPersistTheme({ mode, palette, themeSaveUrl, csrf, persistLocal = true }) {
  const result = syncRqTheme({ mode, palette });
  if (persistLocal) {
    try {
      localStorage.setItem('vh_mode', result.mode);
      localStorage.setItem('vh_palette', result.palette);
    } catch { /* ignore */ }
  }
  persistRqTheme({
    mode: result.mode,
    palette: result.palette,
    themeSaveUrl,
    csrf,
  });
  return result;
}

export function toggleModeFromUi() {
  const root = document.documentElement;
  const body = document.body;
  const next = root.dataset.mode === 'dark' ? 'light' : 'dark';
  return applyAndPersistTheme({
    mode: next,
    palette: root.dataset.palette || 'rose',
    themeSaveUrl: body?.dataset?.themeSaveUrl || '',
    csrf: body?.dataset?.csrf || '',
  });
}

export function setPaletteFromUi(palette) {
  const body = document.body;
  return applyAndPersistTheme({
    mode: document.documentElement.dataset.mode || 'dark',
    palette,
    themeSaveUrl: body?.dataset?.themeSaveUrl || '',
    csrf: body?.dataset?.csrf || '',
  });
}
