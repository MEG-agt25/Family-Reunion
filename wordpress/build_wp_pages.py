#!/usr/bin/env python3
"""Regenerate WordPress Custom-HTML fragments from website/ pages.
Run from repo root: python3 wordpress/build_wp_pages.py"""
import re, os
SITE, OUT = 'website', 'wordpress'
# WordPress Media Library base — all photos uploaded there in one batch.
# If your uploads land in a different month folder, adjust once here and re-run.
MEDIA = '/wp-content/uploads/2026/07/'
LINKMAP = {
  'index.html': '/', 'history.html': '/history/', 'tree.html': '/family-tree-form/',
  'committees.html': '/committees/', 'superlatives.html': '/superlatives/',
  'gallery.html': '/photos/', 'shop.html': '/dues-and-shirts/', 'give.html': '/give/',
  'business.html': '/family-business/', 'constitution.html': '/constitution-and-bylaws/',
  'hardship.html': '/hardship-fund/', 'features.html': '/features/', 'account.html': '/register/',
  'mildred-tree.html': '/members/interactive-tree/',
}
def wp_fragment(fname):
    src = open(os.path.join(SITE, fname)).read()
    hero = re.search(r'(<div class="hero">.*?</div>)\s*\n', src, re.S)
    main = re.search(r'<main class="wrap[^"]*">(.*?)</main>', src, re.S)
    page_css = re.search(r'<style>(.*?)</style>', src, re.S)
    body = (hero.group(1) if hero else '') + '\n<div class="wrap">' + (main.group(1) if main else '') + '</div>'
    for k, v in LINKMAP.items():
        body = body.replace('href="%s"' % k, 'href="%s"' % v)
    if fname not in ('gallery.html',):
        def formsub(m):
            label = re.search(r'data-hn-form="([^"]+)"', m.group(0))
            name = label.group(1) if label else 'form'
            return ('<div class="card" style="border:2px dashed #cb923f; text-align:center; padding:2rem;">'
                    '<p><strong>[Fluent Forms placeholder]</strong><br>Insert the "%s" form here — '
                    'field list in RUNBOOK-WORDPRESS.md</p></div>' % name)
        body = re.sub(r'<form data-hn-form=.*?</form>', formsub, body, flags=re.S)
    frag = '<div class="hnwp">\n'
    if page_css and fname in ('gallery.html', 'constitution.html'):
        frag += '<style>\n' + page_css.group(1) + '\n</style>\n'
    frag += body + '\n</div>\n'
    if fname == 'gallery.html':
        for s in re.findall(r'<script>(.*?)</script>', src, re.S):
            frag += '<script>\n' + s + '\n</script>\n'
    frag = frag.replace("'photos/2024/'", "'" + MEDIA + "'")
    frag = frag.replace("'photos/family/'", "'" + MEDIA + "'")
    frag = frag.replace('src="photos/2024/', 'src="' + MEDIA).replace('src="photos/family/', 'src="' + MEDIA)
    
    return frag
PAGES = ['index.html','history.html','tree.html','committees.html','superlatives.html',
         'gallery.html','shop.html','give.html','business.html','constitution.html',
         'hardship.html','features.html']
for p in PAGES:
    out = os.path.join(OUT, 'pages', p.replace('.html', '.wp.html'))
    open(out, 'w').write(wp_fragment(p))
    print('wrote', out)
