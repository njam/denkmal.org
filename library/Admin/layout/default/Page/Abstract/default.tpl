{extends file=$render->getLayoutPath('Page/Abstract/default.tpl', 'CM')}

{block name='content'}
  <div class="content-header">
    {menu name="main" class="menu-pills" depth=1}
  </div>
  {block name='content-title'}{/block}
  {block name='content-main'}{/block}
{/block}
