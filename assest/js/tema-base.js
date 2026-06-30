document.addEventListener('DOMContentLoaded', () => {
  if (document.body) {
    document.body.setAttribute('data-menu-color', 'light');
  }

  const iconMap = [
    { words: ['inicio', 'dashboard'], icon: 'home' },
    { words: ['granel', 'fruta'], icon: 'leaf' },
    { words: ['recepcion', 'recepción'], icon: 'inbox' },
    { words: ['despacho'], icon: 'truck' },
    { words: ['guia', 'guía'], icon: 'file' },
    { words: ['materia prima'], icon: 'box' },
    { words: ['industrial', 'proceso'], icon: 'factory' },
    { words: ['envases', 'embalaje'], icon: 'cubes' },
    { words: ['packing'], icon: 'grid' },
    { words: ['materiales'], icon: 'cube' },
    { words: ['administracion', 'administración'], icon: 'briefcase' },
    { words: ['orden compra', 'compra'], icon: 'cart' },
    { words: ['existencia', 'inventario', 'stock'], icon: 'chart' },
    { words: ['resumen', 'estadistica', 'estadística'], icon: 'trend' },
    { words: ['historial'], icon: 'history' },
    { words: ['calidad', 'inspeccion', 'inspección'], icon: 'check' },
    { words: ['exportadora', 'exportacion', 'exportación'], icon: 'plane' },
    { words: ['usuario', 'perfil'], icon: 'user' },
    { words: ['registro'], icon: 'edit' },
  ];

  const iconPaths = {
    home: '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9.5 21v-6h5v6"/>',
    leaf: '<path d="M20 4c-7.5.2-12.5 3.9-14 10.8C4.9 20 9.8 22 13.2 18.6 16.9 15 18.8 9.4 20 4Z"/><path d="M6 19c2.7-4.4 6.1-7.5 10.8-9.3"/>',
    inbox: '<path d="M4 4h16l-2 9H6L4 4Z"/><path d="M6 13l2.5 4h7L18 13"/><path d="M4 20h16"/>',
    truck: '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
    file: '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/>',
    box: '<path d="M4 8l8-4 8 4-8 4-8-4Z"/><path d="M4 8v8l8 4 8-4V8"/><path d="M12 12v8"/>',
    factory: '<path d="M3 21V9l6 4V9l6 4h6v8z"/><path d="M7 17h2"/><path d="M12 17h2"/><path d="M17 17h2"/>',
    cubes: '<path d="M8 3l4 2.2v4.6L8 12 4 9.8V5.2L8 3Z"/><path d="M16 12l4 2.2v4.6L16 21l-4-2.2v-4.6L16 12Z"/><path d="M8 12l4 2.2v4.6L8 21l-4-2.2v-4.6L8 12Z"/>',
    grid: '<path d="M4 4h6v6H4z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6H4z"/><path d="M14 14h6v6h-6z"/>',
    cube: '<path d="M12 3 4 7.5v9L12 21l8-4.5v-9L12 3Z"/><path d="M4 7.5 12 12l8-4.5"/><path d="M12 12v9"/>',
    briefcase: '<path d="M4 7h16v12H4z"/><path d="M9 7V5h6v2"/><path d="M4 12h16"/>',
    cart: '<path d="M3 4h2l2.4 10.5h9.8L20 7H7"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
    chart: '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-3"/>',
    trend: '<path d="M4 16 9 11l4 4 7-8"/><path d="M15 7h5v5"/>',
    history: '<path d="M4 12a8 8 0 1 0 2.3-5.7"/><path d="M4 5v5h5"/><path d="M12 8v5l3 2"/>',
    check: '<path d="M4 5h16v16H4z"/><path d="m8 13 3 3 5-7"/>',
    plane: '<path d="M3 11 21 4l-7 17-3-7-8-3Z"/><path d="m11 14 4-4"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.6-4 4.2-6 8-6s6.4 2 8 6"/>',
    edit: '<path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13 7 4 4"/>',
    default: '<circle cx="12" cy="12" r="8"/><path d="M12 8v8"/><path d="M8 12h8"/>',
  };

  const normalize = (value) => value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, ' ')
    .trim();

  const resolveIcon = (text) => {
    const normalizedText = normalize(text);
    const match = iconMap.find((item) => item.words.some((word) => normalizedText.includes(normalize(word))));
    return match ? match.icon : 'default';
  };

  const createIcon = (name) => {
    const wrapper = document.createElement('span');
    wrapper.className = 'smart-menu-icon';
    wrapper.setAttribute('aria-hidden', 'true');
    wrapper.innerHTML = `<svg viewBox="0 0 24 24" focusable="false">${iconPaths[name] || iconPaths.default}</svg>`;
    return wrapper;
  };

  document.querySelectorAll('.main-sidebar .sidebar-menu > li > a').forEach((link) => {
    if (link.querySelector('.smart-menu-icon')) {
      link.classList.add('smart-menu-has-icon');
      return;
    }

    link.querySelectorAll('img.svg-icon').forEach((icon) => {
      icon.classList.add('smart-menu-original-icon');
      icon.setAttribute('aria-hidden', 'true');
    });

    const label = Array.from(link.childNodes)
      .filter((node) => node.nodeType === Node.TEXT_NODE || (node.nodeType === Node.ELEMENT_NODE && !node.classList.contains('pull-right-container') && !node.classList.contains('pull-left-container') && !node.classList.contains('smart-menu-original-icon')))
      .map((node) => node.textContent || '')
      .join(' ');

    link.insertBefore(createIcon(resolveIcon(label)), link.firstChild);
    link.classList.add('smart-menu-has-icon');
  });
});
