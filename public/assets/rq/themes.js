export const themes = {
  rqcode: { accent: '#675cff', accent2: '#19d7a5', glow: '103 92 255', logo: 'RQ' },
  fleetway: { accent: '#5267ff', accent2: '#13c999', glow: '82 103 255', logo: 'FW' },
  checklist: { accent: '#8b5cf6', accent2: '#22c55e', glow: '139 92 246', logo: 'ZC' },
  ecv: { accent: '#0ea5e9', accent2: '#2dd4bf', glow: '14 165 233', logo: 'EC' },
  betromano: { accent: '#f59e0b', accent2: '#ef4444', glow: '245 158 11', logo: 'BR' },
  venda_hoje: { accent: '#f43f5e', accent2: '#f59e0b', glow: '244 63 94', logo: 'VH' }
};

export function applyTheme(name = 'venda_hoje', root = document.documentElement) {
  const theme = themes[name] || themes.venda_hoje;
  root.dataset.rqTheme = name;
  root.style.setProperty('--rq-accent', theme.accent);
  root.style.setProperty('--rq-accent-2', theme.accent2);
  root.style.setProperty('--rq-glow-rgb', theme.glow);
  return theme;
}
