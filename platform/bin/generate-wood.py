import random, sys

def planks(name, w, h, plank_sizes, vertical, base, var, grain_freq, grain_alpha, seam, seam_alpha, seed, band_alpha=None, band_div=5):
    rnd = random.Random(seed)
    out = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{w}" height="{h}" viewBox="0 0 {w} {h}">', '<defs>']
    length = w if vertical else h
    sizes, pos = [], 0
    while pos < length:
        s = rnd.choice(plank_sizes)
        s = min(s, length - pos)
        sizes.append((pos, s)); pos += s
    for i, _ in enumerate(sizes):
        fx, fy = (grain_freq[1], grain_freq[0]) if vertical else grain_freq
        sd = rnd.randint(1, 999)
        out.append(f'<filter id="g{i}" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency="{fx} {fy}" numOctaves="5" seed="{sd}"/>'
                   f'<feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 {grain_alpha} 0"/></filter>')
        bx, by = (fx, fy / band_div) if not vertical else (fx / band_div, fy)
        ba = grain_alpha * 0.9 if band_alpha is None else band_alpha
        out.append(f'<filter id="b{i}" x="0" y="0" width="100%" height="100%"><feTurbulence type="turbulence" baseFrequency="{bx} {by}" numOctaves="2" seed="{sd+7}"/>'
                   f'<feColorMatrix values="0 0 0 0 1  0 0 0 0 0.92  0 0 0 0 0.8  0 0 0 {ba:.2f} -0.12"/></filter>')
    out.append('</defs>')
    for i, (p, s) in enumerate(sizes):
        t = rnd.uniform(-1, 1); r, g, b = (max(0, min(255, round(c + t * var * c / max(base)))) for c in base)
        x, y, pw, ph = (p, 0, s, h) if vertical else (0, p, w, s)
        out.append(f'<rect x="{x}" y="{y}" width="{pw}" height="{ph}" fill="rgb({r},{g},{b})"/>')
        out.append(f'<rect x="{x}" y="{y}" width="{pw}" height="{ph}" filter="url(#g{i})"/>')
        out.append(f'<rect x="{x}" y="{y}" width="{pw}" height="{ph}" filter="url(#b{i})"/>')
        if vertical:
            out.append(f'<rect x="{x}" y="0" width="2" height="{h}" fill="#000" fill-opacity="{seam_alpha}"/>')
            out.append(f'<rect x="{x+2}" y="0" width="1" height="{h}" fill="#fff" fill-opacity="{seam_alpha/4:.3f}"/>')
        else:
            out.append(f'<rect x="0" y="{y}" width="{w}" height="2" fill="#000" fill-opacity="{seam_alpha}"/>')
            out.append(f'<rect x="0" y="{y+2}" width="{w}" height="1" fill="#fff" fill-opacity="{seam_alpha/4:.3f}"/>')
            # butt joints so horizontal boards look like real cladding
            jx = rnd.randint(int(w*0.25), int(w*0.8))
            out.append(f'<rect x="{jx}" y="{y}" width="2" height="{s}" fill="#000" fill-opacity="{seam_alpha*0.8}"/>')
    out.append('</svg>')
    open(f'{sys.argv[1]}/{name}.svg', 'w').write('\n'.join(out))

d = sys.argv[1]
planks('wood-dark', 1200, 900, [110, 130, 150], True, (40, 31, 24), 0.22, (0.004, 0.12), 0.6, 1, 0.55, 7, band_alpha=0.28, band_div=1.6)
planks('wood-dark-h', 1200, 900, [90, 110, 130], False, (38, 29, 22), 0.22, (0.003, 0.11), 0.6, 1, 0.55, 11, band_alpha=0.28, band_div=1.6)
planks('wood-light', 1200, 900, [120, 140, 160], True, (222, 198, 160), 0.07, (0.004, 0.12), 0.22, 1, 0.22, 3)
