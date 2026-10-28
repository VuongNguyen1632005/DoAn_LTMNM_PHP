for pkg in ['matplotlib', 'PIL', 'graphviz', 'cairosvg']:
    try:
        __import__(pkg)
        print(pkg, 'AVAILABLE')
    except ImportError:
        print(pkg, 'NOT AVAILABLE')
