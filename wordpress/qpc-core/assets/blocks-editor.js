/* Editor view for QPC blocks: a labelled placeholder (the real content appears on the live page).
   Title, icon and description come from each block's block.json via WordPress. */
(function (wp) {
  var el = wp.element.createElement;
  var Placeholder = wp.components.Placeholder;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var links = window.qpcAdminLinks || {};

  Object.keys(links).forEach(function (name) {
    if (wp.blocks.getBlockType(name)) return;
    wp.blocks.registerBlockType(name, {
      edit: function () {
        var type = wp.blocks.getBlockType(name) || {};
        var link = links[name];
        return el('div', useBlockProps(),
          el(Placeholder, { icon: type.icon && type.icon.src, label: type.title, instructions: type.description },
            link ? el('a', { href: link, target: '_blank', rel: 'noopener', className: 'components-button is-secondary' }, 'Manage content ↗') : null
          )
        );
      },
      save: function () { return null; }
    });
  });
})(window.wp);
