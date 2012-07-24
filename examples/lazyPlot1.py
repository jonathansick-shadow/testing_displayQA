#!/usr/bin/env python

import sys
import shelve

import matplotlib

import matplotlib.figure as figure
from matplotlib.backends.backend_agg import FigureCanvasAgg as FigCanvas

def plot(data, color='r'):
    x = data['x']
    y = data['y']

    fig = figure.Figure(figsize=(4.0, 3.0))
    canvas = FigCanvas(fig)
    ax = fig.add_subplot(111)
    ax.plot(x, y, color+"-")

    return fig
        
    
if __name__ == '__main__':
    filename, color = sys.argv[1:3]
    
    shelf = shelve.open(filename+".shelve")
    x = shelf['x']
    y = shelf['y']
    shelf.close()
    
    fig = plot({'x' : x, 'y': y}, color)
    
    fig.savefig(filename)

